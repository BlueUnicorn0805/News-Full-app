// ignore_for_file: file_names, use_build_context_synchronously

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/cubits/tagNewsCubit.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/ui/widgets/customAppBar.dart';
import 'package:news/ui/widgets/errorContainerWidget.dart';
import 'package:news/ui/widgets/networkImage.dart';
import 'package:news/utils/ErrorMessageKeys.dart';
import 'package:news/utils/uiUtils.dart';
import '../../app/routes.dart';
import '../../cubits/Bookmark/UpdateBookmarkCubit.dart';
import '../../cubits/Bookmark/bookmarkCubit.dart';
import '../../cubits/LikeAndDislikeNews/LikeAndDislikeCubit.dart';
import '../../cubits/LikeAndDislikeNews/updateLikeAndDislikeCubit.dart';
import '../../data/repositories/Bookmark/bookmarkRepository.dart';
import '../../data/repositories/LikeAndDisLikeNews/LikeAndDisLikeNewsRepository.dart';
import '../../utils/internetConnectivity.dart';
import '../widgets/SnackBarWidget.dart';
import '../widgets/circularProgressIndicator.dart';
import '../widgets/createDynamicLink.dart';
import '../widgets/loginRequired.dart';
import '../widgets/shimmerNewsList.dart';

class NewsTag extends StatefulWidget {
  final String tagId;
  final String tagName;

  const NewsTag({Key? key, required this.tagId, required this.tagName}) : super(key: key);

  @override
  NewsTagState createState() => NewsTagState();

  static Route route(RouteSettings routeSettings) {
    final arguments = routeSettings.arguments as Map<String, dynamic>;
    return CupertinoPageRoute(
        builder: (_) => NewsTag(
              tagId: arguments['tagId'],
              tagName: arguments['tagName'],
            ));
  }
}

class NewsTagState extends State<NewsTag> {
  @override
  void initState() {
    super.initState();
    getTagWiseNews();
  }

  getTagWiseNews() {
    Future.delayed(Duration.zero, () {
      context.read<TagNewsCubit>().getTagNews(context: context, tagId: widget.tagId, userId: context.read<AuthCubit>().getUserId(), langId: context.read<AppLocalizationCubit>().state.id);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: setCustomAppBar(isConvertText: false, height: 45, isBackBtn: true, label: widget.tagName, context: context, horizontalPad: 15),
      body: viewContent(),
    );
  }

  newsItem(NewsModel model, int index) {
    List<String> tagList = [];
    List<String> tagId = [];

    DateTime time1 = DateTime.parse(model.date!);

    if (model.tagName! != "") {
      final tagName = model.tagName!;
      tagList = tagName.split(',');
    }

    if (model.tagId! != "") {
      tagId = model.tagId!.split(",");
    }

    return Builder(builder: (context) {
      bool isLike = context.read<LikeAndDisLikeCubit>().isNewsLikeAndDisLike(model.id!);
      return Padding(
          padding: EdgeInsetsDirectional.only(top: index == 0 ? 0 : MediaQuery.of(context).size.height / 25.0),
          child: Column(children: <Widget>[
            InkWell(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.start,
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: <Widget>[
                  SizedBox(
                    height: MediaQuery.of(context).size.height / 4.2,
                    child: Stack(
                      alignment: Alignment.bottomLeft,
                      children: [
                        ClipRRect(
                            borderRadius: BorderRadius.circular(10.0),
                            child: CustomNetworkImage(
                              networkImageUrl: model.image!,
                              width: double.maxFinite,
                              height: MediaQuery.of(context).size.height / 4.2,
                              isVideo: false,
                              fit: BoxFit.cover,
                            )),
                        if (model.tagName! != "")
                          Container(
                            margin: const EdgeInsets.only(bottom: 5.0, left: 5.0, right: 5.0),
                            child: SizedBox(
                                height: 16,
                                child: ListView.builder(
                                    scrollDirection: Axis.horizontal,
                                    itemCount: tagList.length,
                                    itemBuilder: (context, index) {
                                      return Padding(
                                          padding: EdgeInsetsDirectional.only(start: index == 0 ? 0 : 5.5),
                                          child: InkWell(
                                            child: Container(
                                                height: 20.0,
                                                width: 65,
                                                alignment: Alignment.center,
                                                padding: const EdgeInsetsDirectional.only(
                                                  start: 3.0,
                                                  end: 3.0,
                                                  top: 1.0,
                                                  bottom: 1.0,
                                                ),
                                                decoration: BoxDecoration(
                                                  borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(10.0), topRight: Radius.circular(10.0)),
                                                  color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.85),
                                                ),
                                                child: Text(
                                                  tagList[index],
                                                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                                                        color: UiUtils.getColorScheme(context).secondary,
                                                        fontSize: 9.5,
                                                      ),
                                                  overflow: TextOverflow.ellipsis,
                                                  softWrap: true,
                                                )),
                                            onTap: () async {
                                              Navigator.of(context).pushNamed(Routes.tagScreen, arguments: {"tagId": tagId[index], "tagName": tagList[index]});
                                            },
                                          ));
                                    })),
                          )
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsetsDirectional.only(top: 4.0, start: 5.0, end: 5.0),
                    child: Text(
                      model.title!,
                      style: Theme.of(context).textTheme.titleSmall?.copyWith(color: Theme.of(context).colorScheme.primaryContainer.withOpacity(0.9)),
                      maxLines: 2,
                      softWrap: true,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  Row(
                    children: [
                      Expanded(
                        child: Padding(
                          padding: const EdgeInsetsDirectional.only(top: 4.0, start: 5.0, end: 5.0),
                          child: Text(UiUtils.convertToAgo(context, time1, 0)!,
                              style: Theme.of(context).textTheme.bodySmall!.copyWith(color: Theme.of(context).colorScheme.primaryContainer.withOpacity(0.6))),
                        ),
                      ),
                      Row(
                        children: [
                          Padding(
                            padding: const EdgeInsetsDirectional.only(start: 13.0),
                            child: InkWell(
                              child: const Icon(Icons.share_rounded),
                              onTap: () async {
                                if (await InternetConnectivity.isNetworkAvailable()) {
                                  createDynamicLink(context: context, id: model.id!, title: model.title!, isVideoId: false, isBreakingNews: false, image: model.image!);
                                } else {
                                  showSnackBar(UiUtils.getTranslatedLabel(context, 'internetmsg'), context);
                                }
                              },
                            ),
                          ),
                          SizedBox(width: MediaQuery.of(context).size.width / 99.0),
                          BlocProvider(
                            create: (context) => UpdateBookmarkStatusCubit(BookmarkRepository()),
                            child: BlocBuilder<BookmarkCubit, BookmarkState>(
                                bloc: context.read<BookmarkCubit>(),
                                builder: (context, bookmarkState) {
                                  bool isBookmark = context.read<BookmarkCubit>().isNewsBookmark(model.id!);
                                  return BlocConsumer<UpdateBookmarkStatusCubit, UpdateBookmarkStatusState>(
                                      bloc: context.read<UpdateBookmarkStatusCubit>(),
                                      listener: ((context, state) {
                                        if (state is UpdateBookmarkStatusSuccess) {
                                          if (state.wasBookmarkNewsProcess) {
                                            context.read<BookmarkCubit>().addBookmarkNews(state.news);
                                          } else {
                                            context.read<BookmarkCubit>().removeBookmarkNews(state.news);
                                          }
                                        }
                                      }),
                                      builder: (context, state) {
                                        return InkWell(
                                            onTap: () {
                                              if (context.read<AuthCubit>().getUserId() != "0") {
                                                if (state is UpdateBookmarkStatusInProgress) {
                                                  return;
                                                }
                                                context.read<UpdateBookmarkStatusCubit>().setBookmarkNews(
                                                      context: context,
                                                      userId: context.read<AuthCubit>().getUserId(),
                                                      news: model,
                                                      status: (isBookmark) ? "0" : "1",
                                                    );
                                              } else {
                                                loginRequired(context);
                                              }
                                            },
                                            child: Padding(
                                                // SAVE
                                                padding: EdgeInsetsDirectional.zero,
                                                child: InkWell(
                                                    child: state is UpdateBookmarkStatusInProgress
                                                        ? SizedBox(
                                                            height: 15,
                                                            width: 15,
                                                            child: showCircularProgress(true, Theme.of(context).primaryColor),
                                                          )
                                                        : Icon(
                                                            isBookmark ? Icons.bookmark_added_rounded : Icons.bookmark_add_outlined,
                                                          ))));
                                      });
                                }),
                          ),
                          SizedBox(width: MediaQuery.of(context).size.width / 99.0),
                          BlocProvider(
                              create: (context) => UpdateLikeAndDisLikeStatusCubit(LikeAndDisLikeRepository()),
                              child: BlocConsumer<LikeAndDisLikeCubit, LikeAndDisLikeState>(
                                  bloc: context.read<LikeAndDisLikeCubit>(),
                                  listener: ((context, state) {
                                    isLike = context.read<LikeAndDisLikeCubit>().isNewsLikeAndDisLike(model.id!);
                                  }),
                                  builder: (context, likeAndDislikeState) {
                                    return BlocConsumer<UpdateLikeAndDisLikeStatusCubit, UpdateLikeAndDisLikeStatusState>(
                                        bloc: context.read<UpdateLikeAndDisLikeStatusCubit>(),
                                        listener: ((context, state) {
                                          if (state is UpdateLikeAndDisLikeStatusSuccess) {
                                            context
                                                .read<LikeAndDisLikeCubit>()
                                                .getLikeAndDisLike(context: context, langId: context.read<AppLocalizationCubit>().state.id, userId: context.read<AuthCubit>().getUserId());
                                            state.news.totalLikes = (!isLike)
                                                ? (int.parse(state.news.totalLikes.toString()) + 1).toString()
                                                : (state.news.totalLikes!.isNotEmpty)
                                                    ? (int.parse(state.news.totalLikes.toString()) - 1).toString()
                                                    : "0";
                                          }
                                        }),
                                        builder: (context, state) {
                                          isLike = context.read<LikeAndDisLikeCubit>().isNewsLikeAndDisLike(model.id!);
                                          return InkWell(
                                              splashColor: Colors.transparent,
                                              onTap: () {
                                                if (context.read<AuthCubit>().getUserId() != "0") {
                                                  if (state is UpdateLikeAndDisLikeStatusInProgress) {
                                                    return;
                                                  }
                                                  context.read<UpdateLikeAndDisLikeStatusCubit>().setLikeAndDisLikeNews(
                                                        context: context,
                                                        userId: context.read<AuthCubit>().getUserId(),
                                                        news: model,
                                                        status: (isLike) ? "0" : "1",
                                                      );
                                                } else {
                                                  loginRequired(context);
                                                }
                                              },
                                              child: (state is UpdateLikeAndDisLikeStatusInProgress)
                                                  ? SizedBox(
                                                      height: 20,
                                                      width: 20,
                                                      child: showCircularProgress(true, Theme.of(context).primaryColor),
                                                    )
                                                  : isLike
                                                      ? const Icon(Icons.thumb_up_alt)
                                                      : const Icon(Icons.thumb_up_off_alt));
                                        });
                                  })),
                        ],
                      ),
                    ],
                  ),
                ],
              ),
              onTap: () {
                //Interstitial Ad here
                if (index % 3 == 0) UiUtils.showInterstitialAds(context: context);
                Navigator.of(context).pushNamed(Routes.newsDetails, arguments: {"model": model, "isFromBreak": false, "fromShowMore": false});
              },
            ),
          ]));
    });
  }

  viewContent() {
    return BlocBuilder<TagNewsCubit, TagNewsState>(builder: (context, state) {
      if (state is TagNewsFetchSuccess) {
        return Padding(
            padding: const EdgeInsetsDirectional.only(top: 10.0, bottom: 10.0, start: 13.0, end: 13.0),
            child: ListView.builder(
                itemCount: state.tagNews.length,
                shrinkWrap: true,
                padding: const EdgeInsets.only(bottom: 20),
                physics: const AlwaysScrollableScrollPhysics(),
                itemBuilder: ((context, index) {
                  return newsItem((state).tagNews[index], index);
                })));
      }
      if (state is TagNewsFetchFailure) {
        return ErrorContainerWidget(
            errorMsg: (state.errorMessage.contains(ErrorMessageKeys.noInternet)) ? UiUtils.getTranslatedLabel(context, 'internetmsg') : state.errorMessage, onRetry: getTagWiseNews);
      }
      return shimmerNewsList(context);
    });
  }
}
