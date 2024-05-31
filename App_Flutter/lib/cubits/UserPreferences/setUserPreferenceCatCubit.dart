// ignore_for_file: file_names, prefer_typing_uninitialized_variables

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import 'package:news/data/repositories/SetUserPreferenceCat/setUserPrefCatRepository.dart';

abstract class SetUserPrefCatState {}

class SetUserPrefCatInitial extends SetUserPrefCatState {}

class SetUserPrefCatFetchInProgress extends SetUserPrefCatState {}

class SetUserPrefCatFetchSuccess extends SetUserPrefCatState {
  var setUserPrefCat;

  SetUserPrefCatFetchSuccess({
    required this.setUserPrefCat,
  });
}

class SetUserPrefCatFetchFailure extends SetUserPrefCatState {
  final String errorMessage;

  SetUserPrefCatFetchFailure(this.errorMessage);
}

class SetUserPrefCatCubit extends Cubit<SetUserPrefCatState> {
  final SetUserPrefCatRepository _setUserPrefCatRepository;

  SetUserPrefCatCubit(this._setUserPrefCatRepository) : super(SetUserPrefCatInitial());

  void setUserPrefCat({required BuildContext context, required String catId, required String userId}) async {
    emit(SetUserPrefCatFetchInProgress());
    try {
      final result = await _setUserPrefCatRepository.setUserPrefCat(context: context, catId: catId, userId: userId);

      emit(SetUserPrefCatFetchSuccess(setUserPrefCat: result['SetUserPrefCat']));
    } catch (e) {
      emit(SetUserPrefCatFetchFailure(e.toString()));
    }
  }
}
