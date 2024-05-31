// ignore_for_file: file_names

import 'dart:io';
import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/models/AppSystemSettingModel.dart';
import 'package:news/data/repositories/AppSystemSetting/systemRepository.dart';

abstract class AppConfigurationState extends Equatable {}

class AppConfigurationInitial extends AppConfigurationState {
  @override
  List<Object?> get props => [];
}

class AppConfigurationFetchInProgress extends AppConfigurationState {
  @override
  List<Object?> get props => [];
}

class AppConfigurationFetchSuccess extends AppConfigurationState {
  final AppSystemSettingModel appConfiguration;

  AppConfigurationFetchSuccess({required this.appConfiguration});

  @override
  List<Object?> get props => [appConfiguration];
}

class AppConfigurationFetchFailure extends AppConfigurationState {
  final String errorMessage;

  AppConfigurationFetchFailure(this.errorMessage);

  @override
  List<Object?> get props => [errorMessage];
}

class AppConfigurationCubit extends Cubit<AppConfigurationState> {
  final SystemRepository _systemRepository;

  AppConfigurationCubit(this._systemRepository) : super(AppConfigurationInitial());

  fetchAppConfiguration() async {
    emit(AppConfigurationFetchInProgress());
    try {
      final appConfiguration = AppSystemSettingModel.fromJson(await _systemRepository.fetchSettings());
      emit(AppConfigurationFetchSuccess(appConfiguration: appConfiguration));
    } catch (e) {
      emit(AppConfigurationFetchFailure(e.toString()));
    }
  }

  AppSystemSettingModel getAppConfiguration() {
    if (state is AppConfigurationFetchSuccess) {
      return (state as AppConfigurationFetchSuccess).appConfiguration;
    }
    return AppSystemSettingModel.fromJson({});
  }

  String? getBreakingNewsMode() {
    if (state is AppConfigurationFetchSuccess) {
      return getAppConfiguration().breakNewsMode;
    }
    return "";
  }

  String? getLiveStreamMode() {
    if (state is AppConfigurationFetchSuccess) {
      return getAppConfiguration().liveStreamMode;
    }
    return "";
  }

  String? getCategoryMode() {
    if (state is AppConfigurationFetchSuccess) {
      return getAppConfiguration().catMode;
    }
    return "";
  }

  String? getSubCatMode() {
    if (state is AppConfigurationFetchSuccess) {
      return getAppConfiguration().subCatMode;
    }
    return "";
  }

  String? getCommentsMode() {
    if (state is AppConfigurationFetchSuccess) {
      return getAppConfiguration().commentMode;
    }
    return "";
  }

  String? getInAppAdsMode() {
    if (state is AppConfigurationFetchSuccess) {
      return (Platform.isAndroid) ? getAppConfiguration().inAppAdsMode : getAppConfiguration().iosInAppAdsMode;
    }
    return "";
  }

  String? checkAdsType() {
    if (getInAppAdsMode() == "1") {
      return (Platform.isIOS) ? getIOSAdsType() : getAdsType();
    }
    return null;
  }

  String? getAdsType() {
    if (state is AppConfigurationFetchSuccess) {
      switch (getAppConfiguration().adsType) {
        case "1":
          return "google";
        case "2":
          return "fb";
        case "3":
          return "unity";
        default:
          return "";
      }
    }
    return "";
  }

  String? getIOSAdsType() {
    if (state is AppConfigurationFetchSuccess) {
      switch (getAppConfiguration().iosAdsType) {
        case "1":
          return "google";
        case "2":
          return "fb";
        case "3":
          return "unity";
        default:
          return "";
      }
    }
    return "";
  }

  String? bannerId() {
    if (state is AppConfigurationFetchSuccess) {
      if (Platform.isAndroid && getInAppAdsMode() != "0") {
        if (getAdsType() == "fb") return getAppConfiguration().fbBannerId;
        if (getAdsType() == "google") return getAppConfiguration().goBannerId;
        if (getAdsType() == "unity") return getAppConfiguration().unityBannerId;
      }
      if (Platform.isIOS && getInAppAdsMode() != "0") {
        if (getIOSAdsType() == "fb") return getAppConfiguration().fbIOSBannerId;
        if (getIOSAdsType() == "google") return getAppConfiguration().goIOSBannerId;
        if (getIOSAdsType() == "unity") return getAppConfiguration().unityIOSBannerId;
      }
    }
    return "";
  }

  String? rewardId() {
    if (state is AppConfigurationFetchSuccess) {
      if (Platform.isAndroid && getInAppAdsMode() != "0") {
        if (getAdsType() == "fb") return getAppConfiguration().fbRewardedId;
        if (getAdsType() == "google") return getAppConfiguration().goRewardedId;
        if (getAdsType() == "unity") return getAppConfiguration().unityRewardedId;
      }
      if (Platform.isIOS && getInAppAdsMode() != "0") {
        if (getIOSAdsType() == "fb") return getAppConfiguration().fbIOSRewardedId;
        if (getIOSAdsType() == "google") return getAppConfiguration().goIOSRewardedId;
        if (getIOSAdsType() == "unity") return getAppConfiguration().unityIOSRewardedId;
      }
    }
    return "";
  }

  String? nativeId() {
    if (state is AppConfigurationFetchSuccess) {
      if (Platform.isAndroid && getInAppAdsMode() != "0") {
        if (getAdsType() == "fb") return getAppConfiguration().fbNativeId;
        if (getAdsType() == "google") return getAppConfiguration().goNativeId;
        if (getAdsType() == "unity") return ""; //no native ads in unity
      }
      if (Platform.isIOS && getInAppAdsMode() != "0") {
        if (getIOSAdsType() == "fb") return getAppConfiguration().fbIOSNativeId;
        if (getIOSAdsType() == "google") return getAppConfiguration().goIOSNativeId;
        if (getIOSAdsType() == "unity") return ""; //no native ads in unity
      }
    }
    return "";
  }

  String? interstitialId() {
    if (state is AppConfigurationFetchSuccess) {
      if (Platform.isAndroid && getInAppAdsMode() != "0") {
        if (getAdsType() == "fb") return getAppConfiguration().fbInterId;
        if (getAdsType() == "google") return getAppConfiguration().goInterId;
        if (getAdsType() == "unity") return getAppConfiguration().unityInterId;
      }
      if (Platform.isIOS && getInAppAdsMode() != "0") {
        if (getIOSAdsType() == "fb") return getAppConfiguration().fbIOSInterId;
        if (getIOSAdsType() == "google") return getAppConfiguration().goIOSInterId;
        if (getIOSAdsType() == "unity") return getAppConfiguration().unityIOSInterId;
      }
    }
    return "";
  }

  String? unityGameId() {
    if (Platform.isAndroid) {
      return getAppConfiguration().gameId;
    } else {
      return getAppConfiguration().iosGameId;
    }
  }
}
