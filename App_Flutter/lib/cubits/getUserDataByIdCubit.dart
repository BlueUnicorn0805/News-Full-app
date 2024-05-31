// ignore_for_file: file_names, prefer_typing_uninitialized_variables

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../data/repositories/GetUserById/getUserByIdRepository.dart';

abstract class GetUserByIdState {}

class GetUserByIdInitial extends GetUserByIdState {}

class GetUserByIdFetchInProgress extends GetUserByIdState {}

class GetUserByIdFetchSuccess extends GetUserByIdState {
  var result;

  GetUserByIdFetchSuccess({
    required this.result,
  });
}

class GetUserByIdFetchFailure extends GetUserByIdState {
  final String errorMessage;

  GetUserByIdFetchFailure(this.errorMessage);
}

class GetUserByIdCubit extends Cubit<GetUserByIdState> {
  final GetUserByIdRepository _getUserByIdRepository;

  GetUserByIdCubit(this._getUserByIdRepository) : super(GetUserByIdInitial());

  void getUserById({required BuildContext context, required String userId}) {
    emit(GetUserByIdFetchInProgress());
    _getUserByIdRepository.getUserById(context: context, userId: userId).then((value) {
      emit(GetUserByIdFetchSuccess(result: value));
    }).catchError((e) {
      emit(GetUserByIdFetchFailure(e.toString()));
    });
  }
}
