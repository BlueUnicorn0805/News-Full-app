// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/data/repositories/SubCatNews/subCatNewsRemoteDataSource.dart';

class SubCatNewsRepository {
  static final SubCatNewsRepository _subCatNewsRepository = SubCatNewsRepository._internal();

  late SubCatNewsRemoteDataSource _subCatNewsRemoteDataSource;

  factory SubCatNewsRepository() {
    _subCatNewsRepository._subCatNewsRemoteDataSource = SubCatNewsRemoteDataSource();
    return _subCatNewsRepository;
  }

  SubCatNewsRepository._internal();

  Future<Map<String, dynamic>> getSubCatNews(
      {required BuildContext context, required String offset, required String limit, String? catId, String? subCatId, required String userId, required String langId}) async {
    final result = await _subCatNewsRemoteDataSource.getSubCatNews(limit: limit, offset: offset, context: context, langId: langId, userId: userId, subCatId: subCatId, catId: catId);

    return {
      "total": result['total'],
      "SubCatNews": (result['data'] as List).map((e) => NewsModel.fromJson(e)).toList(),
    };
  }
}
