// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/ui/widgets/breakingVideoItem.dart';
import 'package:news/ui/widgets/errorContainerWidget.dart';
import 'package:news/ui/widgets/shimmerNewsList.dart';
import 'package:news/utils/ErrorMessageKeys.dart';
import 'package:news/utils/uiUtils.dart';
import '../../../cubits/Auth/authCubit.dart';
import '../../../cubits/appLocalizationCubit.dart';
import '../../../cubits/sectionByIdCubit.dart';
import '../../../data/models/BreakingNewsModel.dart';
import '../../widgets/breakingNewsItem.dart';
import '../../widgets/circularProgressIndicator.dart';
import '../../widgets/customAppBar.dart';

class SectionMoreBreakingNewsList extends StatefulWidget {
  final String sectionId;
  final String title;

  const SectionMoreBreakingNewsList({
    Key? key,
    required this.sectionId,
    required this.title,
  }) : super(key: key);

  @override
  State<StatefulWidget> createState() {
    return _SectionBreakingNewsState();
  }

  static Route route(RouteSettings routeSettings) {
    final arguments = routeSettings.arguments as Map<String, dynamic>;
    return CupertinoPageRoute(
        builder: (_) => SectionMoreBreakingNewsList(
              sectionId: arguments['sectionId'],
              title: arguments['title'],
            ));
  }
}

class _SectionBreakingNewsState extends State<SectionMoreBreakingNewsList> {
  late final ScrollController controller = ScrollController()..addListener(hasMoreSectionIdDataScrollListener);

  @override
  void initState() {
    getSectionByData();
    super.initState();
  }

  void getSectionByData() {
    Future.delayed(Duration.zero, () {
      context.read<SectionByIdCubit>().getSectionById(langId: context.read<AppLocalizationCubit>().state.id, userId: context.read<AuthCubit>().getUserId(), sectionId: widget.sectionId);
    });
  }

  @override
  void dispose() {
    controller.dispose();
    super.dispose();
  }

  void hasMoreSectionIdDataScrollListener() {
    if (controller.position.maxScrollExtent == controller.offset) {
      if (context.read<SectionByIdCubit>().hasMoreSectionById()) {
        context.read<SectionByIdCubit>().getMoreSectionById(langId: context.read<AppLocalizationCubit>().state.id, userId: context.read<AuthCubit>().getUserId(), sectionId: widget.sectionId);
      } else {
        debugPrint("No more SectionBreakingNews");
      }
    }
  }

  _buildSectionBreakingNewsContainer(
      {required BreakingNewsModel model,
      required String type,
      required int index,
      required int totalCurrentSectionBreakingNews,
      required bool hasMoreSectionBreakingNewsFetchError,
      required bool hasMore,
      required List<BreakingNewsModel> newsList}) {
    if (index == totalCurrentSectionBreakingNews - 1 && index != 0) {
      //check if hasMore
      if (hasMore) {
        if (hasMoreSectionBreakingNewsFetchError) {
          return Center(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 10.0, vertical: 8.0),
              child: IconButton(
                  onPressed: () {
                    context
                        .read<SectionByIdCubit>()
                        .getMoreSectionById(langId: context.read<AppLocalizationCubit>().state.id, userId: context.read<AuthCubit>().getUserId(), sectionId: widget.sectionId);
                  },
                  icon: Icon(
                    Icons.error,
                    color: Theme.of(context).primaryColor,
                  )),
            ),
          );
        } else {
          return Center(child: Padding(padding: const EdgeInsets.symmetric(horizontal: 10.0, vertical: 8.0), child: showCircularProgress(true, Theme.of(context).primaryColor)));
        }
      }
    }
    return type == 'breaking_news'
        ? BreakNewsItem(model: model, index: index, breakNewsList: newsList)
        : BreakVideoItem(
            model: model,
          );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: setCustomAppBar(height: 45, isBackBtn: true, label: widget.title, context: context, horizontalPad: 15, isConvertText: false),
      body: BlocBuilder<SectionByIdCubit, SectionByIdState>(
        builder: (context, state) {
          if (state is SectionByIdFetchSuccess) {
            return Padding(
              padding: const EdgeInsetsDirectional.all(10.0),
              child: RefreshIndicator(
                onRefresh: () async {
                  context.read<SectionByIdCubit>().getSectionById(
                        userId: context.read<AuthCubit>().getUserId(),
                        sectionId: widget.sectionId,
                        langId: context.read<AppLocalizationCubit>().state.id,
                      );
                },
                child: ListView.builder(
                    controller: controller,
                    physics: const AlwaysScrollableScrollPhysics(),
                    shrinkWrap: true,
                    itemCount: state.breakNewsModel.length,
                    itemBuilder: (context, index) {
                      return _buildSectionBreakingNewsContainer(
                          model: (state).breakNewsModel[index],
                          type: (state).type,
                          hasMore: state.hasMore,
                          hasMoreSectionBreakingNewsFetchError: state.hasMoreFetchError,
                          index: index,
                          totalCurrentSectionBreakingNews: (state).breakNewsModel.length,
                          newsList: (state).type == 'breaking_news' ? state.breakNewsModel : []);
                    }),
              ),
            );
          }
          if (state is SectionByIdFetchFailure) {
            return ErrorContainerWidget(
                errorMsg: (state.errorMessage.contains(ErrorMessageKeys.noInternet)) ? UiUtils.getTranslatedLabel(context, 'internetmsg') : state.errorMessage, onRetry: getSectionByData);
          }
          //state is SectionByIdFetchInProgress || state is SectionByIdInitial
          return shimmerNewsList(context);
        },
      ),
    );
  }
}
