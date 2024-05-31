// ignore_for_file: file_names

import 'package:news/data/models/NewsModel.dart';
import 'getUserNewsRemoteDataSource.dart';

class GetUserNewsRepository {
  static final GetUserNewsRepository _getUserNewsRepository = GetUserNewsRepository._internal();

  late GetUserNewsRemoteDataSource _getUserNewsRemoteDataSource;

  factory GetUserNewsRepository() {
    _getUserNewsRepository._getUserNewsRemoteDataSource = GetUserNewsRemoteDataSource();
    return _getUserNewsRepository;
  }

  GetUserNewsRepository._internal();

  Future<Map<String, dynamic>> getGetUserNews({required String offset, required String limit, required String userId, required String langId}) async {
    final result = await _getUserNewsRemoteDataSource.getGetUserNews(
      limit: limit,
      offset: offset,
      langId: langId,
      userId: userId,
    );

    return {
      "total": result['total'],
      "GetUserNews": (result['data'] as List).map((e) => NewsModel.fromJson(e)).toList(),
    };
  }
}
