// ignore_for_file: file_names, unused_element

import 'package:flutter/cupertino.dart';
import 'package:unity_ads_plugin/unity_ads_plugin.dart';

void loadUnityInterAd(String placementId) {
  UnityAds.load(
      placementId: placementId,
      onComplete: (placementId) {
        debugPrint('Unity ad Load Complete $placementId');
      },
      onFailed: (placementId, error, message) {
        debugPrint('Unity ad Load Failed $placementId: $error $message');
      });
}

void showUnityInterstitialAds(String placementId) {
  UnityAds.showVideoAd(
    placementId: placementId,
    onComplete: (placementId) {
      debugPrint('Video Ad $placementId completed');
      loadUnityInterAd(placementId);
    },
    onFailed: (placementId, error, message) {
      debugPrint('Video Ad $placementId failed: $error $message');
      loadUnityInterAd(placementId);
    },
    onStart: (placementId) => debugPrint('Video Ad $placementId started'),
    onClick: (placementId) => debugPrint('Video Ad $placementId click'),
    onSkipped: (placementId) {
      debugPrint('Video Ad $placementId skipped');
      loadUnityInterAd(placementId);
    },
  );
}
