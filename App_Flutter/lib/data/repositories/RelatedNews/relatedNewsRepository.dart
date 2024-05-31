// ignore_for_file: file_names

import 'package:news/data/repositories/RelatedNews/relatedNewsDataSource.dart';

import 'package:news/data/models/NewsModel.dart';

class RelatedNewsRepository {
  static final RelatedNewsRepository _relatedNewsRepository = RelatedNewsRepository._internal();

  late RelatedNewsRemoteDataSource _relatedNewsRemoteDataSource;

  factory RelatedNewsRepository() {
    _relatedNewsRepository._relatedNewsRemoteDataSource = RelatedNewsRemoteDataSource();
    return _relatedNewsRepository;
  }

  RelatedNewsRepository._internal();

  Future<Map<String, dynamic>> getRelatedNews({
    required String userId,
    required String langId,
    required String offset,
    required String perPage,
    String? catId,
    String? subCatId,
  }) async {
    final result = await _relatedNewsRemoteDataSource.getRelatedNews(userId: userId, langId: langId, catId: catId, subCatId: subCatId, offset: offset, perPage: perPage);

    return {
      "RelatedNews": (result['data'] as List).map((e) => NewsModel.fromJson(e)).toList(),
      "total": result['total'],
    };
  }
}
