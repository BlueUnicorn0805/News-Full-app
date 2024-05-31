// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/cubits/videosCubit.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';
import 'package:news/ui/widgets/customAppBar.dart';
import 'package:news/ui/widgets/errorContainerWidget.dart';
import 'package:news/ui/widgets/videoItem.dart';
import 'package:news/ui/widgets/videoShimmer.dart';
import 'package:news/utils/ErrorMessageKeys.dart';
import 'package:news/utils/uiUtils.dart';

class VideoScreen extends StatefulWidget {
  const VideoScreen({super.key});

  @override
  VideoScreenState createState() => VideoScreenState();

  static Route<dynamic> route(RouteSettings routeSettings) {
    return CupertinoPageRoute(builder: (_) => const VideoScreen());
  }
}

class VideoScreenState extends State<VideoScreen> {
  late final ScrollController _videoScrollController = ScrollController()..addListener(hasMoreVideoScrollListener);

  void getVideos() {
    Future.delayed(Duration.zero, () {
      context.read<VideoCubit>().getVideo(context: context, langId: context.read<AppLocalizationCubit>().state.id);
    });
  }

  @override
  void initState() {
    getVideos();
    super.initState();
  }

  @override
  void dispose() {
    _videoScrollController.dispose();
    super.dispose();
  }

  void hasMoreVideoScrollListener() {
    if (_videoScrollController.position.maxScrollExtent == _videoScrollController.offset) {
      if (context.read<VideoCubit>().hasMoreVideo()) {
        context.read<VideoCubit>().getMoreVideo(context: context, langId: context.read<AppLocalizationCubit>().state.id);
      } else {
        debugPrint("No more videos");
      }
    }
  }

  Widget _buildVideos() {
    return BlocBuilder<VideoCubit, VideoState>(
      builder: (context, state) {
        if (state is VideoFetchSuccess) {
          return RefreshIndicator(
              onRefresh: () async {
                context.read<VideoCubit>().getVideo(context: context, langId: context.read<AppLocalizationCubit>().state.id);
              },
              child: ListView.builder(
                  padding: const EdgeInsets.only(bottom: 10.0),
                  controller: _videoScrollController,
                  physics: const AlwaysScrollableScrollPhysics(),
                  shrinkWrap: true,
                  itemCount: state.video.length,
                  itemBuilder: (context, index) {
                    return _buildVideoContainer(
                        video: state.video[index], hasMore: state.hasMore, hasMoreVideoFetchError: state.hasMoreFetchError, index: index, totalCurrentVideo: state.video.length);
                  }));
        }
        if (state is VideoFetchFailure) {
          return ErrorContainerWidget(
              errorMsg: (state.errorMessage.contains(ErrorMessageKeys.noInternet)) ? UiUtils.getTranslatedLabel(context, 'internetmsg') : state.errorMessage, onRetry: getVideos);
        }
        return Padding(padding: const EdgeInsets.only(bottom: 10.0, left: 10.0, right: 10.0), child: videoShimmer(context));
      },
    );
  }

  _buildVideoContainer({
    required NewsModel video,
    required int index,
    required int totalCurrentVideo,
    required bool hasMoreVideoFetchError,
    required bool hasMore,
  }) {
    if (index == totalCurrentVideo - 1 && index != 0) {
      //check if hasMore
      if (hasMore) {
        if (hasMoreVideoFetchError) {
          return Center(
            child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 15.0, vertical: 8.0),
                child: IconButton(
                    onPressed: () {
                      context.read<VideoCubit>().getMoreVideo(context: context, langId: context.read<AppLocalizationCubit>().state.id);
                    },
                    icon: Icon(Icons.error, color: Theme.of(context).primaryColor))),
          );
        } else {
          return Center(child: Padding(padding: const EdgeInsets.symmetric(horizontal: 15.0, vertical: 8.0), child: showCircularProgress(true, Theme.of(context).primaryColor)));
        }
      }
    }

    return VideoItem(model: video);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(appBar: setCustomAppBar(height: 45, isBackBtn: false, label: 'videosLbl', context: context, isConvertText: true), body: _buildVideos());
  }
}
