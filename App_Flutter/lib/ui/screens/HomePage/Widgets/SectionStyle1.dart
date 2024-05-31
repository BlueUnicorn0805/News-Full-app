// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/ui/screens/HomePage/Widgets/CommonSectionTitle.dart';

import '../../../../app/routes.dart';
import '../../../../data/models/BreakingNewsModel.dart';
import '../../../../data/models/FeatureSectionModel.dart';
import '../../../../data/models/NewsModel.dart';
import '../../../../utils/uiUtils.dart';
import '../../../styles/colors.dart';
import '../../../widgets/networkImage.dart';

class Style1Section extends StatefulWidget {
  final FeatureSectionModel model;

  const Style1Section({super.key, required this.model});

  @override
  Style1SectionState createState() => Style1SectionState();
}

class Style1SectionState extends State<Style1Section> {
  int? style1Sel;
  PageController? _pageStyle1Controller = PageController();

  @override
  void dispose() {
    _pageStyle1Controller!.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return style1Data(widget.model);
  }

  Widget style1Data(FeatureSectionModel model) {
    if (model.breakVideos!.isNotEmpty || model.breakNews!.isNotEmpty || model.videos!.isNotEmpty || model.news!.isNotEmpty) {
      return Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          commonSectionTitle(model, context),
          if (model.newsType == 'news' || model.videosType == "news" || model.newsType == "user_choice")
            if ((model.newsType == 'news' || model.newsType == "user_choice") ? model.news!.isNotEmpty : model.videos!.isNotEmpty) style1NewsData(model),
          if (model.newsType == 'breaking_news' || model.videosType == "breaking_news")
            if (model.newsType == 'breaking_news' ? model.breakNews!.isNotEmpty : model.breakVideos!.isNotEmpty) style1BreakNewsData(model)
        ],
      );
    } else {
      return const SizedBox.shrink();
    }
  }

  Widget style1NewsData(FeatureSectionModel model) {
    if ((model.newsType == 'news' || model.newsType == "user_choice") ? model.news!.length > 1 : model.videos!.length > 1) {
      style1Sel ??= 1;
      _pageStyle1Controller = PageController(initialPage: 1, viewportFraction: 0.87);
    } else {
      style1Sel = 0;
      _pageStyle1Controller = PageController(initialPage: 0, viewportFraction: 1);
    }

    return Column(
      mainAxisSize: MainAxisSize.min,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          height: MediaQuery.of(context).size.height * 0.36,
          width: double.maxFinite,
          child: PageView.builder(
            physics: const BouncingScrollPhysics(),
            itemCount: (model.newsType == 'news' || model.newsType == "user_choice") ? model.news!.length : model.videos!.length,
            scrollDirection: Axis.horizontal,
            pageSnapping: true,
            controller: _pageStyle1Controller,
            onPageChanged: (index) {
              setState(() {
                style1Sel = index;
              });
            },
            itemBuilder: (BuildContext context, int index) {
              NewsModel data = (model.newsType == 'news' || model.newsType == "user_choice") ? model.news![index] : model.videos![index];
              return InkWell(
                child: Padding(
                  padding: EdgeInsetsDirectional.only(start: 7, end: 7, top: style1Sel == index ? 0 : MediaQuery.of(context).size.height * 0.027),
                  child: Stack(
                    children: <Widget>[
                      ClipRRect(
                        borderRadius: BorderRadius.circular(15),
                        child: CustomNetworkImage(
                            networkImageUrl: data.image!,
                            height: style1Sel == index ? MediaQuery.of(context).size.height / 4 : MediaQuery.of(context).size.height / 5,
                            width: double.maxFinite,
                            fit: BoxFit.cover,
                            isVideo: model.newsType == 'videos' ? true : false),
                      ),
                      if (model.newsType == 'videos')
                        Positioned.directional(
                          textDirection: Directionality.of(context),
                          top: MediaQuery.of(context).size.height * 0.075,
                          start: MediaQuery.of(context).size.width / 3,
                          end: MediaQuery.of(context).size.width / 3,
                          child: InkWell(
                            onTap: () {
                              Navigator.of(context).pushNamed(Routes.newsVideo, arguments: {"from": 1, "model": data});
                            },
                            child: Container(
                              alignment: Alignment.center,
                              height: 40,
                              width: 40,
                              decoration: BoxDecoration(shape: BoxShape.circle, color: Theme.of(context).primaryColor),
                              child: const Icon(
                                Icons.play_arrow_sharp,
                                size: 25,
                                color: secondaryColor,
                              ),
                            ),
                          ),
                        ),
                      Positioned.directional(
                          textDirection: Directionality.of(context),
                          start: 8,
                          end: 8,
                          top: MediaQuery.of(context).size.height / 7,
                          child: Container(
                            alignment: Alignment.center,
                            height: MediaQuery.of(context).size.height / 5,
                            width: MediaQuery.of(context).size.width,
                            margin: const EdgeInsetsDirectional.all(10),
                            decoration: BoxDecoration(borderRadius: BorderRadius.circular(15), color: UiUtils.getColorScheme(context).background),
                            padding: const EdgeInsets.all(13),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              mainAxisSize: MainAxisSize.min,
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                if (data.categoryName != null)
                                  Container(
                                      height: 20.0,
                                      padding: const EdgeInsetsDirectional.only(start: 8.0, end: 8.0),
                                      decoration: BoxDecoration(borderRadius: BorderRadius.circular(5), color: Theme.of(context).primaryColor),
                                      child: Text(
                                        data.categoryName!,
                                        textAlign: TextAlign.center,
                                        style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                                              color: secondaryColor,
                                            ),
                                        overflow: TextOverflow.ellipsis,
                                        softWrap: true,
                                      )),
                                Padding(
                                  padding: const EdgeInsets.only(top: 15.0),
                                  child: Text(data.title!,
                                      style: Theme.of(context).textTheme.titleMedium!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.normal),
                                      softWrap: true,
                                      maxLines: 3,
                                      overflow: TextOverflow.ellipsis),
                                ),
                              ],
                            ),
                          )),
                    ],
                  ),
                ),
                onTap: () {
                  if (model.newsType == 'news' || model.newsType == "user_choice") {
                    List<NewsModel> newsList = [];
                    newsList.addAll(model.news!);
                    newsList.removeAt(index);
                    Navigator.of(context).pushNamed(Routes.newsDetails, arguments: {"model": data, "newsList": newsList, "isFromBreak": false, "fromShowMore": false});
                  }
                },
              );
            },
          ),
        ),
        style1Indicator(model, (model.newsType == 'news' || model.newsType == "user_choice") ? model.news!.length : model.videos!.length)
      ],
    );
  }

  Widget style1BreakNewsData(FeatureSectionModel model) {
    if (model.newsType == 'breaking_news' ? model.breakNews!.length > 1 : model.breakVideos!.length > 1) {
      style1Sel ??= 1;
      _pageStyle1Controller = PageController(initialPage: 1, viewportFraction: 0.87);
    } else {
      style1Sel = 0;
      _pageStyle1Controller = PageController(initialPage: 0, viewportFraction: 1);
    }
    return Column(
      mainAxisSize: MainAxisSize.min,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          height: MediaQuery.of(context).size.height * 0.36,
          width: double.maxFinite,
          child: PageView.builder(
            physics: const BouncingScrollPhysics(),
            itemCount: model.newsType == 'breaking_news' ? model.breakNews!.length : model.breakVideos!.length,
            scrollDirection: Axis.horizontal,
            controller: _pageStyle1Controller,
            reverse: false,
            onPageChanged: (index) {
              setState(() {
                style1Sel = index;
              });
            },
            itemBuilder: (BuildContext context, int index) {
              BreakingNewsModel data = model.newsType == 'breaking_news' ? model.breakNews![index] : model.breakVideos![index];

              return Padding(
                padding: EdgeInsetsDirectional.only(start: 7, end: 7, top: style1Sel == index ? 0 : MediaQuery.of(context).size.height * 0.027),
                child: InkWell(
                  onTap: () {
                    if (model.newsType == 'breaking_news') {
                      List<BreakingNewsModel> breakList = [];
                      breakList.addAll(model.breakNews!);
                      breakList.removeAt(index);
                      Navigator.of(context).pushNamed(Routes.newsDetails, arguments: {"breakModel": data, "breakNewsList": breakList, "isFromBreak": true, "fromShowMore": false});
                    }
                  },
                  child: Stack(
                    children: <Widget>[
                      ClipRRect(
                        borderRadius: BorderRadius.circular(15),
                        child: CustomNetworkImage(
                            networkImageUrl: data.image!,
                            height: style1Sel == index ? MediaQuery.of(context).size.height / 4 : MediaQuery.of(context).size.height / 5,
                            width: double.maxFinite,
                            fit: BoxFit.cover,
                            isVideo: model.newsType == 'videos' ? true : false),
                      ),
                      if (model.newsType == 'videos')
                        Positioned.directional(
                          textDirection: Directionality.of(context),
                          top: MediaQuery.of(context).size.height * 0.075,
                          start: MediaQuery.of(context).size.width / 3,
                          end: MediaQuery.of(context).size.width / 3,
                          child: InkWell(
                            onTap: () {
                              Navigator.of(context).pushNamed(Routes.newsVideo, arguments: {"from": 3, "breakModel": data});
                            },
                            child: Container(
                              alignment: Alignment.center,
                              height: 40,
                              width: 40,
                              decoration: BoxDecoration(shape: BoxShape.circle, color: Theme.of(context).primaryColor),
                              child: const Icon(
                                Icons.play_arrow_sharp,
                                size: 25,
                                color: secondaryColor,
                              ),
                            ),
                          ),
                        ),
                      Positioned.directional(
                          textDirection: Directionality.of(context),
                          start: 8,
                          end: 8,
                          top: MediaQuery.of(context).size.height / 7,
                          child: Container(
                            alignment: Alignment.center,
                            height: MediaQuery.of(context).size.height / 5,
                            width: MediaQuery.of(context).size.width,
                            margin: const EdgeInsetsDirectional.all(10),
                            decoration: BoxDecoration(borderRadius: BorderRadius.circular(15), color: UiUtils.getColorScheme(context).background),
                            padding: const EdgeInsets.all(13),
                            child: Text(data.title!,
                                style: Theme.of(context).textTheme.titleMedium!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.normal),
                                softWrap: true,
                                maxLines: 4,
                                overflow: TextOverflow.ellipsis),
                          )),
                    ],
                  ),
                ),
              );
            },
          ),
        ),
        style1Indicator(model, model.breakNews!.length)
      ],
    );
  }

  Widget style1Indicator(FeatureSectionModel model, int len) {
    return len <= 1
        ? const SizedBox.shrink()
        : Align(
            alignment: Alignment.center,
            child: Padding(
                padding: const EdgeInsets.only(top: 10),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: map<Widget>(
                    (model.newsType == 'news' || model.newsType == "user_choice")
                        ? model.news!
                        : model.newsType == 'videos'
                            ? model.videos!
                            : model.breakNews!,
                    (index, url) {
                      return Container(
                        alignment: Alignment.center,
                        child: Padding(
                          padding: const EdgeInsetsDirectional.only(start: 5.0, end: 5.0),
                          child: Container(
                            height: 14.0,
                            width: 14.0,
                            decoration: BoxDecoration(color: Colors.transparent, shape: BoxShape.circle, border: Border.all(color: UiUtils.getColorScheme(context).primaryContainer)),
                            child: style1Sel == index
                                ? Container(
                                    margin: const EdgeInsets.all(2),
                                    decoration: BoxDecoration(
                                      color: Theme.of(context).primaryColor,
                                      shape: BoxShape.circle,
                                    ),
                                  )
                                : const SizedBox.shrink(),
                          ),
                        ),
                      );
                    },
                  ),
                )));
  }

  List<T> map<T>(List list, Function handler) {
    List<T> result = [];
    for (var i = 0; i < list.length; i++) {
      result.add(handler(i, list[i]));
    }

    return result;
  }
}
