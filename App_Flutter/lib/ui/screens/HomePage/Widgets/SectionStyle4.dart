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

class Style4Section extends StatelessWidget {
  final FeatureSectionModel model;

  const Style4Section({super.key, required this.model});

  @override
  Widget build(BuildContext context) {
    return style4Data(model, context);
  }

  Widget style4Data(FeatureSectionModel model, BuildContext context) {
    if (model.breakVideos!.isNotEmpty || model.breakNews!.isNotEmpty || model.videos!.isNotEmpty || model.news!.isNotEmpty) {
      return Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          commonSectionTitle(model, context),
          if (model.newsType == 'news' || model.videosType == "news" || model.newsType == "user_choice")
            if ((model.newsType == 'news' || model.newsType == "user_choice") ? model.news!.isNotEmpty : model.videos!.isNotEmpty)
              GridView.builder(
                  padding: EdgeInsets.zero,
                  gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(mainAxisExtent: MediaQuery.of(context).size.height * 0.29, crossAxisCount: 2, crossAxisSpacing: 10, mainAxisSpacing: 13),
                  shrinkWrap: true,
                  itemCount: (model.newsType == 'news' || model.newsType == "user_choice") ? model.news!.length : model.videos!.length,
                  physics: const NeverScrollableScrollPhysics(),
                  itemBuilder: (context, index) {
                    NewsModel data = (model.newsType == 'news' || model.newsType == "user_choice") ? model.news![index] : model.videos![index];
                    return InkWell(
                      child: Container(
                        padding: const EdgeInsets.all(7),
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(10),
                          color: UiUtils.getColorScheme(context).background,
                        ),
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Stack(children: [
                              ClipRRect(
                                borderRadius: BorderRadius.circular(10),
                                child: CustomNetworkImage(
                                    networkImageUrl: data.image!,
                                    height: MediaQuery.of(context).size.height * 0.175,
                                    width: MediaQuery.of(context).size.width,
                                    fit: BoxFit.cover,
                                    isVideo: model.newsType == 'videos' ? true : false),
                              ),
                              if (data.categoryName != "")
                                Align(
                                  alignment: Alignment.topLeft,
                                  child: Container(
                                      margin: const EdgeInsetsDirectional.only(start: 7.0, top: 7.0),
                                      height: 18.0,
                                      padding: const EdgeInsetsDirectional.only(start: 6.0, end: 6.0),
                                      decoration: BoxDecoration(borderRadius: BorderRadius.circular(5), color: Theme.of(context).primaryColor),
                                      child: Text(
                                        data.categoryName!,
                                        textAlign: TextAlign.center,
                                        style: Theme.of(context).textTheme.bodySmall?.copyWith(
                                              color: secondaryColor,
                                            ),
                                        overflow: TextOverflow.ellipsis,
                                        softWrap: true,
                                      )),
                                ),
                              if (model.newsType == 'videos')
                                Positioned.directional(
                                  textDirection: Directionality.of(context),
                                  top: MediaQuery.of(context).size.height * 0.065,
                                  start: MediaQuery.of(context).size.width / 6,
                                  end: MediaQuery.of(context).size.width / 6,
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
                            ]),
                            Padding(
                              padding: const EdgeInsets.only(top: 8.0),
                              child: Text(data.title!,
                                  style: Theme.of(context).textTheme.titleSmall!.copyWith(
                                        color: UiUtils.getColorScheme(context).primaryContainer,
                                      ),
                                  softWrap: true,
                                  maxLines: 3,
                                  overflow: TextOverflow.ellipsis),
                            ),
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
                  }),
          if (model.newsType == 'breaking_news' || model.videosType == "breaking_news")
            if (model.newsType == 'breaking_news' ? model.breakNews!.isNotEmpty : model.breakVideos!.isNotEmpty)
              GridView.builder(
                  padding: EdgeInsets.zero,
                  gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(mainAxisExtent: MediaQuery.of(context).size.height * 0.29, crossAxisCount: 2, crossAxisSpacing: 10, mainAxisSpacing: 13),
                  shrinkWrap: true,
                  itemCount: model.newsType == 'breaking_news' ? model.breakNews!.length : model.breakVideos!.length,
                  physics: const NeverScrollableScrollPhysics(),
                  itemBuilder: (context, index) {
                    BreakingNewsModel data = model.newsType == 'breaking_news' ? model.breakNews![index] : model.breakVideos![index];
                    return InkWell(
                      child: Container(
                        padding: const EdgeInsets.all(7),
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(10),
                          color: UiUtils.getColorScheme(context).background,
                        ),
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Stack(children: [
                              ClipRRect(
                                borderRadius: BorderRadius.circular(10),
                                child: CustomNetworkImage(
                                    networkImageUrl: data.image!,
                                    height: MediaQuery.of(context).size.height * 0.175,
                                    width: MediaQuery.of(context).size.width,
                                    fit: BoxFit.cover,
                                    isVideo: model.newsType == 'videos' ? true : false),
                              ),
                              if (model.newsType == 'videos')
                                Positioned.directional(
                                  textDirection: Directionality.of(context),
                                  top: MediaQuery.of(context).size.height * 0.065,
                                  start: MediaQuery.of(context).size.width / 6,
                                  end: MediaQuery.of(context).size.width / 6,
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
                            ]),
                            Padding(
                              padding: const EdgeInsets.only(top: 8.0),
                              child: Text(data.title!,
                                  style: Theme.of(context).textTheme.titleSmall!.copyWith(
                                        color: UiUtils.getColorScheme(context).primaryContainer,
                                      ),
                                  softWrap: true,
                                  maxLines: 3,
                                  overflow: TextOverflow.ellipsis),
                            ),
                          ],
                        ),
                      ),
                      onTap: () {
                        if (model.newsType == 'breaking_news') {
                          List<BreakingNewsModel> breakList = [];
                          breakList.addAll(model.breakNews!);
                          breakList.removeAt(index);
                          Navigator.of(context).pushNamed(Routes.newsDetails, arguments: {"breakModel": data, "breakNewsList": breakList, "isFromBreak": true, "fromShowMore": false});
                        }
                      },
                    );
                  }),
        ],
      );
    } else {
      return const SizedBox.shrink();
    }
  }
}
