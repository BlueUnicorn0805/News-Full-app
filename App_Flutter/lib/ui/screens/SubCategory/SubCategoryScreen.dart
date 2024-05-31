// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/ui/styles/colors.dart';
import 'package:news/ui/widgets/customBackBtn.dart';
import 'package:news/ui/widgets/shimmerNewsList.dart';
import 'package:news/ui/screens/SubCategory/Widgets/SubCatNewsList.dart';
import 'package:news/ui/screens/SubCategory/Widgets/categoryShimmer.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/cubits/subCategoryCubit.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/cubits/appSystemSettingCubit.dart';
import 'package:news/cubits/subCatNewsCubit.dart';
import 'package:news/data/repositories/SubCatNews/subCatRepository.dart';

class SubCategoryScreen extends StatefulWidget {
  final String catId;
  final String catName;

  const SubCategoryScreen({super.key, required this.catId, required this.catName});

  @override
  SubCategoryScreenState createState() => SubCategoryScreenState();

  static Route route(RouteSettings routeSettings) {
    final arguments = routeSettings.arguments as Map<String, dynamic>;
    return CupertinoPageRoute(builder: (_) => SubCategoryScreen(catId: arguments['catId'], catName: arguments['catName']));
  }
}

class SubCategoryScreenState extends State<SubCategoryScreen> with TickerProviderStateMixin {
  final List<Map<String, dynamic>> _tabs = [];
  TabController? _tc;

  @override
  void initState() {
    getSubCatData();
    super.initState();
  }

  @override
  void dispose() {
    if (_tc != null) {
      _tc!.dispose();
    }
    super.dispose();
  }

  Future getSubCatData() async {
    Future.delayed(Duration.zero, () {
      context.read<SubCategoryCubit>().getSubCategory(context: context, langId: context.read<AppLocalizationCubit>().state.id, catId: widget.catId);
    });
  }

  Widget catData() {
    return BlocConsumer<SubCategoryCubit, SubCategoryState>(
        bloc: context.read<SubCategoryCubit>(),
        listener: (context, state) {
          if (state is SubCategoryFetchSuccess) {
            setState(() {
              for (int i = 0; i < state.subCategory.length; i++) {
                _tabs.add({'text': state.subCategory[i].subCatName, 'subCatId': state.subCategory[i].id});
              }

              _tc = TabController(
                vsync: this,
                length: state.subCategory.length,
              )..addListener(() {});
            });
          }
        },
        builder: (context, state) {
          if (state is SubCategoryFetchSuccess) {
            return DefaultTabController(
                length: state.subCategory.length,
                child: _tabs.isNotEmpty
                    ? TabBar(
                        controller: _tc,
                        labelColor: secondaryColor,
                        indicatorColor: Colors.transparent,
                        isScrollable: true,
                        padding: const EdgeInsets.only(top: 10, bottom: 5),
                        physics: const AlwaysScrollableScrollPhysics(),
                        unselectedLabelColor: UiUtils.getColorScheme(context).primaryContainer,
                        indicator: BoxDecoration(borderRadius: BorderRadius.circular(5.0), color: UiUtils.getColorScheme(context).secondaryContainer),
                        tabs: _tabs
                            .map((tab) => AnimatedContainer(
                                height: 30,
                                duration: const Duration(milliseconds: 600),
                                padding: const EdgeInsetsDirectional.only(top: 5.0, bottom: 5.0),
                                child: Tab(
                                  text: tab['text'],
                                )))
                            .toList())
                    : catShimmer());
          }
          if (state is SubCategoryFetchFailure) {
            return const SizedBox.shrink();
          }
          //state is SubCategoryFetchInProgress || state is SubCategoryInitial
          return catShimmer();
        });
  }

  setAppBar() {
    return PreferredSize(
        preferredSize: const Size(double.infinity, 80),
        child: AppBar(
          leading: const CustomBackButton(horizontalPadding: 15),
          titleSpacing: 0.0,
          centerTitle: false,
          backgroundColor: Colors.transparent,
          title:
              Text(widget.catName, style: Theme.of(context).textTheme.titleLarge?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.w600, letterSpacing: 0.5)),
          bottom: PreferredSize(
            preferredSize: Size(MediaQuery.of(context).size.width, 30),
            child: catData(),
          ),
        ));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: setAppBar(),
      body: BlocBuilder<SubCategoryCubit, SubCategoryState>(builder: (context, state) {
        if (state is SubCategoryFetchFailure || context.read<AppConfigurationCubit>().getSubCatMode() != "1") {
          return BlocProvider<SubCatNewsCubit>(
            create: (_) => SubCatNewsCubit(SubCatNewsRepository()),
            child: SubCatNewsList(
              from: 1,
              catId: widget.catId,
            ),
          );
        }
        if (state is SubCategoryFetchSuccess && _tc != null && _tc!.length > 0) {
          return TabBarView(
              controller: _tc,
              children: List<Widget>.generate(_tc!.length, (int index) {
                return BlocProvider<SubCatNewsCubit>(
                  create: (_) => SubCatNewsCubit(SubCatNewsRepository()),
                  child: SubCatNewsList(
                    from: index == 0 ? 1 : 2,
                    subCatId: index == 0 ? null : _tabs[index]['subCatId'],
                    catId: widget.catId,
                  ),
                );
              }));
        }
        return shimmerNewsList(context);
      }),
    );
  }
}
