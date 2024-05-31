// ignore_for_file: file_names

import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:news/ui/screens/HomePage/Widgets/CommonSectionTitle.dart';
import 'package:news/app/routes.dart';
import 'package:news/data/models/BreakingNewsModel.dart';
import 'package:news/data/models/FeatureSectionModel.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/ui/styles/colors.dart';
import 'package:news/ui/widgets/networkImage.dart';

class Style2Section extends StatelessWidget {
  final FeatureSectionModel model;

  const Style2Section({super.key, required this.model});

  @override
  Widget build(BuildContext context) {
    return style2Data(model, context);
  }

  Widget style2Data(FeatureSectionModel model, BuildContext context) {
    if (model.breakVideos!.isNotEmpty || model.breakNews!.isNotEmpty || model.videos!.isNotEmpty || model.news!.isNotEmpty) {
      return Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          commonSectionTitle(model, context),
          if (model.newsType == 'news' || model.videosType == "news" || model.newsType == "user_choice")
            if ((model.newsType == 'news' || model.newsType == "user_choice") ? model.news!.isNotEmpty : model.videos!.isNotEmpty)
              ListView.builder(
                  padding: EdgeInsets.zero,
                  physics: const NeverScrollableScrollPhysics(),
                  shrinkWrap: true,
                  itemCount: (model.newsType == 'news' || model.newsType == "user_choice") ? model.news!.length : model.videos!.length,
                  itemBuilder: (context, index) {
                    NewsModel data = (model.newsType == 'news' || model.newsType == "user_choice") ? model.news![index] : model.videos![index];
                    return Padding(
                      padding: EdgeInsets.only(top: index == 0 ? 0 : 15),
                      child: InkWell(
                        onTap: () {
                          if (model.newsType == 'news' || model.newsType == "user_choice") {
                            List<NewsModel> newsList = [];
                            newsList.addAll(model.news!);
                            newsList.removeAt(index);
                            Navigator.of(context).pushNamed(Routes.newsDetails, arguments: {"model": data, "newsList": newsList, "isFromBreak": false, "fromShowMore": false});
                          }
                        },
                        child: Stack(
                          children: [
                            ClipRRect(
                                borderRadius: BorderRadius.circular(15),
                                child: ShaderMask(
                                  shaderCallback: (rect) {
                                    return LinearGradient(
                                      begin: Alignment.center,
                                      end: Alignment.bottomCenter,
                                      colors: [Colors.transparent, darkSecondaryColor.withOpacity(0.9)],
                                    ).createShader(rect);
                                  },
                                  blendMode: BlendMode.darken,
                                  child: CustomNetworkImage(
                                      networkImageUrl: data.image!,
                                      fit: BoxFit.cover,
                                      width: double.maxFinite,
                                      height: MediaQuery.of(context).size.height / 3.3,
                                      isVideo: model.newsType == 'videos' ? true : false),
                                )),
                            if (model.newsType == 'videos')
                              Positioned.directional(
                                textDirection: Directionality.of(context),
                                top: MediaQuery.of(context).size.height * 0.12,
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
                                bottom: 10,
                                start: 10,
                                end: 10,
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    if (data.categoryName != null)
                                      ClipRRect(
                                        borderRadius: BorderRadius.circular(8.0),
                                        child: Container(
                                          padding: const EdgeInsets.all(5),
                                          child: BackdropFilter(
                                            filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
                                            child: Text(
                                              data.categoryName!,
                                              style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                                                    color: secondaryColor.withOpacity(0.6),
                                                  ),
                                              overflow: TextOverflow.ellipsis,
                                              softWrap: true,
                                            ),
                                          ),
                                        ),
                                      ),
                                    Padding(
                                      padding: const EdgeInsets.only(top: 8),
                                      child: Text(
                                        data.title!,
                                        style: Theme.of(context).textTheme.titleMedium?.copyWith(color: secondaryColor, fontWeight: FontWeight.normal),
                                        maxLines: 2,
                                        overflow: TextOverflow.ellipsis,
                                        softWrap: true,
                                      ),
                                    ),
                                  ],
                                ))
                          ],
                        ),
                      ),
                    );
                  }),
          if (model.newsType == 'breaking_news' || model.videosType == "breaking_news")
            if (model.newsType == 'breaking_news' ? model.breakNews!.isNotEmpty : model.breakVideos!.isNotEmpty)
              ListView.builder(
                  padding: EdgeInsets.zero,
                  physics: const NeverScrollableScrollPhysics(),
                  shrinkWrap: true,
                  itemCount: model.newsType == 'breaking_news' ? model.breakNews!.length : model.breakVideos!.length,
                  itemBuilder: (context, index) {
                    BreakingNewsModel data = model.newsType == 'breaking_news' ? model.breakNews![index] : model.breakVideos![index];
                    return Padding(
                      padding: EdgeInsets.only(top: index == 0 ? 0 : 15),
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
                          children: [
                            ClipRRect(
                                borderRadius: BorderRadius.circular(15),
                                child: ShaderMask(
                                  shaderCallback: (rect) {
                                    return LinearGradient(
                                      begin: Alignment.center,
                                      end: Alignment.bottomCenter,
                                      colors: [Colors.transparent, darkSecondaryColor.withOpacity(0.9)],
                                    ).createShader(rect);
                                  },
                                  blendMode: BlendMode.darken,
                                  child: CustomNetworkImage(
                                      networkImageUrl: data.image!,
                                      fit: BoxFit.cover,
                                      width: double.maxFinite,
                                      height: MediaQuery.of(context).size.height / 3.3,
                                      isVideo: model.newsType == 'videos' ? true : false),
                                )),
                            if (model.newsType == 'videos')
                              Positioned.directional(
                                textDirection: Directionality.of(context),
                                top: MediaQuery.of(context).size.height * 0.12,
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
                                bottom: 10,
                                start: 10,
                                end: 10,
                                child: Padding(
                                  padding: const EdgeInsets.only(top: 8),
                                  child: Text(
                                    data.title!,
                                    style: Theme.of(context).textTheme.titleMedium?.copyWith(color: secondaryColor, fontWeight: FontWeight.normal),
                                    maxLines: 2,
                                    overflow: TextOverflow.ellipsis,
                                    softWrap: true,
                                  ),
                                ))
                          ],
                        ),
                      ),
                    );
                  })
        ],
      );
    } else {
      return const SizedBox.shrink();
    }
  }
}
