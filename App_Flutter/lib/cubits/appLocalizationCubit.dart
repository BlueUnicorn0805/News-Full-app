// ignore_for_file: file_names

import 'package:flutter_bloc/flutter_bloc.dart';
import '../data/repositories/Settings/settingsLocalDataRepository.dart';

class AppLocalizationState {
  String languageCode;
  String id;
  String isRTL;

  AppLocalizationState(
    this.languageCode,
    this.id,
    this.isRTL,
  );
}

class AppLocalizationCubit extends Cubit<AppLocalizationState> {
  final SettingsLocalDataRepository _settingsRepository;

  AppLocalizationCubit(this._settingsRepository)
      : super(AppLocalizationState(_settingsRepository.getCurrentLanguageCode(), _settingsRepository.getCurrentLanguageId(), _settingsRepository.getCurrentLanguageRTL()));

  void changeLanguage(String lanCode, String lanId, String lanRTL) {
    _settingsRepository.setCurrentLanguageCode(lanCode);
    _settingsRepository.setCurrentLangId(lanId);
    _settingsRepository.setCurrentLanguageRTL(lanRTL);
    emit(AppLocalizationState(lanCode, lanId, lanRTL));
  }
}
