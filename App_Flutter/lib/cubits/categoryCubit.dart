// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../data/models/CategoryModel.dart';
import '../data/repositories/Category/categoryRepository.dart';
import '../utils/constant.dart';

abstract class CategoryState {}

class CategoryInitial extends CategoryState {}

class CategoryFetchInProgress extends CategoryState {}

class CategoryFetchSuccess extends CategoryState {
  final List<CategoryModel> category;
  final int totalCategoryCount;
  final bool hasMoreFetchError;
  final bool hasMore;

  CategoryFetchSuccess({
    required this.category,
    required this.totalCategoryCount,
    required this.hasMoreFetchError,
    required this.hasMore,
  });
}

class CategoryFetchFailure extends CategoryState {
  final String errorMessage;

  CategoryFetchFailure(this.errorMessage);
}

class CategoryCubit extends Cubit<CategoryState> {
  final CategoryRepository _categoryRepository;

  CategoryCubit(this._categoryRepository) : super(CategoryInitial());

  void getCategory({required BuildContext context, required String langId}) async {
    try {
      emit(CategoryFetchInProgress());
      int catLimit = 20;
      final result = await _categoryRepository.getCategory(limit: catLimit.toString(), offset: "0", context: context, langId: langId);
      emit(CategoryFetchSuccess(
        category: result['Category'],
        totalCategoryCount: int.parse(result['total']),
        hasMoreFetchError: false,
        hasMore: (result['Category'] as List<CategoryModel>).length < int.parse(result['total']),
      ));
    } catch (e) {
      emit(CategoryFetchFailure(e.toString()));
    }
  }

  bool hasMoreCategory() {
    if (state is CategoryFetchSuccess) {
      return (state as CategoryFetchSuccess).hasMore;
    }
    return false;
  }

  void getMoreCategory({required BuildContext context, required String langId}) async {
    if (state is CategoryFetchSuccess) {
      try {
        final result = await _categoryRepository.getCategory(context: context, limit: limitOfAPIData.toString(), langId: langId, offset: (state as CategoryFetchSuccess).category.length.toString());
        List<CategoryModel> updatedResults = (state as CategoryFetchSuccess).category;
        updatedResults.addAll(result['Category'] as List<CategoryModel>);
        emit(CategoryFetchSuccess(
          category: updatedResults,
          totalCategoryCount: int.parse(result['total']),
          hasMoreFetchError: false,
          hasMore: updatedResults.length < int.parse(result['total']),
        ));
      } catch (e) {
        //in case of any error
        emit(CategoryFetchSuccess(
          category: (state as CategoryFetchSuccess).category,
          hasMoreFetchError: true,
          totalCategoryCount: (state as CategoryFetchSuccess).totalCategoryCount,
          hasMore: (state as CategoryFetchSuccess).hasMore,
        ));
      }
    }
  }

  List<CategoryModel> getCatList() {
    if (state is CategoryFetchSuccess) {
      return (state as CategoryFetchSuccess).category;
    }
    return [];
  }

  int getCategoryIndex({required String categoryName}) {
    if (state is CategoryFetchSuccess) {
      return (state as CategoryFetchSuccess).category.indexWhere((element) => element.categoryName == categoryName);
    }
    return 0;
  }
}
