// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../data/repositories/LanguageJson/languageJsonRepository.dart';
import '../utils/appLanguages.dart';

abstract class LanguageJsonState {}

class LanguageJsonInitial extends LanguageJsonState {}

class LanguageJsonFetchInProgress extends LanguageJsonState {}

class LanguageJsonFetchSuccess extends LanguageJsonState {
  Map<dynamic, dynamic> languageJson;

  LanguageJsonFetchSuccess({
    required this.languageJson,
  });
}

class LanguageJsonFetchFailure extends LanguageJsonState {
  final String errorMessage;

  LanguageJsonFetchFailure(this.errorMessage);
}

class LanguageJsonCubit extends Cubit<LanguageJsonState> {
  final LanguageJsonRepository _languageJsonRepository;

  LanguageJsonCubit(this._languageJsonRepository) : super(LanguageJsonInitial());

  void fetchCurrentLanguageAndLabels(String currentLanguage) async {
    try {
      emit(LanguageJsonFetchInProgress());

      await _languageJsonRepository.fetchLanguageLabels(currentLanguage).then((value) {
        emit(LanguageJsonFetchSuccess(languageJson: value));
      });
    } catch (e) {
      emit(LanguageJsonFetchSuccess(
        languageJson: appLanguageLabelKeys,
      ));
    }
  }

  void getLanguageJson({required BuildContext context, required String lanCode}) async {
    try {
      emit(LanguageJsonFetchInProgress());
      final result = await _languageJsonRepository.getLanguageJson(lanCode: lanCode);

      emit(LanguageJsonFetchSuccess(languageJson: result));
    } catch (e) {
      emit(LanguageJsonFetchSuccess(languageJson: appLanguageLabelKeys));
    }
  }

  String getTranslatedLabels(String label) {
    if (state is LanguageJsonFetchSuccess) {
      return (state as LanguageJsonFetchSuccess).languageJson[label] ?? appLanguageLabelKeys[label] ?? label;
    } else {
      return appLanguageLabelKeys[label] ?? label;
    }
  }
}
