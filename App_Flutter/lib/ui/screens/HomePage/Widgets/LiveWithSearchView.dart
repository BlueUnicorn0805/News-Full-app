// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter_svg/svg.dart';
import 'package:news/cubits/themeCubit.dart';
import 'package:news/ui/styles/appTheme.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:shimmer/shimmer.dart';
import '../../../../app/routes.dart';
import '../../../../cubits/appSystemSettingCubit.dart';
import '../../../../cubits/liveStreamCubit.dart';

class LiveWithSearchView extends StatefulWidget {
  const LiveWithSearchView({super.key});

  @override
  LiveWithSearchState createState() => LiveWithSearchState();
}

class LiveWithSearchState extends State<LiveWithSearchView> {
  Widget liveWithSearchView() {
    return BlocBuilder<LiveStreamCubit, LiveStreamState>(
        bloc: context.read<LiveStreamCubit>(),
        builder: (context, state) {
          if (state is LiveStreamFetchSuccess) {
            return Padding(
                padding: const EdgeInsets.only(top: 10.0),
                child: Row(
                  children: [
                    Expanded(
                        //Live button is present
                        flex: 9,
                        child: InkWell(
                          child: Container(
                              alignment: Alignment.centerLeft,
                              height: (state.liveStream.isNotEmpty && context.read<AppConfigurationCubit>().getLiveStreamMode() == "1") ? 40 : 60,
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(16.0),
                                color: UiUtils.getColorScheme(context).background,
                              ),
                              child: Row(
                                children: [
                                  Padding(
                                    padding: const EdgeInsetsDirectional.only(start: 10.0),
                                    child: Icon(Icons.search_rounded, color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7)),
                                  ),
                                  Padding(
                                    padding: const EdgeInsetsDirectional.only(start: 10.0),
                                    child: CustomTextLabel(
                                      text: 'searchHomeNews',
                                      textStyle: TextStyle(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7)),
                                    ),
                                  ),
                                ],
                              )),
                          onTap: () {
                            Navigator.of(context).pushNamed(Routes.search);
                          },
                        )),
                    if (state.liveStream.isNotEmpty && context.read<AppConfigurationCubit>().getLiveStreamMode() == "1")
                      Expanded(
                          flex: 3,
                          child: InkWell(
                            child: Padding(
                                padding: const EdgeInsetsDirectional.only(start: 10.0),
                                child: SizedBox(
                                    height: 60,
                                    child: Row(
                                      mainAxisAlignment: MainAxisAlignment.center,
                                      children: [
                                        SvgPicture.asset(UiUtils.getSvgImagePath(context.read<ThemeCubit>().state.appTheme == AppTheme.Dark ? "live_news_dark" : "live_news"),
                                            height: 30.0, width: 54.0)
                                      ],
                                    ))),
                            onTap: () {
                              Navigator.of(context).pushNamed(Routes.live, arguments: {"liveNews": state.liveStream});
                            },
                          ))
                  ],
                ));
          }
          if (state is LiveStreamFetchFailure) {
            return Padding(
              padding: const EdgeInsets.only(top: 10),
              child: InkWell(
                child: Container(
                    alignment: Alignment.centerLeft,
                    height: 60,
                    decoration: BoxDecoration(borderRadius: BorderRadius.circular(16.0), color: UiUtils.getColorScheme(context).background),
                    child: Row(
                      children: [
                        Padding(padding: const EdgeInsetsDirectional.only(start: 10.0), child: Icon(Icons.search_rounded, color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7))),
                        Padding(
                          padding: const EdgeInsetsDirectional.only(start: 10.0),
                          child: CustomTextLabel(text: 'searchHomeNews', textStyle: TextStyle(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7))),
                        ),
                      ],
                    )),
                onTap: () {
                  Navigator.of(context).pushNamed(Routes.search);
                },
              ),
            );
          }
          return shimmerData(); //state is LiveStreamFetchInProgress ||  state is LiveStreamInitial
        });
  }

  Widget shimmerData() {
    return Shimmer.fromColors(
        baseColor: Colors.grey.withOpacity(0.6),
        highlightColor: Colors.grey,
        child: Container(
            height: 40,
            margin: const EdgeInsets.only(top: 15),
            width: double.maxFinite,
            padding: const EdgeInsets.only(left: 10.0, right: 10.0),
            decoration: BoxDecoration(borderRadius: BorderRadius.circular(20), color: Colors.grey.withOpacity(0.6))));
  }

  @override
  Widget build(BuildContext context) {
    return liveWithSearchView();
  }
}
