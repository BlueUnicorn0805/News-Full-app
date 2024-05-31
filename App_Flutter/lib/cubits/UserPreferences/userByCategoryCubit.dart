// ignore_for_file: file_names, prefer_typing_uninitialized_variables

import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import 'package:news/data/repositories/UserByCategory/userByCatRepository.dart';

abstract class UserByCatState {}

class UserByCatInitial extends UserByCatState {}

class UserByCatFetchInProgress extends UserByCatState {}

class UserByCatFetchSuccess extends UserByCatState {
  var userByCat;

  UserByCatFetchSuccess({
    required this.userByCat,
  });
}

class UserByCatFetchFailure extends UserByCatState {
  final String errorMessage;

  UserByCatFetchFailure(this.errorMessage);
}

class UserByCatCubit extends Cubit<UserByCatState> {
  final UserByCatRepository _userByCatRepository;

  UserByCatCubit(this._userByCatRepository) : super(UserByCatInitial());

  void getUserByCat({required BuildContext context, required String userId}) async {
    emit(UserByCatFetchInProgress());
    try {
      final result = await _userByCatRepository.getUserByCat(context: context, userId: userId);

      emit(UserByCatFetchSuccess(userByCat: result['UserByCat']));
    } catch (e) {
      emit(UserByCatFetchFailure(e.toString()));
    }
  }
}
