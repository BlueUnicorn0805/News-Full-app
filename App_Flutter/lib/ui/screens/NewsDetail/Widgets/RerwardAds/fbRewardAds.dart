// ignore_for_file: file_names

import 'dart:io';

import 'package:device_info_plus/device_info_plus.dart';
import 'package:facebook_audience_network/facebook_audience_network.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import 'package:news/cubits/appSystemSettingCubit.dart';

bool _isRewardedAdLoaded = false;

fbInit() async {
  String? deviceId = await getId();

  FacebookAudienceNetwork.init(iOSAdvertiserTrackingEnabled: true, testingId: deviceId);
}

Future<String?> getId() async {
  var deviceInfo = DeviceInfoPlugin();
  if (Platform.isIOS) {
    var iosDeviceInfo = await deviceInfo.iosInfo;
    return iosDeviceInfo.identifierForVendor; // Unique ID on iOS
  }
  return null;
}

void loadFbRewardedAd(BuildContext context) {
  if (context.read<AppConfigurationCubit>().rewardId() != "") {
    FacebookRewardedVideoAd.loadRewardedVideoAd(
      placementId: context.read<AppConfigurationCubit>().rewardId()!,
      listener: (result, value) {
        if (result == RewardedVideoAdResult.LOADED) {
          _isRewardedAdLoaded = true;
        }

        if (result == RewardedVideoAdResult.VIDEO_CLOSED && value["invalidated"] == true) {
          _isRewardedAdLoaded = false;
          loadFbRewardedAd(context);
        }
      },
    );
  }
}

showFbRewardedAd() {
  if (_isRewardedAdLoaded == true) {
    FacebookRewardedVideoAd.showRewardedVideoAd();
  } else {
    debugPrint("Rewarded Ad not yet loaded!");
  }
}
