// ignore_for_file: file_names

import 'package:flutter_bloc/flutter_bloc.dart';
import '../data/repositories/DeleteImageId/deleteImageRepository.dart';
import '../utils/api.dart';

abstract class DeleteImageState {}

class DeleteImageInitial extends DeleteImageState {}

class DeleteImageInProgress extends DeleteImageState {}

class DeleteImageSuccess extends DeleteImageState {
  final String message;

  DeleteImageSuccess(this.message);
}

class DeleteImageFailure extends DeleteImageState {
  final String errorMessage;

  DeleteImageFailure(this.errorMessage);
}

class DeleteImageCubit extends Cubit<DeleteImageState> {
  final DeleteImageRepository _deleteImageRepository;

  DeleteImageCubit(this._deleteImageRepository) : super(DeleteImageInitial());

  void setDeleteImage({
    required String imageId,
  }) {
    emit(DeleteImageInProgress());
    _deleteImageRepository
        .setDeleteImage(
      imageId: imageId,
    )
        .then((value) {
      emit(DeleteImageSuccess(value["message"]));
    }).catchError((e) {
      ApiMessageAndCodeException apiMessageAndCodeException = e;
      emit(DeleteImageFailure(apiMessageAndCodeException.errorMessage.toString()));
    });
  }
}
