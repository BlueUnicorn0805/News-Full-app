// ignore_for_file: file_names

import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/models/BreakingNewsModel.dart';
import 'package:news/data/models/NewsModel.dart';
import '../data/repositories/SectionById/sectionByIdRepository.dart';

abstract class SectionByIdState {}

class SectionByIdInitial extends SectionByIdState {}

class SectionByIdFetchInProgress extends SectionByIdState {}

class SectionByIdFetchSuccess extends SectionByIdState {
  final List<NewsModel> newsModel;
  final List<BreakingNewsModel> breakNewsModel;
  final int totalCount;
  final bool hasMoreFetchError;
  final bool hasMore;
  final String type;

  SectionByIdFetchSuccess({
    required this.newsModel,
    required this.breakNewsModel,
    required this.totalCount,
    required this.hasMoreFetchError,
    required this.hasMore,
    required this.type,
  });
}

class SectionByIdFetchFailure extends SectionByIdState {
  final String errorMessage;

  SectionByIdFetchFailure(this.errorMessage);
}

class SectionByIdCubit extends Cubit<SectionByIdState> {
  final SectionByIdRepository _sectionByIdRepository;

  SectionByIdCubit(this._sectionByIdRepository) : super(SectionByIdInitial());

  void getSectionById({required String userId, required String langId, required String sectionId}) async {
    try {
      emit(SectionByIdFetchInProgress());
      final result = await _sectionByIdRepository.getSectionById(userId: userId, langId: langId, sectionId: sectionId);

      emit(SectionByIdFetchSuccess(
          newsModel: (result[0].newsType == "news" || result[0].newsType == "user_choice")
              ? result[0].news!
              : result[0].videosType == "news"
                  ? result[0].videos!
                  : [],
          breakNewsModel: result[0].newsType == "breaking_news"
              ? result[0].breakNews!
              : result[0].videosType == "breaking_news"
                  ? result[0].breakVideos!
                  : [],
          totalCount: (result[0].newsType == "news" || result[0].newsType == "user_choice")
              ? result[0].newsTotal!
              : result[0].newsType == "breaking_news"
                  ? result[0].breakNewsTotal!
                  : result[0].videosTotal!,
          hasMoreFetchError: false,
          hasMore: (result[0].newsType == "news" || result[0].newsType == "user_choice")
              ? (result[0].news as List<NewsModel>).length < result[0].newsTotal!
              : (result[0].videosType == "news" || result[0].newsType == "user_choice")
                  ? (result[0].videos as List<NewsModel>).length < result[0].videosTotal!
                  : result[0].newsType == "breaking_news"
                      ? (result[0].breakNews as List<BreakingNewsModel>).length < result[0].breakNewsTotal!
                      : (result[0].breakVideos as List<BreakingNewsModel>).length < result[0].videosTotal!,
          type: result[0].newsType!));
    } catch (e) {
      emit(SectionByIdFetchFailure(e.toString()));
    }
  }

  bool hasMoreSectionById() {
    if (state is SectionByIdFetchSuccess) {
      return (state as SectionByIdFetchSuccess).hasMore;
    }
    return false;
  }

  void getMoreSectionById({required String userId, required String langId, required sectionId}) async {
    if (state is SectionByIdFetchSuccess) {
      try {
        final result = await _sectionByIdRepository.getSectionById(
          langId: langId,
          userId: userId,
          sectionId: sectionId,
        );

        if ((state as SectionByIdFetchSuccess).newsModel.isNotEmpty) {
          List<NewsModel> updatedResults = (state as SectionByIdFetchSuccess).newsModel;
          updatedResults.addAll(((result[0].newsType == "news" || result[0].newsType == "user_choice") ? result[0].news : result[0].videos) as List<NewsModel>);
          emit(SectionByIdFetchSuccess(
              newsModel: updatedResults,
              totalCount: (result[0].newsType == "news" || result[0].newsType == "user_choice") ? result[0].newsTotal! : result[0].videosTotal!,
              hasMoreFetchError: false,
              hasMore: updatedResults.length < ((result[0].newsType == "news" || result[0].newsType == "user_choice") ? result[0].newsTotal! : result[0].videosTotal!),
              type: result[0].newsType!,
              breakNewsModel: []));
        } else {
          List<BreakingNewsModel> updatedResults = (state as SectionByIdFetchSuccess).breakNewsModel;
          updatedResults.addAll((result[0].newsType == "breaking_news" ? result[0].breakNews : result[0].breakVideos) as List<BreakingNewsModel>);

          emit(SectionByIdFetchSuccess(
              breakNewsModel: updatedResults,
              totalCount: result[0].newsType == "breaking_news" ? result[0].breakNewsTotal! : result[0].videosTotal!,
              hasMoreFetchError: false,
              hasMore: updatedResults.length < (result[0].newsType == "breaking_news" ? result[0].breakNewsTotal! : result[0].videosTotal!),
              type: result[0].newsType!,
              newsModel: []));
        }
      } catch (e) {
        if ((state as SectionByIdFetchSuccess).newsModel.isNotEmpty) {
          emit(SectionByIdFetchSuccess(
              newsModel: (state as SectionByIdFetchSuccess).newsModel,
              hasMoreFetchError: true,
              totalCount: (state as SectionByIdFetchSuccess).totalCount,
              hasMore: (state as SectionByIdFetchSuccess).hasMore,
              type: (state as SectionByIdFetchSuccess).type,
              breakNewsModel: []));
        } else {
          emit(SectionByIdFetchSuccess(
              breakNewsModel: (state as SectionByIdFetchSuccess).breakNewsModel,
              hasMoreFetchError: true,
              totalCount: (state as SectionByIdFetchSuccess).totalCount,
              hasMore: (state as SectionByIdFetchSuccess).hasMore,
              type: (state as SectionByIdFetchSuccess).type,
              newsModel: []));
        }
      }
    }
  }
}
