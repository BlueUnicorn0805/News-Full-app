// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:news/cubits/appSystemSettingCubit.dart';

const AdRequest request = AdRequest(
  //static
  keywords: <String>['foo', 'bar'],
  contentUrl: 'http://foo.com/bar.html',
  nonPersonalizedAds: true,
);
RewardedAd? rewardedAd;
int _numRewardedLoadAttempts = 0;
int maxFailedLoadAttempts = 3;

void createGoogleRewardedAd(BuildContext context) {
  if (context.read<AppConfigurationCubit>().rewardId() != "") {
    RewardedAd.load(
        adUnitId: context.read<AppConfigurationCubit>().rewardId()!,
        request: request,
        rewardedAdLoadCallback: RewardedAdLoadCallback(
          onAdLoaded: (RewardedAd ad) {
            rewardedAd = ad;
            _numRewardedLoadAttempts = 0;
          },
          onAdFailedToLoad: (LoadAdError error) {
            rewardedAd = null;
            _numRewardedLoadAttempts += 1;
            if (_numRewardedLoadAttempts <= maxFailedLoadAttempts) {
              createGoogleRewardedAd(context);
            }
          },
        ));
  }
}

void showGoogleRewardedAd(BuildContext context) {
  if (context.read<AppConfigurationCubit>().rewardId() != "") {
    if (rewardedAd == null) {
      debugPrint('Warning: attempt to show rewarded before loaded.');
      return;
    }
    rewardedAd!.fullScreenContentCallback = FullScreenContentCallback(
      onAdShowedFullScreenContent: (RewardedAd ad) => debugPrint('ad onAdShowedFullScreenContent.'),
      onAdDismissedFullScreenContent: (RewardedAd ad) {
        debugPrint('$ad onAdDismissedFullScreenContent.');
        ad.dispose();
        createGoogleRewardedAd(context);
      },
      onAdFailedToShowFullScreenContent: (RewardedAd ad, AdError error) {
        debugPrint('$ad onAdFailedToShowFullScreenContent: $error');
        ad.dispose();
        createGoogleRewardedAd(context);
      },
    );

    rewardedAd!.setImmersiveMode(true);
    rewardedAd!.show(onUserEarnedReward: (AdWithoutView ad, RewardItem reward) {
      debugPrint('$ad with reward $RewardItem(${reward.amount}, ${reward.type})');
    });
    rewardedAd = null;
  }
}
