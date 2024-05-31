// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/data/repositories/Bookmark/bookmarkRepository.dart';

abstract class BookmarkState {}

class BookmarkInitial extends BookmarkState {}

class BookmarkFetchInProgress extends BookmarkState {}

class BookmarkFetchSuccess extends BookmarkState {
  final List<NewsModel> bookmark;
  final int totalBookmarkCount;
  final bool hasMoreFetchError;
  final bool hasMore;

  BookmarkFetchSuccess({
    required this.bookmark,
    required this.totalBookmarkCount,
    required this.hasMoreFetchError,
    required this.hasMore,
  });
}

class BookmarkFetchFailure extends BookmarkState {
  final String errorMessage;

  BookmarkFetchFailure(this.errorMessage);
}

class BookmarkCubit extends Cubit<BookmarkState> {
  final BookmarkRepository bookmarkRepository;
  int perPageLimit = 25;

  BookmarkCubit(this.bookmarkRepository) : super(BookmarkInitial());

  void getBookmark({required BuildContext context, required String userId, required String langId}) async {
    try {
      emit(BookmarkFetchInProgress());
      final result = await bookmarkRepository.getBookmark(limit: perPageLimit.toString(), offset: "0", context: context, userId: userId, langId: langId);

      emit(BookmarkFetchSuccess(
        bookmark: result['Bookmark'],
        totalBookmarkCount: int.parse(result['total']),
        hasMoreFetchError: false,
        hasMore: (result['Bookmark'] as List<NewsModel>).length < int.parse(result['total']),
      ));
    } catch (e) {
      if (e.toString() == "No Data Found") {
        //incase of 0 Bookmark length - make it success for fresh users
        emit(BookmarkFetchSuccess(bookmark: [], totalBookmarkCount: 0, hasMoreFetchError: false, hasMore: false));
      } else {
        emit(BookmarkFetchFailure(e.toString()));
      }
    }
  }

  bool hasMoreBookmark() {
    if (state is BookmarkFetchSuccess) {
      return (state as BookmarkFetchSuccess).hasMore;
    }
    return false;
  }

  void getMoreBookmark({required BuildContext context, required String userId, required String langId}) async {
    if (state is BookmarkFetchSuccess) {
      try {
        final result =
            await bookmarkRepository.getBookmark(context: context, limit: perPageLimit.toString(), userId: userId, offset: (state as BookmarkFetchSuccess).bookmark.length.toString(), langId: langId);
        List<NewsModel> updatedResults = (state as BookmarkFetchSuccess).bookmark;
        updatedResults.addAll(result['Bookmark'] as List<NewsModel>);
        emit(BookmarkFetchSuccess(
          bookmark: updatedResults,
          totalBookmarkCount: int.parse(result['total']),
          hasMoreFetchError: false,
          hasMore: updatedResults.length < int.parse(result['total']),
        ));
      } catch (e) {
        //in case of any error
        emit(BookmarkFetchSuccess(
          bookmark: (state as BookmarkFetchSuccess).bookmark,
          hasMoreFetchError: (e.toString() == "No Data Found") ? false : true,
          totalBookmarkCount: (state as BookmarkFetchSuccess).totalBookmarkCount,
          hasMore: (state as BookmarkFetchSuccess).hasMore,
        ));
      }
    }
  }

  void addBookmarkNews(NewsModel model) {
    if (state is BookmarkFetchSuccess) {
      List<NewsModel> bookmarklist = [];
      bookmarklist.insert(0, model);
      bookmarklist.addAll((state as BookmarkFetchSuccess).bookmark);

      emit(BookmarkFetchSuccess(
        bookmark: List.from(bookmarklist),
        hasMoreFetchError: true,
        totalBookmarkCount: (state as BookmarkFetchSuccess).totalBookmarkCount,
        hasMore: (state as BookmarkFetchSuccess).hasMore,
      ));
    }
  }

  void removeBookmarkNews(NewsModel model) {
    if (state is BookmarkFetchSuccess) {
      final bookmark = (state as BookmarkFetchSuccess).bookmark;
      bookmark.removeWhere(((element) => (element.id == model.id || element.newsId == model.id)));
      emit(BookmarkFetchSuccess(
        bookmark: List.from(bookmark),
        hasMoreFetchError: true,
        totalBookmarkCount: (state as BookmarkFetchSuccess).totalBookmarkCount,
        hasMore: (state as BookmarkFetchSuccess).hasMore,
      ));
    }
  }

  bool isNewsBookmark(String newsId) {
    if (state is BookmarkFetchSuccess) {
      final bookmark = (state as BookmarkFetchSuccess).bookmark;
      if (bookmark.isNotEmpty) {
        return (bookmark.indexWhere((element) => (element.newsId == newsId || element.id == newsId)) != -1);
      } else {
        return false;
      }
    }
    return false;
  }

  void resetState() {
    emit(BookmarkFetchInProgress());
  }
}
