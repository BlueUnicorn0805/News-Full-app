// ignore_for_file: use_build_context_synchronously, file_names
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:youtube_player_flutter/youtube_player_flutter.dart';
import 'package:news/app/routes.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/cubits/Bookmark/UpdateBookmarkCubit.dart';
import 'package:news/cubits/Bookmark/bookmarkCubit.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/data/repositories/Bookmark/bookmarkRepository.dart';
import 'package:news/utils/internetConnectivity.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/ui/widgets/SnackBarWidget.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';
import 'package:news/ui/widgets/createDynamicLink.dart';
import 'package:news/ui/widgets/loginRequired.dart';
import 'package:news/ui/widgets/networkImage.dart';

class VideoItem extends StatefulWidget {
  final NewsModel model;

  const VideoItem({
    super.key,
    required this.model,
  });

  @override
  VideoItemState createState() => VideoItemState();
}

class VideoItemState extends State<VideoItem> {
  Widget videoData(NewsModel video) {
    return Padding(
      padding: const EdgeInsets.only(top: 15.0, left: 10, right: 10),
      child: Column(
        children: <Widget>[
          ClipRRect(
            borderRadius: const BorderRadius.all(Radius.circular(10.0)),
            child: GestureDetector(
              onTap: () {
                Navigator.of(context).pushNamed(Routes.newsVideo, arguments: {"from": 1, "model": video});
              },
              child: Stack(
                alignment: Alignment.center,
                children: [
                  (video.contentType == 'video_youtube' && video.contentValue!.isNotEmpty)
                      ? CustomNetworkImage(
                          networkImageUrl: 'https://img.youtube.com/vi/${YoutubePlayer.convertUrlToId(video.contentValue!)!}/0.jpg',
                          fit: BoxFit.cover,
                          width: double.maxFinite,
                          height: 220,
                          isVideo: true,
                        )
                      : CustomNetworkImage(
                          networkImageUrl: video.image!,
                          fit: BoxFit.cover,
                          width: double.maxFinite,
                          height: 220,
                          isVideo: true,
                        ),
                  const CircleAvatar(
                      radius: 30,
                      backgroundColor: Colors.black45,
                      child: Icon(
                        Icons.play_arrow,
                        size: 40,
                        color: Colors.white,
                      ))
                ],
              ),
            ),
          ),
          Padding(
              padding: const EdgeInsets.symmetric(horizontal: 5),
              child: Align(
                  alignment: Alignment.centerLeft,
                  child: Text(
                    video.title!,
                    style: Theme.of(context).textTheme.titleSmall!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ))),
          Padding(
            padding: const EdgeInsets.only(left: 5, right: 5),
            child: Row(
              children: [
                Text(UiUtils.convertToAgo(context, DateTime.parse(video.date!), 0)!,
                    style: Theme.of(context).textTheme.labelSmall!.copyWith(color: UiUtils.getColorScheme(context).outline.withOpacity(0.8))),
                const Spacer(),
                BlocProvider(
                  create: (context) => UpdateBookmarkStatusCubit(BookmarkRepository()),
                  child: BlocBuilder<BookmarkCubit, BookmarkState>(
                      bloc: context.read<BookmarkCubit>(),
                      builder: (context, bookmarkState) {
                        bool isBookmark = context.read<BookmarkCubit>().isNewsBookmark(video.id!);
                        return BlocConsumer<UpdateBookmarkStatusCubit, UpdateBookmarkStatusState>(
                            bloc: context.read<UpdateBookmarkStatusCubit>(),
                            listener: ((context, state) {
                              if (state is UpdateBookmarkStatusSuccess) {
                                if (state.wasBookmarkNewsProcess) {
                                  context.read<BookmarkCubit>().addBookmarkNews(state.news);
                                  setState(() {});
                                } else {
                                  context.read<BookmarkCubit>().removeBookmarkNews(state.news);
                                  setState(() {});
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
                                            news: video,
                                            status: (isBookmark) ? "0" : "1",
                                          );
                                    } else {
                                      loginRequired(context);
                                    }
                                  },
                                  child: state is UpdateBookmarkStatusInProgress
                                      ? SizedBox(
                                          height: 15,
                                          width: 15,
                                          child: showCircularProgress(true, Theme.of(context).primaryColor),
                                        )
                                      : Icon(
                                          isBookmark ? Icons.bookmark_added_rounded : Icons.bookmark_add_outlined,
                                        ));
                            });
                      }),
                ),
                const SizedBox(width: 10),
                InkWell(
                  onTap: () async {
                    if (await InternetConnectivity.isNetworkAvailable()) {
                      createDynamicLink(context: context, id: video.id!, title: video.title!, isVideoId: false, isBreakingNews: true, image: video.image!);
                    } else {
                      showSnackBar(UiUtils.getTranslatedLabel(context, 'internetmsg'), context);
                    }
                  },
                  splashColor: Colors.transparent,
                  child: const Icon(Icons.share_rounded),
                )
              ],
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return (widget.model.contentValue!.isNotEmpty) ? videoData(widget.model) : const SizedBox.shrink();
    //do not show VideoTile incase of blank content/videoUrl
  }
}
