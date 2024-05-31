// ignore_for_file: file_names

import 'package:flutter_bloc/flutter_bloc.dart';

import '../data/models/SettingsModel.dart';
import '../data/repositories/Settings/settingRepository.dart';

class SettingsState {
  final SettingsModel? settingsModel;

  SettingsState({this.settingsModel});
}

class SettingsCubit extends Cubit<SettingsState> {
  final SettingsRepository _settingsRepository;

  SettingsCubit(this._settingsRepository) : super(SettingsState()) {
    _getCurrentSettings();
  }

  void _getCurrentSettings() {
    emit(SettingsState(settingsModel: SettingsModel.fromJson(_settingsRepository.getCurrentSettings())));
  }

  SettingsModel getSettings() {
    return state.settingsModel!;
  }

  void changeShowIntroSlider(bool value) {
    _settingsRepository.changeIntroSlider(value);
    emit(SettingsState(settingsModel: state.settingsModel!.copyWith(showIntroSlider: value)));
  }

  void changeToken(String value) {
    _settingsRepository.changeToken(value);
    emit(SettingsState(settingsModel: state.settingsModel!.copyWith(token: value)));
  }

  void changeNotification(bool value) {
    //set HiveBoyKey value for Enabled Notifications
    
    _settingsRepository.changeNotification(value);
    emit(SettingsState(settingsModel: state.settingsModel!.copyWith(notification: value)));
  }
}
