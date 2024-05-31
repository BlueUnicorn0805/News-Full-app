// ignore_for_file: file_names

import 'package:news/data/models/NewsModel.dart';

import 'NewsByIdRemoteDataSource.dart';

class NewsByIdRepository {
  static final NewsByIdRepository _newsByIdRepository = NewsByIdRepository._internal();

  late NewsByIdRemoteDataSource _newsByIdRemoteDataSource;

  factory NewsByIdRepository() {
    _newsByIdRepository._newsByIdRemoteDataSource = NewsByIdRemoteDataSource();
    return _newsByIdRepository;
  }

  NewsByIdRepository._internal();

  Future<Map<String, dynamic>> getNewsById({required String newsId, required String langId, required String userId}) async {
    final result = await _newsByIdRemoteDataSource.getNewsById(newsId: newsId, langId: langId, userId: userId);

    return {
      "NewsById": (result['data'] as List).map((e) => NewsModel.fromJson(e)).toList(),
    };
  }
}
