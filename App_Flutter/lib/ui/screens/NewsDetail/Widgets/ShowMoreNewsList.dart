// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/cubits/relatedNewsCubit.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/ui/widgets/NewsItem.dart';
import 'package:news/ui/widgets/errorContainerWidget.dart';
import 'package:news/ui/widgets/shimmerNewsList.dart';
import 'package:news/utils/ErrorMessageKeys.dart';
import '../../../../cubits/Auth/authCubit.dart';
import '../../../../cubits/appLocalizationCubit.dart';
import '../../../../utils/uiUtils.dart';
import '../../../widgets/circularProgressIndicator.dart';
import '../../../widgets/customAppBar.dart';

class ShowMoreNewsList extends StatefulWidget {
  final NewsModel model;

  const ShowMoreNewsList({Key? key, required this.model}) : super(key: key);

  @override
  ShowMoreNewsListState createState() => ShowMoreNewsListState();

  static Route route(RouteSettings routeSettings) {
    final arguments = routeSettings.arguments as Map<String, dynamic>;
    return CupertinoPageRoute(
        builder: (_) => ShowMoreNewsList(
              model: arguments['model'],
            ));
  }
}

class ShowMoreNewsListState extends State<ShowMoreNewsList> {
  late final ScrollController controller = ScrollController()..addListener(hasMoreRelatedNewsScrollListener);

  void hasMoreRelatedNewsScrollListener() {
    if (controller.position.maxScrollExtent == controller.offset) {
      if (context.read<RelatedNewsCubit>().hasMoreRelatedNews()) {
        context.read<RelatedNewsCubit>().getMoreRelatedNews(
            userId: context.read<AuthCubit>().getUserId(),
            langId: context.read<AppLocalizationCubit>().state.id,
            catId: widget.model.subCatId == "0" || widget.model.subCatId == '' ? widget.model.categoryId : null,
            subCatId: widget.model.subCatId != "0" || widget.model.subCatId != '' ? widget.model.subCatId : null);
      } else {
        debugPrint("No more RelatedNews");
      }
    }
  }

  _buildRelatedNewsContainer(
      {required NewsModel model, required int index, required int totalCurrentRelatedNews, required bool hasMoreRelatedNewsFetchError, required bool hasMore, required List<NewsModel> newsList}) {
    if (index == totalCurrentRelatedNews - 1 && index != 0) {
      //check if hasMore
      if (hasMore) {
        if (hasMoreRelatedNewsFetchError) {
          return Center(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 15.0, vertical: 8.0),
              child: IconButton(
                  onPressed: () {
                    context.read<RelatedNewsCubit>().getMoreRelatedNews(
                        userId: context.read<AuthCubit>().getUserId(),
                        langId: context.read<AppLocalizationCubit>().state.id,
                        catId: widget.model.subCatId == "0" || widget.model.subCatId == '' ? widget.model.categoryId : null,
                        subCatId: widget.model.subCatId != "0" || widget.model.subCatId != '' ? widget.model.subCatId : null);
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

    return NewsItem(
      model: model,
      index: index,
      newslist: newsList,
      fromShowMore: true,
    );
  }

  void refreshNewsList() {
    context.read<RelatedNewsCubit>().getRelatedNews(
        userId: context.read<AuthCubit>().getUserId(),
        langId: context.read<AppLocalizationCubit>().state.id,
        catId: widget.model.subCatId == "0" || widget.model.subCatId == '' ? widget.model.categoryId : null,
        subCatId: widget.model.subCatId != "0" || widget.model.subCatId != '' ? widget.model.subCatId : null);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: setCustomAppBar(height: 45, isBackBtn: true, label: 'relatedNews', context: context, horizontalPad: 15, isConvertText: true),
      body: BlocBuilder<RelatedNewsCubit, RelatedNewsState>(
        builder: (context, state) {
          if (state is RelatedNewsFetchSuccess) {
            return RefreshIndicator(
              onRefresh: () async {
                refreshNewsList();
              },
              child: ListView.builder(
                  controller: controller,
                  physics: const AlwaysScrollableScrollPhysics(),
                  shrinkWrap: true,
                  itemCount: state.relatedNews.length,
                  itemBuilder: (context, index) {
                    return _buildRelatedNewsContainer(
                        model: state.relatedNews[index],
                        hasMore: state.hasMore,
                        hasMoreRelatedNewsFetchError: state.hasMoreFetchError,
                        index: index,
                        totalCurrentRelatedNews: state.relatedNews.length,
                        newsList: state.relatedNews);
                  }),
            );
          }

          if (state is RelatedNewsFetchFailure) {
            return ErrorContainerWidget(
                errorMsg: (state.errorMessage.contains(ErrorMessageKeys.noInternet)) ? UiUtils.getTranslatedLabel(context, 'internetmsg') : state.errorMessage, onRetry: refreshNewsList);
          }
          //state is RelatedNewsInitial || state is RelatedNewsFetchInProgress
          return Padding(padding: const EdgeInsets.only(bottom: 10.0, left: 10.0, right: 10.0), child: shimmerNewsList(context));
        },
      ),
    );
  }
}
