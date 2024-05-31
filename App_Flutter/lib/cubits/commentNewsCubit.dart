// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/models/CommentModel.dart';
import 'package:news/data/repositories/CommentNews/commNewsRepository.dart';
import 'package:news/utils/constant.dart';

abstract class CommentNewsState {}

class CommentNewsInitial extends CommentNewsState {}

class CommentNewsFetchInProgress extends CommentNewsState {}

class CommentNewsFetchSuccess extends CommentNewsState {
  final List<CommentModel> commentNews;
  final int totalCommentNewsCount;
  final bool hasMoreFetchError;
  final bool hasMore;

  CommentNewsFetchSuccess({
    required this.commentNews,
    required this.totalCommentNewsCount,
    required this.hasMoreFetchError,
    required this.hasMore,
  });
}

class CommentNewsFetchFailure extends CommentNewsState {
  final String errorMessage;

  CommentNewsFetchFailure(this.errorMessage);
}

class CommentNewsCubit extends Cubit<CommentNewsState> {
  final CommentNewsRepository _commentNewsRepository;

  CommentNewsCubit(this._commentNewsRepository) : super(CommentNewsInitial());

  void getCommentNews({
    required BuildContext context,
    required String userId,
    required String newsId,
  }) async {
    try {
      emit(CommentNewsFetchInProgress());
      final result = await _commentNewsRepository.getCommentNews(limit: limitOfAPIData.toString(), offset: "0", context: context, userId: userId, newsId: newsId);
      emit(CommentNewsFetchSuccess(
          commentNews: result['CommentNews'],
          totalCommentNewsCount: int.parse(result['total']),
          hasMoreFetchError: false,
          hasMore: ((result['CommentNews'] as List<CommentModel>).length) < int.parse(result['total'])));
    } catch (e) {
      emit(CommentNewsFetchFailure(e.toString()));
    }
  }

  bool hasMoreCommentNews() {
    if (state is CommentNewsFetchSuccess) {
      return (state as CommentNewsFetchSuccess).hasMore;
    }
    return false;
  }

  void getMoreCommentNews({
    required BuildContext context,
    required String userId,
    required String newsId,
  }) async {
    if (state is CommentNewsFetchSuccess) {
      try {
        final result = await _commentNewsRepository.getCommentNews(
            context: context, limit: limitOfAPIData.toString(), newsId: newsId, userId: userId, offset: (state as CommentNewsFetchSuccess).commentNews.length.toString());
        List<CommentModel> updatedResults = (state as CommentNewsFetchSuccess).commentNews;
        updatedResults.addAll(result['CommentNews'] as List<CommentModel>);
        emit(CommentNewsFetchSuccess(
          commentNews: updatedResults,
          totalCommentNewsCount: int.parse(result['total']),
          hasMoreFetchError: false,
          hasMore: updatedResults.length < int.parse(result['total']),
        ));
      } catch (e) {
        //in case of any error
        emit(CommentNewsFetchSuccess(
          commentNews: (state as CommentNewsFetchSuccess).commentNews,
          hasMoreFetchError: true,
          totalCommentNewsCount: (state as CommentNewsFetchSuccess).totalCommentNewsCount,
          hasMore: (state as CommentNewsFetchSuccess).hasMore,
        ));
      }
    }
  }

  void commentUpdateList(List<CommentModel> commentList, int total) {
    if (state is CommentNewsFetchSuccess || state is CommentNewsFetchFailure) {
      bool haseMore = (state is CommentNewsFetchSuccess) ? (state as CommentNewsFetchSuccess).hasMore : false;
      emit(CommentNewsFetchSuccess(commentNews: commentList, hasMore: haseMore, hasMoreFetchError: false, totalCommentNewsCount: total));
    }
  }

  void deleteComment(int index) {
    if (state is CommentNewsFetchSuccess) {
      List<CommentModel> commentList = List.from((state as CommentNewsFetchSuccess).commentNews)..removeAt(index);

      emit(CommentNewsFetchSuccess(
          commentNews: commentList,
          hasMore: (state as CommentNewsFetchSuccess).hasMore,
          hasMoreFetchError: false,
          totalCommentNewsCount: (state as CommentNewsFetchSuccess).totalCommentNewsCount - 1));
    }
  }

  void deleteCommentReply(int index) {
    if (state is CommentNewsFetchSuccess) {
      List<CommentModel> commentList = (state as CommentNewsFetchSuccess).commentNews;
      commentList[index].replyComList!.removeAt(index);
      emit(CommentNewsFetchSuccess(
          commentNews: commentList, hasMore: (state as CommentNewsFetchSuccess).hasMore, hasMoreFetchError: false, totalCommentNewsCount: (state as CommentNewsFetchSuccess).totalCommentNewsCount));
    }
  }
}
