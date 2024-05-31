// ignore_for_file: file_names, unused_element

import 'package:flutter/foundation.dart';
import 'package:unity_ads_plugin/unity_ads_plugin.dart';

void loadUnityRewardAd(String placementId) {
  UnityAds.load(
      placementId: placementId,
      onComplete: (placementId) {
        debugPrint('Load Complete $placementId');
      },
      onFailed: (placementId, error, message) {
        debugPrint('Load Failed $placementId: $error $message');
      });
}

void showUnityRewardAds(String placementId) {
  UnityAds.showVideoAd(
    placementId: placementId,
    onComplete: (placementId) {
      debugPrint('Video Ad $placementId completed');
      loadUnityRewardAd(placementId);
    },
    onFailed: (placementId, error, message) {
      debugPrint('Video Ad $placementId failed: $error $message');
      loadUnityRewardAd(placementId);
    },
    onStart: (placementId) => debugPrint('Video Ad $placementId started'),
    onClick: (placementId) => debugPrint('Video Ad $placementId click'),
    onSkipped: (placementId) {
      debugPrint('Video Ad $placementId skipped');
      loadUnityRewardAd(placementId);
    },
  );
}
