// ignore_for_file: file_names

import 'package:facebook_audience_network/ad/ad_banner.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:unity_ads_plugin/unity_ads_plugin.dart' as unity;
import 'package:news/cubits/appSystemSettingCubit.dart';

setBannerAd(BuildContext context, BannerAd? bannerAd) {
  if (context.read<AppConfigurationCubit>().bannerId() != "") {
    switch (context.read<AppConfigurationCubit>().checkAdsType()) {
      case "google":
        return Padding(
          padding: const EdgeInsetsDirectional.only(start: 5.0, end: 5.0),
          child: SizedBox(width: double.maxFinite, height: bannerAd!.size.height.toDouble(), child: AdWidget(ad: bannerAd)),
        );
      case "fb":
        return FacebookBannerAd(
          placementId: context.read<AppConfigurationCubit>().bannerId()!,
          bannerSize: BannerSize.STANDARD,
          listener: (result, value) {
            switch (result) {
              case BannerAdResult.ERROR:
                debugPrint("Error: $value");
                break;
              case BannerAdResult.LOADED:
                debugPrint("Loaded: $value");
                break;
              case BannerAdResult.CLICKED:
                debugPrint("Clicked: $value");
                break;
              case BannerAdResult.LOGGING_IMPRESSION:
                debugPrint("Logging Impression: $value");
                break;
            }
          },
        );
      case "unity":
        return unity.UnityBannerAd(
          placementId: context.read<AppConfigurationCubit>().bannerId()!,
          onLoad: (placementId) => debugPrint('Banner loaded: $placementId'),
          onClick: (placementId) => debugPrint('Banner clicked: $placementId'),
          onFailed: (placementId, error, message) => debugPrint('Banner Ad $placementId failed: $error $message'),
        );
      default:
        return const SizedBox.shrink();
    }
  }
}
