// ignore_for_file: prefer_typing_uninitialized_variables, file_names

import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../data/repositories/AddNews/addNewsRepository.dart';

abstract class AddNewsState {}

class AddNewsInitial extends AddNewsState {}

class AddNewsFetchInProgress extends AddNewsState {}

class AddNewsFetchSuccess extends AddNewsState {
  var addNews;

  AddNewsFetchSuccess({
    required this.addNews,
  });
}

class AddNewsFetchFailure extends AddNewsState {
  final String errorMessage;

  AddNewsFetchFailure(this.errorMessage);
}

class AddNewsCubit extends Cubit<AddNewsState> {
  final AddNewsRepository _addNewsRepository;

  AddNewsCubit(this._addNewsRepository) : super(AddNewsInitial());

  void addNews(
      {required BuildContext context,
      required String actionType,
      required String userId,
      required String catId,
      required String title,
      required String conTypeId,
      required String conType,
      required String langId,
      File? image,
      String? newsId,
      String? subCatId,
      String? showTill,
      String? tagId,
      String? url,
      File? videoUpload,
      String? desc,
      List<File>? otherImage}) async {
    try {
      emit(AddNewsFetchInProgress());
      final result = await _addNewsRepository.addNews(
          context: context,
          actionType: actionType,
          newsId: newsId,
          userId: userId,
          title: title,
          image: image,
          conTypeId: conTypeId,
          conType: conType,
          langId: langId,
          catId: catId,
          videoUpload: videoUpload,
          url: url,
          tagId: tagId,
          otherImage: otherImage,
          desc: desc,
          showTill: showTill,
          subCatId: subCatId);
      emit(AddNewsFetchSuccess(addNews: result));
    } catch (e) {
      emit(AddNewsFetchFailure(e.toString()));
    }
  }
}
