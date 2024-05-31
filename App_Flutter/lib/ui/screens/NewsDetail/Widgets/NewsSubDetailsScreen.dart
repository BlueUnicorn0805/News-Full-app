// ignore_for_file: file_names

import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter_tts/flutter_tts.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:news/cubits/adSpacesNewsDetailsCubit.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/cubits/commentNewsCubit.dart';
import 'package:news/cubits/relatedNewsCubit.dart';
import 'package:news/cubits/setNewsViewsCubit.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/ImageView.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/horizontalBtnList.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/relatedNewsList.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/setBannderAds.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/tagView.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/titleView.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/videoBtn.dart';
import 'package:news/ui/widgets/adSpaces.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:unity_ads_plugin/unity_ads_plugin.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/cubits/appSystemSettingCubit.dart';
import 'package:news/data/models/BreakingNewsModel.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/CommentView.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/ReplyCommentView.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/RerwardAds/fbRewardAds.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/backBtn.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/dateView.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/descView.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/likeBtn.dart';

class NewsSubDetails extends StatefulWidget {
  final NewsModel? model;
  final BreakingNewsModel? breakModel;
  final bool fromShowMore;
  final bool isFromBreak;

  const NewsSubDetails({Key? key, this.model, this.breakModel, required this.fromShowMore, required this.isFromBreak}) : super(key: key);

  @override
  NewsSubDetailsState createState() => NewsSubDetailsState();
}

class NewsSubDetailsState extends State<NewsSubDetails> {
  bool comEnabled = false;
  bool isReply = false;
  int? replyComIndex;
  int fontValue = 15;
  bool isPlaying = false;
  double volume = 0.5;
  double pitch = 1.0;
  double rate = 0.5;
  BannerAd? _bannerAd;
  NewsModel? newsModel;
  FlutterTts? _flutterTts;
  late final ScrollController controller = ScrollController()..addListener(hasMoreCommScrollListener);

  @override
  void initState() {
    super.initState();
    newsModel = widget.model;
    getComments();
    getRelatedNews();
    initializeTts();
    if (context.read<AuthCubit>().getUserId() != "0") setNewsViews(isBreakingNews: (widget.isFromBreak) ? true : false);
    if (context.read<AppConfigurationCubit>().getInAppAdsMode() == "1") bannerAdsInitialized();
    Future.delayed(Duration.zero, () {
      context.read<AdSpacesNewsDetailsCubit>().getAdspaceForNewsDetails(langId: context.read<AppLocalizationCubit>().state.id);
    });
  }

  setNewsViews({required bool isBreakingNews}) {
    Future.delayed(Duration.zero, () {
      context
          .read<SetNewsViewsCubit>()
          .setSetNewsViews(userId: context.read<AuthCubit>().getUserId(), newsId: (isBreakingNews) ? widget.breakModel!.id! : newsModel!.newsId!, isBreakingNews: isBreakingNews);
    });
  }

  getComments() {
    if (!widget.isFromBreak && context.read<AppConfigurationCubit>().getCommentsMode() == "1") {
      Future.delayed(Duration.zero, () {
        context.read<CommentNewsCubit>().getCommentNews(context: context, newsId: newsModel!.newsId!, userId: context.read<AuthCubit>().getUserId());
      });
    }
  }

  getRelatedNews() {
    if (!widget.isFromBreak) {
      Future.delayed(Duration.zero, () {
        context.read<RelatedNewsCubit>().getRelatedNews(
            userId: context.read<AuthCubit>().getUserId(),
            langId: context.read<AppLocalizationCubit>().state.id,
            catId: (newsModel!.categoryId == "0" || newsModel!.categoryId == '') ? newsModel!.categoryId : null,
            subCatId: (newsModel!.subCatId != "0" || newsModel!.subCatId != '') ? newsModel!.subCatId : null);
      });
    }
  }

  @override
  void dispose() {
    _flutterTts!.stop();
    controller.dispose();
    super.dispose();
  }

  updateFontVal(int fontVal) {
    setState(() {
      fontValue = fontVal;
    });
  }

  initializeTts() {
    _flutterTts = FlutterTts();
    _flutterTts!.setStartHandler(() async {
      if (mounted) {
        setState(() {
          isPlaying = true;
        });
      }
    });

    _flutterTts!.setCompletionHandler(() {
      if (mounted) {
        setState(() {
          isPlaying = false;
        });
      }
    });

    _flutterTts!.setErrorHandler((err) {
      if (mounted) {
        setState(() {
          isPlaying = false;
        });
      }
    });
  }

  bannerAdsInitialized() {
    if (context.read<AppConfigurationCubit>().checkAdsType() == "unity") {
      UnityAds.init(
          gameId: context.read<AppConfigurationCubit>().unityGameId()!,
          testMode: true, //set it to false @Deployment
          onComplete: () {
            debugPrint('Initialization Complete');
          },
          onFailed: (error, message) {
            debugPrint('Initialization Failed: $error $message');
          });
    }

    if (context.read<AppConfigurationCubit>().checkAdsType() == "fb") {
      fbInit();
    }
    if (context.read<AppConfigurationCubit>().checkAdsType() == "google") {
      _createBottomBannerAd();
    }
  }

  void _createBottomBannerAd() {
    if (context.read<AppConfigurationCubit>().bannerId() != "") {
      _bannerAd = BannerAd(
        adUnitId: context.read<AppConfigurationCubit>().bannerId()!,
        request: const AdRequest(),
        size: AdSize.banner,
        listener: BannerAdListener(
          onAdLoaded: (_) {},
          onAdFailedToLoad: (ad, err) {
            ad.dispose();
          },
        ),
      );

      _bannerAd!.load();
    }
  }

  speak(String description) async {
    if (description.isNotEmpty) {
      await _flutterTts!.setVolume(volume);
      await _flutterTts!.setSpeechRate(rate);
      await _flutterTts!.setPitch(pitch);
      await _flutterTts!.getLanguages;
      await _flutterTts!.setLanguage(() {
        return context.read<AppLocalizationCubit>().state.languageCode;
      }());
      int length = description.length;
      if (length < 4000) {
        setState(() {
          isPlaying = true;
        });
        await _flutterTts!.speak(description);
        _flutterTts!.setCompletionHandler(() {
          setState(() {
            _flutterTts!.stop();
            isPlaying = false;
          });
        });
      } else if (length < 8000) {
        String temp1 = description.substring(0, length ~/ 2);
        await _flutterTts!.speak(temp1);
        _flutterTts!.setCompletionHandler(() {
          setState(() {
            isPlaying = true;
          });
        });

        String temp2 = description.substring(temp1.length, description.length);
        await _flutterTts!.speak(temp2);
        _flutterTts!.setCompletionHandler(() {
          setState(() {
            isPlaying = false;
          });
        });
      } else if (length < 12000) {
        String temp1 = description.substring(0, 3999);
        await _flutterTts!.speak(temp1);
        _flutterTts!.setCompletionHandler(() {
          setState(() {
            isPlaying = true;
          });
        });
        String temp2 = description.substring(temp1.length, 7999);
        await _flutterTts!.speak(temp2);
        _flutterTts!.setCompletionHandler(() {
          setState(() {});
        });
        String temp3 = description.substring(temp2.length, description.length);
        await _flutterTts!.speak(temp3);
        _flutterTts!.setCompletionHandler(() {
          setState(() {
            isPlaying = false;
          });
        });
      }
    }
  }

  stop() async {
    var result = await _flutterTts!.stop();
    if (result == 1) {
      setState(() {
        isPlaying = false;
      });
    }
  }

  Future<bool> onBackPress() {
    (widget.fromShowMore == true) ? Navigator.of(context).popUntil((route) => route.isFirst) : Navigator.pop(context);
    return Future.value(false);
  }

  Widget showViews() {
    return Row(
      mainAxisSize: MainAxisSize.min,
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        Icon(Icons.remove_red_eye_rounded, size: 17, color: UiUtils.getColorScheme(context).primaryContainer),
        const SizedBox(width: 5),
        Padding(
            padding: const EdgeInsets.only(bottom: 2),
            child: Text(((!widget.isFromBreak) ? newsModel!.totalViews : widget.breakModel!.totalViews) ?? '0',
                style: Theme.of(context).textTheme.bodySmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.w600), textAlign: TextAlign.center))
      ],
    );
  }

  otherMainDetails() {
    int readingTime = UiUtils.calculateReadingTime(widget.isFromBreak ? widget.breakModel!.desc! : newsModel!.desc!);
    String minutesPostfix = (readingTime == 1) ? UiUtils.getTranslatedLabel(context, 'minute') : UiUtils.getTranslatedLabel(context, 'minutes');
    return Padding(
        padding: EdgeInsets.only(top: MediaQuery.of(context).size.height / 2.7),
        child: Container(
          padding: const EdgeInsetsDirectional.only(top: 20.0, start: 20.0, end: 20.0),
          width: double.maxFinite,
          decoration: BoxDecoration(borderRadius: const BorderRadius.only(topLeft: Radius.circular(25), topRight: Radius.circular(25)), color: UiUtils.getColorScheme(context).secondary),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: <Widget>[
            allRowBtn(
                isFromBreak: widget.isFromBreak,
                context: context,
                breakModel: widget.isFromBreak ? widget.breakModel : null,
                model: !widget.isFromBreak ? newsModel! : null,
                fontVal: fontValue,
                updateFont: updateFontVal,
                isPlaying: isPlaying,
                speak: speak,
                stop: stop,
                updateComEnabled: updateCommentshow),
            BlocBuilder<AdSpacesNewsDetailsCubit, AdSpacesNewsDetailsState>(
              builder: (context, state) {
                return (state is AdSpacesNewsDetailsFetchSuccess && state.adSpaceTopData != null) ? AdSpaces(adsModel: state.adSpaceTopData!) : const SizedBox.shrink();
              },
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (!widget.isFromBreak) tagView(model: newsModel!, context: context, isFromDetailsScreen: true),
                if (!isReply && !comEnabled)
                  Padding(
                      padding: const EdgeInsetsDirectional.only(top: 8.0),
                      child: Row(mainAxisSize: MainAxisSize.min, children: [
                        if (!widget.isFromBreak) dateView(context, newsModel!.date!),
                        if (!widget.isFromBreak) const SizedBox(width: 20),
                        showViews(),
                        const SizedBox(width: 20),
                        Icon(Icons.circle, size: 10, color: UiUtils.getColorScheme(context).primaryContainer),
                        const SizedBox(width: 10),
                        CustomTextLabel(
                            text: "$readingTime $minutesPostfix ${UiUtils.getTranslatedLabel(context, 'read')}",
                            textStyle:
                                Theme.of(context).textTheme.bodySmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.8), fontSize: 12.0, fontWeight: FontWeight.w400))
                      ])),
                if (!isReply && !comEnabled) titleView(title: widget.isFromBreak ? widget.breakModel!.title! : newsModel!.title!, context: context),
                if (!isReply && !comEnabled) descView(desc: widget.isFromBreak ? widget.breakModel!.desc! : newsModel!.desc!, context: context, fontValue: fontValue.toDouble()),
              ],
            ),
            if (!widget.isFromBreak && !isReply && comEnabled) CommentView(newsId: newsModel!.id!, updateComFun: updateCommentshow, updateIsReplyFun: updateComReply),
            if (!widget.isFromBreak && isReply && comEnabled) ReplyCommentView(replyComIndex: replyComIndex!, replyComFun: updateComReply, newsId: newsModel!.id!),
            BlocBuilder<AdSpacesNewsDetailsCubit, AdSpacesNewsDetailsState>(
              builder: (context, state) {
                return (state is AdSpacesNewsDetailsFetchSuccess && state.adSpaceBottomData != null)
                    ? Padding(padding: const EdgeInsetsDirectional.only(bottom: 5), child: AdSpaces(adsModel: state.adSpaceBottomData!))
                    : const SizedBox.shrink();
              },
            ),
            if (!widget.isFromBreak && !isReply && !comEnabled && newsModel != null) RelatedNewsList(model: newsModel!),
          ]),
        ));
  }

  updateCommentshow(bool comEnabledUpdate) {
    setState(() => comEnabled = comEnabledUpdate);
  }

  updateComReply(bool comReplyUpdate, int comIndex) {
    setState(() {
      isReply = comReplyUpdate;
      replyComIndex = comIndex;
    });
  }

  void hasMoreCommScrollListener() {
    if (!widget.isFromBreak && comEnabled && !isReply) {
      if (controller.position.maxScrollExtent == controller.offset) {
        if (context.read<CommentNewsCubit>().hasMoreCommentNews()) {
          context.read<CommentNewsCubit>().getMoreCommentNews(context: context, newsId: newsModel!.id!, userId: context.read<AuthCubit>().getUserId());
        } else {
          debugPrint("No more Comments");
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: onBackPress,
      child: Column(children: [
        Expanded(
          flex: (Platform.isAndroid) ? 12 : 9,
          child: SingleChildScrollView(
              controller: !widget.isFromBreak && comEnabled && !isReply ? controller : null,
              child: Stack(children: <Widget>[
                ImageView(isFromBreak: widget.isFromBreak, model: newsModel, breakModel: widget.breakModel),
                backBtn(context, widget.fromShowMore),
                videoBtn(context: context, isFromBreak: widget.isFromBreak, model: !widget.isFromBreak ? newsModel! : null, breakModel: widget.isFromBreak ? widget.breakModel! : null),
                otherMainDetails(),
                if (!widget.isFromBreak) likeBtn(context, newsModel!),
              ])),
        ),
        if ((context.read<AppConfigurationCubit>().getInAppAdsMode() == "1") || _bannerAd != null) Flexible(child: setBannerAd(context, _bannerAd))
      ]),
    );
  }
}
