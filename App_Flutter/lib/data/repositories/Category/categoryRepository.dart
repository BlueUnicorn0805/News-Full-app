// ignore_for_file: file_names

import 'package:flutter/material.dart';

import 'package:news/data/models/CategoryModel.dart';
import 'categoryRemoteDataSource.dart';

class CategoryRepository {
  static final CategoryRepository _notificationRepository = CategoryRepository._internal();

  late CategoryRemoteDataSource _notificationRemoteDataSource;

  factory CategoryRepository() {
    _notificationRepository._notificationRemoteDataSource = CategoryRemoteDataSource();
    return _notificationRepository;
  }

  CategoryRepository._internal();

  Future<Map<String, dynamic>> getCategory({required BuildContext context, required String offset, required String limit, required String langId}) async {
    final result = await _notificationRemoteDataSource.getCategory(limit: limit, offset: offset, context: context, langId: langId);

    return {
      "total": result['total'],
      "Category": (result['data'] as List).map((e) => CategoryModel.fromJson(e)).toList(),
    };
  }
}
