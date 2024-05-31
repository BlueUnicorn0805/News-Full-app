// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../data/models/NewsModel.dart';
import '../data/repositories/TagNews/tagNewsRepository.dart';

abstract class TagNewsState {}

class TagNewsInitial extends TagNewsState {}

class TagNewsFetchInProgress extends TagNewsState {}

class TagNewsFetchSuccess extends TagNewsState {
  final List<NewsModel> tagNews;

  TagNewsFetchSuccess({
    required this.tagNews,
  });
}

class TagNewsFetchFailure extends TagNewsState {
  final String errorMessage;

  TagNewsFetchFailure(this.errorMessage);
}

class TagNewsCubit extends Cubit<TagNewsState> {
  final TagNewsRepository _tagNewsRepository;

  TagNewsCubit(this._tagNewsRepository) : super(TagNewsInitial());

  void getTagNews({required BuildContext context, required String tagId, required String userId, required String langId}) async {
    try {
      emit(TagNewsFetchInProgress());
      final result = await _tagNewsRepository.getTagNews(context: context, langId: langId, tagId: tagId, userId: userId);

      emit(TagNewsFetchSuccess(tagNews: result['TagNews']));
    } catch (e) {
      emit(TagNewsFetchFailure(e.toString()));
    }
  }
}
