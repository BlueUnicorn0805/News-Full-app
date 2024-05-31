// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/models/NewsModel.dart';

import '../data/repositories/SubCatNews/subCatRepository.dart';
import '../utils/constant.dart';

abstract class SubCatNewsState {}

class SubCatNewsInitial extends SubCatNewsState {}

class SubCatNewsFetchInProgress extends SubCatNewsState {}

class SubCatNewsFetchSuccess extends SubCatNewsState {
  final List<NewsModel> subCatNews;
  final int totalSubCatNewsCount;
  final bool hasMoreFetchError;
  final bool hasMore;
  final bool isFirst;

  SubCatNewsFetchSuccess({required this.subCatNews, required this.totalSubCatNewsCount, required this.hasMoreFetchError, required this.hasMore, required this.isFirst});
}

class SubCatNewsFetchFailure extends SubCatNewsState {
  final String errorMessage;

  SubCatNewsFetchFailure(this.errorMessage);
}

class SubCatNewsCubit extends Cubit<SubCatNewsState> {
  final SubCatNewsRepository _subCatNewsRepository;

  SubCatNewsCubit(this._subCatNewsRepository) : super(SubCatNewsInitial());

  void getSubCatNews({required BuildContext context, String? catId, String? subCatId, required String userId, required String langId}) async {
    try {
      emit(SubCatNewsFetchInProgress());
      final result = await _subCatNewsRepository.getSubCatNews(limit: limitOfAPIData.toString(), offset: "0", context: context, subCatId: subCatId, userId: userId, langId: langId, catId: catId);
      emit(SubCatNewsFetchSuccess(
          subCatNews: result['SubCatNews'],
          totalSubCatNewsCount: int.parse(result['total']),
          hasMoreFetchError: false,
          hasMore: (result['SubCatNews'] as List<NewsModel>).length < int.parse(result['total']),
          isFirst: true));
    } catch (e) {
      emit(SubCatNewsFetchFailure(e.toString()));
    }
  }

  bool hasMoreSubCatNews() {
    if (state is SubCatNewsFetchSuccess) {
      return (state as SubCatNewsFetchSuccess).hasMore;
    }
    return false;
  }

  void getMoreSubCatNews({required BuildContext context, String? catId, String? subCatId, required String userId, required String langId}) async {
    if (state is SubCatNewsFetchSuccess) {
      try {
        final result = await _subCatNewsRepository.getSubCatNews(
            context: context,
            limit: limitOfAPIData.toString(),
            offset: (state as SubCatNewsFetchSuccess).subCatNews.length.toString(),
            langId: langId,
            userId: userId,
            catId: catId,
            subCatId: subCatId);
        List<NewsModel> updatedResults = (state as SubCatNewsFetchSuccess).subCatNews;
        updatedResults.addAll(result['SubCatNews'] as List<NewsModel>);
        emit(SubCatNewsFetchSuccess(
            subCatNews: updatedResults, totalSubCatNewsCount: int.parse(result['total']), hasMoreFetchError: false, hasMore: updatedResults.length < int.parse(result['total']), isFirst: false));
      } catch (e) {
        //in case of any error
        emit(SubCatNewsFetchSuccess(
            subCatNews: (state as SubCatNewsFetchSuccess).subCatNews,
            hasMoreFetchError: true,
            totalSubCatNewsCount: (state as SubCatNewsFetchSuccess).totalSubCatNewsCount,
            hasMore: (state as SubCatNewsFetchSuccess).hasMore,
            isFirst: false));
      }
    }
  }
}
