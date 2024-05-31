// ignore_for_file: file_names

import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/models/NewsModel.dart';
import '../data/repositories/RelatedNews/relatedNewsRepository.dart';
import '../utils/constant.dart';

abstract class RelatedNewsState {}

class RelatedNewsInitial extends RelatedNewsState {}

class RelatedNewsFetchInProgress extends RelatedNewsState {}

class RelatedNewsFetchSuccess extends RelatedNewsState {
  final List<NewsModel> relatedNews;
  final int totalRelatedNewsCount;
  final bool hasMoreFetchError;
  final bool hasMore;

  RelatedNewsFetchSuccess({
    required this.relatedNews,
    required this.totalRelatedNewsCount,
    required this.hasMoreFetchError,
    required this.hasMore,
  });
}

class RelatedNewsFetchFailure extends RelatedNewsState {
  final String errorMessage;

  RelatedNewsFetchFailure(this.errorMessage);
}

class RelatedNewsCubit extends Cubit<RelatedNewsState> {
  final RelatedNewsRepository _relatedNewsRepository;

  RelatedNewsCubit(this._relatedNewsRepository) : super(RelatedNewsInitial());

  void getRelatedNews({required String userId, required String langId, String? catId, String? subCatId}) async {
    try {
      emit(RelatedNewsFetchInProgress());
      final result = await _relatedNewsRepository.getRelatedNews(perPage: limitOfAPIData.toString(), offset: "0", langId: langId, userId: userId, catId: catId, subCatId: subCatId);
      emit(RelatedNewsFetchSuccess(
        relatedNews: result['RelatedNews'],
        totalRelatedNewsCount: int.parse(result['total']),
        hasMoreFetchError: false,
        hasMore: (result['RelatedNews'] as List<NewsModel>).length < int.parse(result['total']),
      ));
    } catch (e) {
      emit(RelatedNewsFetchFailure(e.toString()));
    }
  }

  bool hasMoreRelatedNews() {
    if (state is RelatedNewsFetchSuccess) {
      return (state as RelatedNewsFetchSuccess).hasMore;
    }
    return false;
  }

  void getMoreRelatedNews({required String userId, required String langId, String? catId, String? subCatId}) async {
    if (state is RelatedNewsFetchSuccess) {
      try {
        final result = await _relatedNewsRepository.getRelatedNews(
            perPage: limitOfAPIData.toString(), offset: (state as RelatedNewsFetchSuccess).relatedNews.length.toString(), langId: langId, userId: userId, subCatId: subCatId, catId: catId);
        List<NewsModel> updatedResults = (state as RelatedNewsFetchSuccess).relatedNews;
        updatedResults.addAll(result['RelatedNews'] as List<NewsModel>);
        emit(RelatedNewsFetchSuccess(
          relatedNews: updatedResults,
          totalRelatedNewsCount: int.parse(result['total']),
          hasMoreFetchError: false,
          hasMore: updatedResults.length < int.parse(result['total']),
        ));
      } catch (e) {
        //in case of any error
        emit(RelatedNewsFetchSuccess(
          relatedNews: (state as RelatedNewsFetchSuccess).relatedNews,
          hasMoreFetchError: true,
          totalRelatedNewsCount: (state as RelatedNewsFetchSuccess).totalRelatedNewsCount,
          hasMore: (state as RelatedNewsFetchSuccess).hasMore,
        ));
      }
    }
  }
}
