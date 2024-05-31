// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/models/NewsModel.dart';
import '../../data/repositories/LikeAndDisLikeNews/LikeAndDisLikeNewsRepository.dart';
import '../../utils/api.dart';

abstract class UpdateLikeAndDisLikeStatusState {}

class UpdateLikeAndDisLikeStatusInitial extends UpdateLikeAndDisLikeStatusState {}

class UpdateLikeAndDisLikeStatusInProgress extends UpdateLikeAndDisLikeStatusState {}

class UpdateLikeAndDisLikeStatusSuccess extends UpdateLikeAndDisLikeStatusState {
  final NewsModel news;
  final bool wasLikeAndDisLikeNewsProcess; //to check that process is to favorite the news or not
  UpdateLikeAndDisLikeStatusSuccess(this.news, this.wasLikeAndDisLikeNewsProcess);
}

class UpdateLikeAndDisLikeStatusFailure extends UpdateLikeAndDisLikeStatusState {
  final String errorMessage;

  UpdateLikeAndDisLikeStatusFailure(this.errorMessage);
}

class UpdateLikeAndDisLikeStatusCubit extends Cubit<UpdateLikeAndDisLikeStatusState> {
  final LikeAndDisLikeRepository likeAndDisLikeRepository;

  UpdateLikeAndDisLikeStatusCubit(this.likeAndDisLikeRepository) : super(UpdateLikeAndDisLikeStatusInitial());

  void setLikeAndDisLikeNews({required String userId, required NewsModel news, required BuildContext context, required String status}) {
    emit(UpdateLikeAndDisLikeStatusInProgress());
    likeAndDisLikeRepository.setLikeAndDisLike(userId: userId, newsId: (news.newsId != null) ? news.newsId! : news.id!, status: status, context: context).then((value) {
      emit(UpdateLikeAndDisLikeStatusSuccess(news, status == "1" ? true : false));
    }).catchError((e) {
      ApiMessageAndCodeException apiMessageAndCodeException = e;
      emit(UpdateLikeAndDisLikeStatusFailure(apiMessageAndCodeException.errorMessage.toString()));
    });
  }
}
