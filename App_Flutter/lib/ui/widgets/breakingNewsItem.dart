// ignore_for_file: file_names

import 'dart:io';
import 'package:facebook_audience_network/ad/ad_native.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:news/ui/widgets/networkImage.dart';
import 'package:news/utils/constant.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/app/routes.dart';
import 'package:news/cubits/appSystemSettingCubit.dart';
import 'package:news/data/models/BreakingNewsModel.dart';

class BreakNewsItem extends StatefulWidget {
  final BreakingNewsModel model;
  final int index;
  final List<BreakingNewsModel> breakNewsList;

  const BreakNewsItem({
    super.key,
    required this.model,
    required this.index,
    required this.breakNewsList,
  });

  @override
  BreakNewsItemState createState() => BreakNewsItemState();
}

class BreakNewsItemState extends State<BreakNewsItem> {
  late BannerAd _bannerAd;
  @override
  void initState() {
    if (context.read<AppConfigurationCubit>().getInAppAdsMode() == "1") createBannerAd();
    super.initState();
  }

  Widget newsData() {
    return Builder(builder: (context) {
      return Padding(
          padding: EdgeInsetsDirectional.only(top: widget.index == 0 ? 0 : 15.0),
          child: Column(children: [
            if (context.read<AppConfigurationCubit>().getInAppAdsMode() == "1") nativeAdsShow(),
            InkWell(
              child: Column(
                children: <Widget>[
                  ClipRRect(
                      borderRadius: BorderRadius.circular(10.0),
                      child: CustomNetworkImage(networkImageUrl: widget.model.image!, width: double.infinity, height: MediaQuery.of(context).size.height / 4.2, fit: BoxFit.cover, isVideo: false)),
                  Container(
                      alignment: Alignment.bottomLeft,
                      padding: const EdgeInsetsDirectional.only(top: 4.0, start: 5.0, end: 5.0),
                      child: Text(widget.model.title!,
                          style: Theme.of(context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.9)),
                          maxLines: 2,
                          softWrap: true,
                          overflow: TextOverflow.ellipsis)),
                ],
              ),
              onTap: () {
                List<BreakingNewsModel> newsList = [];
                newsList.addAll(widget.breakNewsList);
                newsList.removeAt(widget.index);
                Navigator.of(context).pushNamed(Routes.newsDetails, arguments: {"breakModel": widget.model, "breakNewsList": newsList, "isFromBreak": true, "fromShowMore": false});
              },
            ),
          ]));
    });
  }

  BannerAd createBannerAd() {
    if (context.read<AppConfigurationCubit>().bannerId() != "") {
      _bannerAd = BannerAd(
        adUnitId: context.read<AppConfigurationCubit>().bannerId()!,
        request: const AdRequest(),
        size: AdSize.mediumRectangle,
        listener: BannerAdListener(
            onAdLoaded: (_) {},
            onAdFailedToLoad: (ad, err) {
              debugPrint("error in loading Native ad $err");
              ad.dispose();
            },
            onAdOpened: (Ad ad) => debugPrint('Native ad opened.'),
            // Called when an ad opens an overlay that covers the screen.
            onAdClosed: (Ad ad) => debugPrint('Native ad closed.'),
            // Called when an ad removes an overlay that covers the screen.
            onAdImpression: (Ad ad) => debugPrint('Native ad impression.')),
      );
    }
    return _bannerAd;
  }

  Widget bannerAdsShow() {
    return AdWidget(key: UniqueKey(), ad: createBannerAd()..load());
  }

  Widget fbNativeAdsShow() {
    return (context.read<AppConfigurationCubit>().nativeId() != "")
        ? FacebookNativeAd(
            placementId: context.read<AppConfigurationCubit>().nativeId()!,
            adType: Platform.isAndroid ? NativeAdType.NATIVE_AD : NativeAdType.NATIVE_AD_VERTICAL,
            width: double.infinity,
            height: 320,
            keepAlive: true,
            keepExpandedWhileLoading: false,
            expandAnimationDuraion: 300,
            listener: (result, value) {
              debugPrint("Native Ad: $result --> $value");
            },
          )
        : const SizedBox.shrink();
  }

  Widget nativeAdsShow() {
    if (context.read<AppConfigurationCubit>().getInAppAdsMode() == "1" &&
        context.read<AppConfigurationCubit>().checkAdsType() != null &&
        context.read<AppConfigurationCubit>().getAdsType() != "unity" &&
        widget.index != 0 &&
        widget.index % nativeAdsIndex == 0) {
      return Padding(
          padding: const EdgeInsets.only(bottom: 15.0),
          child: Container(
              padding: const EdgeInsets.all(7.0),
              height: 300,
              width: double.infinity,
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.5),
                borderRadius: BorderRadius.circular(10.0),
              ),
              child: context.read<AppConfigurationCubit>().checkAdsType() == "google" && (context.read<AppConfigurationCubit>().bannerId() != "") //google ads
                  ? bannerAdsShow()
                  : fbNativeAdsShow()));
    } else {
      return const SizedBox.shrink();
    }
  }

  @override
  Widget build(BuildContext context) {
    return newsData();
  }
}
