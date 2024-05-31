// ignore_for_file: file_names

import 'package:news/data/repositories/Settings/settingsLocalDataRepository.dart';

class SettingsRepository {
  static final SettingsRepository _settingsRepository = SettingsRepository._internal();
  late SettingsLocalDataRepository _settingsLocalDataSource;

  factory SettingsRepository() {
    _settingsRepository._settingsLocalDataSource = SettingsLocalDataRepository();
    return _settingsRepository;
  }

  SettingsRepository._internal();

  Map<String, dynamic> getCurrentSettings() {
    return {
      "showIntroSlider": _settingsLocalDataSource.getIntroSlider(),
      "languageCode": _settingsLocalDataSource.getCurrentLanguageCode(),
      "theme": _settingsLocalDataSource.getCurrentTheme(),
      "notification": _settingsLocalDataSource.getNotification(),
      "token": _settingsLocalDataSource.getToken(),
    };
  }

  void changeIntroSlider(bool value) => _settingsLocalDataSource.setIntroSlider(value);

  void changeToken(String value) => _settingsLocalDataSource.setToken(value);

  void changeNotification(bool value) => _settingsLocalDataSource.setNotification(value);
}
