// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/data/models/NewsModel.dart';

import 'bookmarkRemoteDataSource.dart';

class BookmarkRepository {
  static final BookmarkRepository _bookmarkRepository = BookmarkRepository._internal();
  late BookmarkRemoteDataSource _bookmarkRemoteDataSource;

  factory BookmarkRepository() {
    _bookmarkRepository._bookmarkRemoteDataSource = BookmarkRemoteDataSource();
    return _bookmarkRepository;
  }

  BookmarkRepository._internal();

  Future<Map<String, dynamic>> getBookmark({required BuildContext context, required String offset, required String limit, required String userId, required String langId}) async {
    final result = await _bookmarkRemoteDataSource.getBookmark(perPage: limit, offset: offset, context: context, userId: userId, langId: langId);

    return {
      "total": result['total'],
      "Bookmark": (result['data'] as List).map((e) => NewsModel.fromJson(e)).toList(),
    };
  }

  Future setBookmark({required String userId, required String newsId, required BuildContext context, required String status}) async {
    final result = await _bookmarkRemoteDataSource.addAndRemoveBookmark(userId: userId, context: context, status: status, newsId: newsId);
    return result;
  }
}
