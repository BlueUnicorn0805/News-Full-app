// ignore_for_file: file_names

import 'package:news/data/repositories/NewsComment/SetComment/setComRemoteDataSource.dart';
import 'package:news/data/models/CommentModel.dart';

class SetCommentRepository {
  static final SetCommentRepository _setCommentRepository = SetCommentRepository._internal();

  late SetCommentRemoteDataSource _setCommentRemoteDataSource;

  factory SetCommentRepository() {
    _setCommentRepository._setCommentRemoteDataSource = SetCommentRemoteDataSource();
    return _setCommentRepository;
  }
  SetCommentRepository._internal();
  Future<Map<String, dynamic>> setComment({required String userId, required String parentId, required String newsId, required String langId, required String message}) async {
    final result = await _setCommentRemoteDataSource.setComment(userId: userId, parentId: parentId, newsId: newsId, langId: langId, message: message);
    return {"SetComment": (result['data'] as List).map((e) => CommentModel.fromJson(e)).toList(), "total": result['total']};
  }
}
