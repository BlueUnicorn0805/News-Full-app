// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/repositories/Auth/authRepository.dart';

abstract class DeleteUserState {}

class DeleteUserInitial extends DeleteUserState {}

class DeleteUserFetchInProgress extends DeleteUserState {}

class DeleteUserFetchSuccess extends DeleteUserState {
  dynamic deleteUser;

  DeleteUserFetchSuccess({
    required this.deleteUser,
  });
}

class DeleteUserFetchFailure extends DeleteUserState {
  final String errorMessage;

  DeleteUserFetchFailure(this.errorMessage);
}

class DeleteUserCubit extends Cubit<DeleteUserState> {
  final AuthRepository _deleteUserRepository;

  DeleteUserCubit(this._deleteUserRepository) : super(DeleteUserInitial());

  Future<dynamic> setDeleteUser({required String userId, String? name, String? mobile, String? email, String? filePath, required BuildContext context}) async {
    try {
      emit(DeleteUserFetchInProgress());
      final result = await _deleteUserRepository.deleteUser(
        context: context,
        userId: userId,
      );
      emit(
        DeleteUserFetchSuccess(
          deleteUser: result,
        ),
      );
      return result;
    } catch (e) {
      emit(DeleteUserFetchFailure(e.toString()));
    }
  }
}
