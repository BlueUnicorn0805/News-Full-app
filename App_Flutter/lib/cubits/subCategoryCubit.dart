// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import 'package:news/data/models/CategoryModel.dart';
import 'package:news/data/repositories/SubCategory/subCatRepository.dart';

abstract class SubCategoryState {}

class SubCategoryInitial extends SubCategoryState {}

class SubCategoryFetchInProgress extends SubCategoryState {}

class SubCategoryFetchSuccess extends SubCategoryState {
  final List<SubCategoryModel> subCategory;

  SubCategoryFetchSuccess({
    required this.subCategory,
  });
}

class SubCategoryFetchFailure extends SubCategoryState {
  final String errorMessage;

  SubCategoryFetchFailure(this.errorMessage);
}

class SubCategoryCubit extends Cubit<SubCategoryState> {
  final SubCategoryRepository _subCategoryRepository;

  SubCategoryCubit(this._subCategoryRepository) : super(SubCategoryInitial());

  void getSubCategory({required BuildContext context, required String catId, required String langId}) async {
    try {
      emit(SubCategoryFetchInProgress());
      final result = await _subCategoryRepository.getSubCategory(context: context, catId: catId, langId: langId);

      emit(SubCategoryFetchSuccess(subCategory: result['SubCategory']));
    } catch (e) {
      emit(SubCategoryFetchFailure(e.toString()));
    }
  }
}
