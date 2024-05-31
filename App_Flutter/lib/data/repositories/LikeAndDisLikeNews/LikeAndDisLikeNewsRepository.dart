// ignore_for_file: file_names, non_constant_identifier_names

import 'package:flutter/material.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/data/repositories/LikeAndDisLikeNews/LikeAndDisLikeNewsDataSource.dart';

class LikeAndDisLikeRepository {
  static final LikeAndDisLikeRepository _LikeAndDisLikeRepository = LikeAndDisLikeRepository._internal();
  late LikeAndDisLikeRemoteDataSource _LikeAndDisLikeRemoteDataSource;

  factory LikeAndDisLikeRepository() {
    _LikeAndDisLikeRepository._LikeAndDisLikeRemoteDataSource = LikeAndDisLikeRemoteDataSource();
    return _LikeAndDisLikeRepository;
  }

  LikeAndDisLikeRepository._internal();

  Future<Map<String, dynamic>> getLikeAndDisLike({required BuildContext context, required String offset, required String limit, required String userId, required String langId}) async {
    final result = await _LikeAndDisLikeRemoteDataSource.getLikeAndDisLike(perPage: limit, offset: offset, context: context, userId: userId, langId: langId);

    return {
      "total": result['total'],
      "LikeAndDisLike": (result['data'] as List).map((e) => NewsModel.fromJson(e)).toList(),
    };
  }

  Future setLikeAndDisLike({required String userId, required String newsId, required BuildContext context, required String status}) async {
    final result = await _LikeAndDisLikeRemoteDataSource.addAndRemoveLikeAndDisLike(userId: userId, context: context, status: status, newsId: newsId);
    return result;
  }
}
