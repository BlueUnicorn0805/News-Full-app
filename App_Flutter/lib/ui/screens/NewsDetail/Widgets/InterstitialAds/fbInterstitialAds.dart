// ignore_for_file: file_names

import 'package:facebook_audience_network/ad/ad_interstitial.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../../cubits/appSystemSettingCubit.dart';

bool _isInterstitialAdLoaded = false;

void loadFbInterstitialAd(BuildContext context) {
  if (context.read<AppConfigurationCubit>().interstitialId() != "") {
    FacebookInterstitialAd.loadInterstitialAd(
      placementId: context.read<AppConfigurationCubit>().interstitialId()!,
      listener: (result, value) {
        debugPrint(">> FAN > Interstitial Ad: $result --> $value");
        if (result == InterstitialAdResult.LOADED) {
          _isInterstitialAdLoaded = true;
        }

        if (result == InterstitialAdResult.DISMISSED && value["invalidated"] == true) {
          debugPrint("invalidated fb");
          _isInterstitialAdLoaded = false;
          loadFbInterstitialAd(context);
        }
      },
    );
  }
}

showFBInterstitialAd() {
  if (_isInterstitialAdLoaded == true) {
    FacebookInterstitialAd.showInterstitialAd();
  } else {
    debugPrint("Interstial Ad not yet loaded!");
  }
}
