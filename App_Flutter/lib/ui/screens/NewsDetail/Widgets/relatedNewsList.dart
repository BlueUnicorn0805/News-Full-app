// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/cubits/relatedNewsCubit.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/ui/widgets/networkImage.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/app/routes.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/ui/styles/colors.dart';

class RelatedNewsList extends StatefulWidget {
  final NewsModel model;

  const RelatedNewsList({
    Key? key,
    required this.model,
  }) : super(key: key);

  @override
  RelatedNewsListState createState() => RelatedNewsListState();
}

class RelatedNewsListState extends State<RelatedNewsList> {
  int sliderIndex = 0;
  List<NewsModel> relatedList = [];

  Widget getRelatedList() {
    return BlocConsumer<RelatedNewsCubit, RelatedNewsState>(
        bloc: context.read<RelatedNewsCubit>(),
        listener: (context, state) {
          if (state is RelatedNewsFetchSuccess) {
            setState(() {
              relatedList.clear();
              relatedList.addAll(state.relatedNews);
              relatedList.removeWhere((element) => element.id == widget.model.id!);
            });
          }
        },
        builder: (context, state) {
          if (state is RelatedNewsFetchSuccess && relatedList.isNotEmpty) {
            return Padding(
                padding: const EdgeInsetsDirectional.only(
                  top: 15.0,
                  bottom: 15.0,
                ),
                child: Column(children: [
                  Align(
                      alignment: Alignment.topLeft,
                      child: Padding(
                        padding: const EdgeInsetsDirectional.only(bottom: 15.0),
                        child: CustomTextLabel(
                            text: 'relatedNews', textStyle: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.w600)),
                      )),
                  showCoverageNews(relatedList)
                ]));
          }
          return const SizedBox.shrink(); //state is RelatedNewsFetchInProgress || state is RelatedNewsInitial || state is RelatedNewsFetchFailure
        });
  }

  Widget relatedNewsData(NewsModel model, List<NewsModel> newsList) {
    return Stack(
      children: [
        ClipRRect(
            borderRadius: const BorderRadius.only(topLeft: Radius.circular(15.0), topRight: Radius.circular(15.0)),
            child: ShaderMask(
              shaderCallback: (rect) {
                return const LinearGradient(
                  begin: Alignment.center,
                  end: Alignment.bottomCenter,
                  colors: [Colors.transparent, secondaryColor],
                ).createShader(rect);
              },
              blendMode: BlendMode.darken,
              child: GestureDetector(
                child: CustomNetworkImage(
                  networkImageUrl: model.image!,
                  width: double.maxFinite,
                  height: MediaQuery.of(context).size.height / 2.9,
                  isVideo: false,
                  fit: BoxFit.cover,
                ),
                onTap: () {
                  List<NewsModel> addNewsList = [];
                  addNewsList.addAll(newsList);
                  addNewsList.remove(model);
                  //Interstitial Ad here
                  UiUtils.showInterstitialAds(context: context);
                  Navigator.of(context).pushNamed(Routes.newsDetails, arguments: {"model": model, "newsList": addNewsList, "isFromBreak": false, "fromShowMore": false});
                },
              ),
            )),
        Align(
            alignment: Alignment.bottomCenter,
            child: Container(
                padding: EdgeInsetsDirectional.only(bottom: MediaQuery.of(context).size.height / 18.9, start: MediaQuery.of(context).size.width / 20.0, end: 5.0),
                width: MediaQuery.of(context).size.width,
                child: Text(
                  model.title!,
                  style: Theme.of(context).textTheme.titleSmall?.copyWith(color: secondaryColor, fontWeight: FontWeight.normal, fontSize: 12.5, height: 1.0),
                  maxLines: 1,
                  softWrap: true,
                  overflow: TextOverflow.ellipsis,
                ))),
      ],
    );
  }

  List<T> map<T>(List list, Function handler) {
    List<T> result = [];
    for (var i = 0; i < list.length; i++) {
      result.add(handler(i, list[i]));
    }

    return result;
  }

  CustomTextLabel setCoverageText(BuildContext context) {
    return CustomTextLabel(
      text: 'viewFullCoverage',
      textAlign: TextAlign.center,
      textStyle: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.9), fontWeight: FontWeight.w600),
    );
  }

  Icon setCoverageIcon(BuildContext context) {
    return Icon(Icons.image, color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.9));
  }

  Widget showCoverageNews(List<NewsModel> relatedNews) {
    return Column(
      children: [
        Stack(
          children: [
            SizedBox(
              height: MediaQuery.of(context).size.height / 2.9,
              width: double.infinity,
              child: PageView.builder(
                itemCount: relatedNews.length,
                scrollDirection: Axis.horizontal,
                physics: const AlwaysScrollableScrollPhysics(),
                onPageChanged: (index) {
                  setState(() {
                    sliderIndex = index;
                  });
                },
                itemBuilder: (BuildContext context, int index) {
                  return relatedNewsData(relatedNews[index], relatedNews);
                },
              ),
            ),
            Align(
                alignment: Alignment.bottomCenter,
                child: Padding(
                  padding: EdgeInsetsDirectional.only(top: MediaQuery.of(context).size.height / 3.3),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    mainAxisAlignment: MainAxisAlignment.center,
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: map<Widget>(
                      relatedNews,
                      (index, url) {
                        return AnimatedContainer(
                            duration: const Duration(milliseconds: 500),
                            width: sliderIndex == index ? MediaQuery.of(context).size.width / 15.0 : MediaQuery.of(context).size.width / 15.0,
                            height: 5.0,
                            margin: const EdgeInsets.symmetric(vertical: 10.0, horizontal: 2.0),
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(5.0),
                              color: sliderIndex == index ? Theme.of(context).primaryColor : darkBackgroundColor.withOpacity(0.5),
                            ));
                      },
                    ),
                  ),
                )),
          ],
        ),
        Container(
            height: 38.0,
            alignment: Alignment.center,
            decoration: BoxDecoration(
              borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(15.0), bottomRight: Radius.circular(15.0)),
              color: UiUtils.getColorScheme(context).background,
              boxShadow: [BoxShadow(color: borderColor.withOpacity(0.4), offset: const Offset(0.0, 2.0), blurRadius: 6.0, spreadRadius: 0)],
            ),
            child: ElevatedButton.icon(
              icon: setCoverageIcon(context),
              label: setCoverageText(context),
              onPressed: () {
                Navigator.of(context).pushNamed(Routes.showMoreRelatedNews, arguments: {
                  "model": widget.model,
                });
              },
              style: ElevatedButton.styleFrom(
                foregroundColor: Theme.of(context).primaryColor,
                backgroundColor: Colors.transparent,
                shadowColor: Colors.transparent,
              ),
            )),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return getRelatedList();
  }
}
