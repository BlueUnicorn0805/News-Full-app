// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/data/repositories/TagNews/tagNewsDataSource.dart';

class TagNewsRepository {
  static final TagNewsRepository _tagNewsRepository = TagNewsRepository._internal();

  late TagNewsRemoteDataSource _tagNewsRemoteDataSource;

  factory TagNewsRepository() {
    _tagNewsRepository._tagNewsRemoteDataSource = TagNewsRemoteDataSource();
    return _tagNewsRepository;
  }

  TagNewsRepository._internal();

  Future<Map<String, dynamic>> getTagNews({required BuildContext context, required String tagId, required String userId, required String langId}) async {
    final result = await _tagNewsRemoteDataSource.getTagNews(context: context, userId: userId, tagId: tagId, langId: langId);

    return {
      "TagNews": (result['data'] as List).map((e) => NewsModel.fromJson(e)).toList(),
    };
  }
}
