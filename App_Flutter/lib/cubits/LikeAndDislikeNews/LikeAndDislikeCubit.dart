// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/models/NewsModel.dart';

import 'package:news/data/repositories/LikeAndDisLikeNews/LikeAndDisLikeNewsRepository.dart';

abstract class LikeAndDisLikeState {}

class LikeAndDisLikeInitial extends LikeAndDisLikeState {}

class LikeAndDisLikeFetchInProgress extends LikeAndDisLikeState {}

class LikeAndDisLikeFetchSuccess extends LikeAndDisLikeState {
  final List<NewsModel> likeAndDisLike;
  final int totalLikeAndDisLikeCount;
  final bool hasMoreFetchError;
  final bool hasMore;

  LikeAndDisLikeFetchSuccess({
    required this.likeAndDisLike,
    required this.totalLikeAndDisLikeCount,
    required this.hasMoreFetchError,
    required this.hasMore,
  });
}

class LikeAndDisLikeFetchFailure extends LikeAndDisLikeState {
  final String errorMessage;

  LikeAndDisLikeFetchFailure(this.errorMessage);
}

class LikeAndDisLikeCubit extends Cubit<LikeAndDisLikeState> {
  final LikeAndDisLikeRepository likeAndDisLikeRepository;
  int perPageLimit = 25;

  LikeAndDisLikeCubit(this.likeAndDisLikeRepository) : super(LikeAndDisLikeInitial());

  void getLikeAndDisLike({required BuildContext context, required String userId, required String langId}) async {
    try {
      emit(LikeAndDisLikeFetchInProgress());
      final result = await likeAndDisLikeRepository.getLikeAndDisLike(limit: perPageLimit.toString(), offset: "0", context: context, userId: userId, langId: langId);
      emit(LikeAndDisLikeFetchSuccess(
        likeAndDisLike: result['LikeAndDisLike'],
        totalLikeAndDisLikeCount: int.parse(result['total']),
        hasMoreFetchError: false,
        hasMore: (result['LikeAndDisLike'] as List<NewsModel>).length < int.parse(result['total']),
      ));
    } catch (e) {
      emit(LikeAndDisLikeFetchFailure(e.toString()));
    }
  }

  bool hasMoreLikeAndDisLike() {
    if (state is LikeAndDisLikeFetchSuccess) {
      return (state as LikeAndDisLikeFetchSuccess).hasMore;
    }
    return false;
  }

  void getMoreLikeAndDisLike({required BuildContext context, required String userId, required String langId}) async {
    if (state is LikeAndDisLikeFetchSuccess) {
      try {
        final result = await likeAndDisLikeRepository.getLikeAndDisLike(
            context: context, limit: perPageLimit.toString(), userId: userId, offset: (state as LikeAndDisLikeFetchSuccess).likeAndDisLike.length.toString(), langId: langId);
        List<NewsModel> updatedResults = (state as LikeAndDisLikeFetchSuccess).likeAndDisLike;
        updatedResults.addAll(result['LikeAndDisLike'] as List<NewsModel>);
        emit(LikeAndDisLikeFetchSuccess(
          likeAndDisLike: updatedResults,
          totalLikeAndDisLikeCount: int.parse(result['total']),
          hasMoreFetchError: false,
          hasMore: updatedResults.length < int.parse(result['total']),
        ));
      } catch (e) {
        //in case of any error
        emit(LikeAndDisLikeFetchSuccess(
          likeAndDisLike: (state as LikeAndDisLikeFetchSuccess).likeAndDisLike,
          hasMoreFetchError: true,
          totalLikeAndDisLikeCount: (state as LikeAndDisLikeFetchSuccess).totalLikeAndDisLikeCount,
          hasMore: (state as LikeAndDisLikeFetchSuccess).hasMore,
        ));
      }
    }
  }

  bool isNewsLikeAndDisLike(String newsId) {
    if (state is LikeAndDisLikeFetchSuccess) {
      final likeAndDisLike = (state as LikeAndDisLikeFetchSuccess).likeAndDisLike;

      return likeAndDisLike.indexWhere((element) => (element.id == newsId || element.newsId == newsId)) != -1;
    }
    return false;
  }

  void resetState() {
    emit(LikeAndDisLikeFetchInProgress());
  }
}
