// ignore_for_file: file_names

import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/repositories/SetNewsViews/setNewsViewsRepository.dart';
import 'package:news/utils/api.dart';

abstract class SetNewsViewsState {}

class SetNewsViewsInitial extends SetNewsViewsState {}

class SetNewsViewsInProgress extends SetNewsViewsState {}

class SetNewsViewsSuccess extends SetNewsViewsState {
  final String message;

  SetNewsViewsSuccess(this.message);
}

class SetNewsViewsFailure extends SetNewsViewsState {
  final String errorMessage;

  SetNewsViewsFailure(this.errorMessage);
}

class SetNewsViewsCubit extends Cubit<SetNewsViewsState> {
  final SetNewsViewsRepository setNewsViewsRepository;

  SetNewsViewsCubit(this.setNewsViewsRepository) : super(SetNewsViewsInitial());

  void setSetNewsViews({required String newsId, required String userId, required bool isBreakingNews}) {
    emit(SetNewsViewsInProgress());
    setNewsViewsRepository.setNewsViews(newsId: newsId, userId: userId, isBreakingNews: isBreakingNews).then((value) {
      emit(SetNewsViewsSuccess(value["message"]));
    }).catchError((e) {
      ApiMessageAndCodeException apiMessageAndCodeException = e;
      emit(SetNewsViewsFailure(apiMessageAndCodeException.errorMessage.toString()));
    });
  }
}
