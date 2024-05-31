// ignore_for_file: file_names, use_build_context_synchronously

import 'package:flutter/cupertino.dart';
import 'package:news/data/repositories/SubCategory/subCatRemoteDataSource.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/data/models/CategoryModel.dart';

class SubCategoryRepository {
  static final SubCategoryRepository _subCategoryRepository = SubCategoryRepository._internal();

  late SubCategoryRemoteDataSource _subCategoryRemoteDataSource;

  factory SubCategoryRepository() {
    _subCategoryRepository._subCategoryRemoteDataSource = SubCategoryRemoteDataSource();
    return _subCategoryRepository;
  }

  SubCategoryRepository._internal();

  Future<Map<String, dynamic>> getSubCategory({required BuildContext context, required String catId, required String langId}) async {
    final result = await _subCategoryRemoteDataSource.getSubCategory(context: context, langId: langId, catId: catId);

    List<SubCategoryModel> subCatList = [];

    subCatList.insert(0, SubCategoryModel(id: "0", subCatName: UiUtils.getTranslatedLabel(context, 'allLbl')));

    subCatList.addAll((result['data'] as List).map((e) => SubCategoryModel.fromJson(e)).toList());
    return {
      "SubCategory": subCatList,
    };
  }
}
