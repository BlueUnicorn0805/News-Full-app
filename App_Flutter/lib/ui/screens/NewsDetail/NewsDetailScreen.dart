// ignore_for_file: use_build_context_synchronously, file_names

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/InterstitialAds/googleInterstitialAds.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/InterstitialAds/unityInterstitialAds.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/RerwardAds/fbRewardAds.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/RerwardAds/googleRewardAds.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/RerwardAds/unityRewardAds.dart';
import 'package:news/utils/constant.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:unity_ads_plugin/unity_ads_plugin.dart';
import 'package:news/cubits/appSystemSettingCubit.dart';
import 'package:news/data/models/BreakingNewsModel.dart';
import 'package:news/utils/internetConnectivity.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/InterstitialAds/fbInterstitialAds.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/NewsSubDetailsScreen.dart';

class NewsDetailScreen extends StatefulWidget {
  final NewsModel? model;
  final List<NewsModel>? newsList;
  final BreakingNewsModel? breakModel;
  final List<BreakingNewsModel>? breakNewsList;
  final bool isFromBreak;
  final bool fromShowMore;

  const NewsDetailScreen({Key? key, this.model, this.breakModel, this.breakNewsList, this.newsList, required this.isFromBreak, required this.fromShowMore}) : super(key: key);

  @override
  NewsDetailsState createState() => NewsDetailsState();

  static Route route(RouteSettings routeSettings) {
    final arguments = routeSettings.arguments as Map<String, dynamic>;
    return CupertinoPageRoute(
        builder: (_) => NewsDetailScreen(
            model: arguments['model'],
            breakModel: arguments['breakModel'],
            breakNewsList: arguments['breakNewsList'],
            newsList: arguments['newsList'],
            isFromBreak: arguments['isFromBreak'],
            fromShowMore: arguments['fromShowMore']));
  }
}

class NewsDetailsState extends State<NewsDetailScreen> {
  final PageController pageController = PageController();

  @override
  void initState() {
    if (context.read<AppConfigurationCubit>().getInAppAdsMode() == "1") {
      if (context.read<AppConfigurationCubit>().checkAdsType() == "google") {
        createGoogleInterstitialAd(context);
        createGoogleRewardedAd(context);
      } else if (context.read<AppConfigurationCubit>().checkAdsType() == "fb") {
        fbInit();
        loadFbInterstitialAd(context);
        loadFbRewardedAd(context);
      } else {
        if (context.read<AppConfigurationCubit>().unityGameId() != null) {
          UnityAds.init(
            gameId: context.read<AppConfigurationCubit>().unityGameId()!,
            testMode: true, //set it to False @Deployement
            onComplete: () {
              loadUnityInterAd(context.read<AppConfigurationCubit>().interstitialId()!);
              loadUnityRewardAd(context.read<AppConfigurationCubit>().rewardId()!);
            },
            onFailed: (error, message) => debugPrint('Initialization Failed: $error $message'),
          );
        }
      }
    }

    super.initState();
  }

  Widget showBreakingNews() {
    return PageView.builder(
        controller: pageController,
        scrollDirection: Axis.vertical,
        onPageChanged: (index) async {
          if (await InternetConnectivity.isNetworkAvailable()) {
            if (index % rewardAdsIndex == 0) showRewardAds();
            if (index % interstitialAdsIndex == 0) UiUtils.showInterstitialAds(context: context);
          }
        },
        itemCount: (widget.breakNewsList == null || widget.breakNewsList!.isEmpty) ? 1 : widget.breakNewsList!.length + 1,
        itemBuilder: (context, index) {
          return NewsSubDetails(
              breakModel: (index == 0) ? widget.breakModel : widget.breakNewsList![index - 1], fromShowMore: widget.fromShowMore, isFromBreak: widget.isFromBreak, model: widget.model);
        });
  }

  void showRewardAds() {
    if (context.read<AppConfigurationCubit>().getInAppAdsMode() == "1") {
      if (context.read<AppConfigurationCubit>().checkAdsType() == "google") {
        showGoogleRewardedAd(context);
      } else if (context.read<AppConfigurationCubit>().checkAdsType() == "fb") {
        showFbRewardedAd();
      } else {
        showUnityRewardAds(context.read<AppConfigurationCubit>().rewardId()!);
      }
    }
  }

  Widget showNews() {
    return PageView.builder(
        controller: pageController,
        onPageChanged: (index) async {
          if (await InternetConnectivity.isNetworkAvailable()) {
            if (index % rewardAdsIndex == 0) showRewardAds();
            if (index % interstitialAdsIndex == 0) UiUtils.showInterstitialAds(context: context);
          }
        },
        itemCount: (widget.newsList == null || widget.newsList!.isEmpty) ? 1 : widget.newsList!.length + 1,
        itemBuilder: (context, index) {
          return NewsSubDetails(model: (index == 0) ? widget.model : widget.newsList![index - 1], fromShowMore: widget.fromShowMore, isFromBreak: widget.isFromBreak, breakModel: widget.breakModel);
        });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: UiUtils.getColorScheme(context).secondary,
      body: widget.isFromBreak ? showBreakingNews() : showNews(),
    );
  }
}
