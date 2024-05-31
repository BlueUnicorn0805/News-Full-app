// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/ui/widgets/errorContainerWidget.dart';
import 'package:news/utils/ErrorMessageKeys.dart';
import 'package:shimmer/shimmer.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/cubits/deleteUserNewsCubit.dart';
import 'package:news/cubits/getUserNewsCubit.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/app/routes.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/ui/widgets/networkImage.dart';
import 'package:news/ui/widgets/SnackBarWidget.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';

class ShowNews extends StatefulWidget {
  const ShowNews({super.key});

  @override
  ShowNewsState createState() => ShowNewsState();
}

class ShowNewsState extends State<ShowNews> {
  final bool _isButtonExtended = true;
  late final ScrollController controller = ScrollController()..addListener(hasMoreNewsScrollListener);

  @override
  void initState() {
    getNews();
    super.initState();
  }

  @override
  void dispose() {
    controller.dispose();
    super.dispose();
  }

  void getNews() {
    context.read<GetUserNewsCubit>().getGetUserNews(userId: context.read<AuthCubit>().getUserId(), langId: context.read<AppLocalizationCubit>().state.id);
  }

  void hasMoreNewsScrollListener() {
    if (controller.position.maxScrollExtent == controller.offset) {
      if (context.read<GetUserNewsCubit>().hasMoreGetUserNews()) {
        context.read<GetUserNewsCubit>().getMoreGetUserNews(userId: context.read<AuthCubit>().getUserId(), langId: context.read<AppLocalizationCubit>().state.id);
      } else {
        debugPrint("No more News for this user");
      }
    }
  }

  //set appbar
  getAppBar() {
    return PreferredSize(
        preferredSize: const Size(double.infinity, 45),
        child: AppBar(
          centerTitle: false,
          backgroundColor: Colors.transparent,
          title: Transform(
            transform: Matrix4.translationValues(-20.0, 0.0, 0.0),
            child: Text(
              UiUtils.getTranslatedLabel(context, 'manageNewsLbl'),
              style: Theme.of(context).textTheme.titleLarge?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.w600, letterSpacing: 0.5),
            ),
          ),
          leading: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 10.0),
            child: InkWell(
              onTap: () {
                Navigator.of(context).pop();
              },
              splashColor: Colors.transparent,
              highlightColor: Colors.transparent,
              child: Icon(Icons.arrow_back, color: UiUtils.getColorScheme(context).primaryContainer),
            ),
          ),
        ));
  }

  floatingBtn() {
    return Column(
      mainAxisAlignment: MainAxisAlignment.end,
      children: [
        FloatingActionButton(
          isExtended: _isButtonExtended,
          backgroundColor: UiUtils.getColorScheme(context).background,
          child: Icon(
            Icons.add,
            size: 32,
            color: UiUtils.getColorScheme(context).primaryContainer,
          ),
          onPressed: () {
            Navigator.of(context).pushNamed(Routes.addNews, arguments: {"isEdit": false, "from": "myNews"});
          },
        ),
        const SizedBox(height: 10),
      ],
    );
  }

  _buildNewsContainer({
    required NewsModel model,
    required int index,
    required int totalCurrentNews,
    required bool hasMoreNewsFetchError,
    required bool hasMore,
  }) {
    if (index == totalCurrentNews - 1 && index != 0) {
      //check if hasMore
      if (hasMore) {
        if (hasMoreNewsFetchError) {
          return Center(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 15.0, vertical: 8.0),
              child: IconButton(
                  onPressed: () {
                    context.read<GetUserNewsCubit>().getMoreGetUserNews(userId: context.read<AuthCubit>().getUserId(), langId: context.read<AppLocalizationCubit>().state.id);
                  },
                  icon: Icon(
                    Icons.error,
                    color: Theme.of(context).primaryColor,
                  )),
            ),
          );
        } else {
          return Center(child: Padding(padding: const EdgeInsets.symmetric(horizontal: 15.0, vertical: 8.0), child: showCircularProgress(true, Theme.of(context).primaryColor)));
        }
      }
    }
    DateTime time1 = DateTime.parse(model.date!);
    List<String> tagList = [];

    if (model.tagName! != "") {
      final tagName = model.tagName!;
      tagList = tagName.split(',');
    }

    List<String> tagId = [];
    if (model.tagId! != "") {
      tagId = model.tagId!.split(",");
    }

    String contType = "";
    if (model.contentType == "standard_post") {
      contType = UiUtils.getTranslatedLabel(context, 'stdPostLbl');
    } else if (model.contentType == "video_youtube") {
      contType = UiUtils.getTranslatedLabel(context, 'videoYoutubeLbl');
    } else if (model.contentType == "video_other") {
      contType = UiUtils.getTranslatedLabel(context, 'videoOtherUrlLbl');
    } else if (model.contentType == "video_upload") {
      contType = UiUtils.getTranslatedLabel(context, 'videoUploadLbl');
    }

    return InkWell(
      onTap: () {
        Navigator.of(context).pushNamed(Routes.newsDetails, arguments: {"model": model, "isFromBreak": false, "fromShowMore": false});
      },
      child: Container(
        decoration: BoxDecoration(borderRadius: BorderRadius.circular(10.0), color: UiUtils.getColorScheme(context).background),
        padding: const EdgeInsetsDirectional.all(15),
        margin: const EdgeInsets.only(top: 20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                SizedBox(
                  width: MediaQuery.of(context).size.width * 0.24,
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      ClipRRect(
                          borderRadius: BorderRadius.circular(10),
                          child: CustomNetworkImage(
                            networkImageUrl: model.image!,
                            fit: BoxFit.cover,
                            height: MediaQuery.of(context).size.width * 0.23,
                            isVideo: false,
                            width: MediaQuery.of(context).size.width * 0.23,
                          )),
                      Padding(
                        padding: const EdgeInsets.only(top: 4),
                        child: Text(model.categoryName!,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            softWrap: true,
                            style: Theme.of(context).textTheme.bodyMedium!.copyWith(
                                  color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.8),
                                  fontWeight: FontWeight.w600,
                                  fontStyle: FontStyle.normal,
                                )),
                      ),
                    ],
                  ),
                ),
                Expanded(
                    child: Padding(
                  padding: const EdgeInsetsDirectional.only(start: 15),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(model.title!,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          softWrap: true,
                          style: Theme.of(context).textTheme.titleLarge!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer)),
                      Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                        Expanded(
                          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                            if (model.subCatName != "")
                              Padding(
                                padding: const EdgeInsets.only(top: 7),
                                child: Text(UiUtils.getTranslatedLabel(context, 'subcatLbl'),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    softWrap: true,
                                    style: Theme.of(context).textTheme.bodyLarge!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.8))),
                              ),
                            if (model.contentType != "")
                              Padding(
                                padding: const EdgeInsets.only(top: 7),
                                child: Text(UiUtils.getTranslatedLabel(context, 'contentTypeLbl'),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    softWrap: true,
                                    style: Theme.of(context).textTheme.bodyLarge!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer)),
                              ),
                          ]),
                        ),
                        Expanded(
                          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                            if (model.subCatName != "")
                              Padding(
                                padding: const EdgeInsets.only(top: 7),
                                child: Text(model.subCatName!,
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    softWrap: true,
                                    style: Theme.of(context).textTheme.bodyMedium!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7))),
                              ),
                            if (model.contentType != "")
                              Padding(
                                padding: const EdgeInsets.only(top: 7),
                                child: Text(contType,
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    softWrap: true,
                                    style: Theme.of(context).textTheme.bodyMedium!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7))),
                              ),
                          ]),
                        )
                      ]),
                      model.tagName! != ""
                          ? Container(
                              margin: const EdgeInsets.only(top: 7),
                              height: 18.0,
                              child: ListView.builder(
                                  physics: const AlwaysScrollableScrollPhysics(),
                                  scrollDirection: Axis.horizontal,
                                  shrinkWrap: true,
                                  itemCount: tagList.length,
                                  itemBuilder: (context, index) {
                                    return Padding(
                                        padding: EdgeInsetsDirectional.only(start: index == 0 ? 0 : 5.5),
                                        child: InkWell(
                                          child: Container(
                                              height: 20.0,
                                              width: 65,
                                              alignment: Alignment.center,
                                              padding: const EdgeInsetsDirectional.only(start: 3.0, end: 3.0),
                                              decoration: BoxDecoration(
                                                  borderRadius: const BorderRadius.only(topLeft: Radius.circular(10.0), bottomRight: Radius.circular(10.0)),
                                                  color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.2)),
                                              child: Text(
                                                tagList[index],
                                                style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                                                      color: UiUtils.getColorScheme(context).primaryContainer,
                                                      fontSize: 9.5,
                                                    ),
                                                overflow: TextOverflow.ellipsis,
                                                softWrap: true,
                                              )),
                                          onTap: () {
                                            Navigator.of(context).pushNamed(Routes.tagScreen, arguments: {"tagId": tagId[index], "tagName": tagList[index]});
                                          },
                                        ));
                                  }))
                          : const SizedBox.shrink(),
                      Padding(
                        padding: const EdgeInsets.only(top: 15),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceAround,
                          children: [
                            InkWell(
                              child: Container(
                                alignment: Alignment.center,
                                width: MediaQuery.of(context).size.width * 0.20,
                                height: 25,
                                padding: const EdgeInsetsDirectional.only(top: 3, bottom: 3),
                                decoration: BoxDecoration(color: UiUtils.getColorScheme(context).primaryContainer, borderRadius: BorderRadius.circular(3)),
                                child: Text(UiUtils.getTranslatedLabel(context, 'editLbl'),
                                    overflow: TextOverflow.ellipsis,
                                    softWrap: true,
                                    style: Theme.of(context).textTheme.bodyMedium!.copyWith(color: UiUtils.getColorScheme(context).background, fontWeight: FontWeight.w600)),
                              ),
                              onTap: () {
                                Navigator.of(context).pushNamed(Routes.addNews, arguments: {"model": model, "isEdit": true, "from": "myNews"});
                              },
                            ),
                            InkWell(
                              child: Container(
                                width: MediaQuery.of(context).size.width * 0.20,
                                height: 25,
                                padding: const EdgeInsetsDirectional.only(top: 3, bottom: 3),
                                alignment: Alignment.center,
                                decoration: BoxDecoration(color: UiUtils.getColorScheme(context).primaryContainer, borderRadius: BorderRadius.circular(3)),
                                child: Text(UiUtils.getTranslatedLabel(context, 'deleteTxt'),
                                    overflow: TextOverflow.ellipsis,
                                    softWrap: true,
                                    style: Theme.of(context).textTheme.bodyMedium!.copyWith(color: UiUtils.getColorScheme(context).background, fontWeight: FontWeight.w600)),
                              ),
                              onTap: () {
                                deleteNewsDialogue(model.id!, index);
                              },
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                )),
              ],
            ),
            Text(UiUtils.convertToAgo(context, time1, 0)!,
                overflow: TextOverflow.ellipsis, softWrap: true, style: Theme.of(context).textTheme.bodySmall!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.8)))
          ],
        ),
      ),
    );
  }

  //set Delete dialogue
  deleteNewsDialogue(String id, int index) async {
    await showDialog(
        context: context,
        builder: (BuildContext context) {
          return StatefulBuilder(builder: (BuildContext context, StateSetter setStater) {
            return AlertDialog(
              backgroundColor: UiUtils.getColorScheme(context).background,
              shape: const RoundedRectangleBorder(borderRadius: BorderRadius.all(Radius.circular(5.0))),
              content: Text(
                UiUtils.getTranslatedLabel(context, 'doYouReallyNewsLbl'),
                style: Theme.of(this.context).textTheme.titleMedium,
              ),
              title: Text(UiUtils.getTranslatedLabel(context, 'delNewsLbl')),
              titleTextStyle: Theme.of(this.context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w600),
              actions: <Widget>[
                TextButton(
                    child: Text(
                      UiUtils.getTranslatedLabel(context, 'noLbl'),
                      style: Theme.of(this.context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.bold),
                    ),
                    onPressed: () {
                      Navigator.of(context).pop(false);
                    }),
                BlocConsumer<DeleteUserNewsCubit, DeleteUserNewsState>(
                    bloc: context.read<DeleteUserNewsCubit>(),
                    listener: (context, state) {
                      if (state is DeleteUserNewsSuccess) {
                        context.read<GetUserNewsCubit>().deleteNews(index);
                        showSnackBar(state.message, context);
                        Navigator.pop(context);
                      }
                    },
                    builder: (context, state) {
                      return TextButton(
                          child: Text(
                            UiUtils.getTranslatedLabel(context, 'yesLbl'),
                            style: Theme.of(this.context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.bold),
                          ),
                          onPressed: () async {
                            context.read<DeleteUserNewsCubit>().setDeleteUserNews(newsId: id);
                          });
                    })
              ],
            );
          });
        });
  }

  contentShimmer(BuildContext context) {
    return Shimmer.fromColors(
        baseColor: Colors.grey.withOpacity(0.6),
        highlightColor: Colors.grey,
        child: ListView.builder(
          shrinkWrap: true,
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsetsDirectional.only(start: 20, end: 20),
          itemBuilder: (_, i) => Container(
            decoration: BoxDecoration(borderRadius: BorderRadius.circular(10.0), color: Colors.grey.withOpacity(0.6)),
            margin: const EdgeInsets.only(top: 20),
            height: 170.0,
          ),
          itemCount: 6,
        ));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: getAppBar(),
        floatingActionButton: floatingBtn(),
        body: BlocBuilder<GetUserNewsCubit, GetUserNewsState>(
          builder: (context, state) {
            if (state is GetUserNewsFetchSuccess) {
              return Padding(
                padding: const EdgeInsetsDirectional.all(10),
                child: RefreshIndicator(
                  onRefresh: () async {
                    context.read<GetUserNewsCubit>().getGetUserNews(userId: context.read<AuthCubit>().getUserId(), langId: context.read<AppLocalizationCubit>().state.id);
                  },
                  child: ListView.builder(
                      controller: controller,
                      physics: const AlwaysScrollableScrollPhysics(),
                      shrinkWrap: true,
                      itemCount: state.getUserNews.length,
                      itemBuilder: (context, index) {
                        return _buildNewsContainer(
                          model: state.getUserNews[index],
                          hasMore: state.hasMore,
                          hasMoreNewsFetchError: state.hasMoreFetchError,
                          index: index,
                          totalCurrentNews: state.getUserNews.length,
                        );
                      }),
                ),
              );
            }
            if (state is GetUserNewsFetchFailure) {
              return ErrorContainerWidget(
                  errorMsg: (state.errorMessage.contains(ErrorMessageKeys.noInternet)) ? UiUtils.getTranslatedLabel(context, 'internetmsg') : state.errorMessage, onRetry: getNews);
            }
            return contentShimmer(context);
          },
        ));
  }
}
