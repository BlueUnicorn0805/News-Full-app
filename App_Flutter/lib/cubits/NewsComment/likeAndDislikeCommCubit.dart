// ignore_for_file: file_names

import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/models/CommentModel.dart';
import '../../data/repositories/NewsComment/LikeAndDislikeComment/likeAndDislikeCommRepository.dart';
import '../../utils/api.dart';

abstract class LikeAndDislikeCommState {}

class LikeAndDislikeCommInitial extends LikeAndDislikeCommState {}

class LikeAndDislikeCommInProgress extends LikeAndDislikeCommState {}

class LikeAndDislikeCommSuccess extends LikeAndDislikeCommState {
  final CommentModel comment;
  final bool wasLikeAndDislikeCommNewsProcess;
  final bool fromLike;

  LikeAndDislikeCommSuccess(this.comment, this.wasLikeAndDislikeCommNewsProcess, this.fromLike);
}

class LikeAndDislikeCommFailure extends LikeAndDislikeCommState {
  final String errorMessage;

  LikeAndDislikeCommFailure(this.errorMessage);
}

class LikeAndDislikeCommCubit extends Cubit<LikeAndDislikeCommState> {
  final LikeAndDislikeCommRepository _likeAndDislikeCommRepository;

  LikeAndDislikeCommCubit(this._likeAndDislikeCommRepository) : super(LikeAndDislikeCommInitial());

  void setLikeAndDislikeComm({required String userId, required CommentModel comment, required String status, required String langId, required bool fromLike}) {
    emit(LikeAndDislikeCommInProgress());
    _likeAndDislikeCommRepository.setLikeAndDislikeComm(userId: userId, commId: comment.id!, status: status, langId: langId).then((value) {
      emit(LikeAndDislikeCommSuccess(comment, true, fromLike));
    }).catchError((e) {
      ApiMessageAndCodeException apiMessageAndCodeException = e;
      emit(LikeAndDislikeCommFailure(apiMessageAndCodeException.errorMessage.toString()));
    });
  }
}
