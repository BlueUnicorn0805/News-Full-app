// ignore_for_file: file_names

import 'package:flutter_bloc/flutter_bloc.dart';
import '../../data/repositories/NewsComment/DeleteComment/deleteCommRepository.dart';
import '../../utils/api.dart';

abstract class DeleteCommState {}

class DeleteCommInitial extends DeleteCommState {}

class DeleteCommInProgress extends DeleteCommState {}

class DeleteCommSuccess extends DeleteCommState {
  final String message;

  DeleteCommSuccess(this.message);
}

class DeleteCommFailure extends DeleteCommState {
  final String errorMessage;

  DeleteCommFailure(this.errorMessage);
}

class DeleteCommCubit extends Cubit<DeleteCommState> {
  final DeleteCommRepository _deleteCommRepository;

  DeleteCommCubit(this._deleteCommRepository) : super(DeleteCommInitial());

  void setDeleteComm({
    required String userId,
    required String commId,
  }) {
    emit(DeleteCommInProgress());
    _deleteCommRepository
        .setDeleteComm(
      userId: userId,
      commId: commId,
    )
        .then((value) {
      emit(DeleteCommSuccess(value["message"]));
    }).catchError((e) {
      ApiMessageAndCodeException apiMessageAndCodeException = e;
      emit(DeleteCommFailure(apiMessageAndCodeException.errorMessage.toString()));
    });
  }
}
