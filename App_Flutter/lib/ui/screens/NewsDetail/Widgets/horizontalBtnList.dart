// ignore_for_file: use_build_context_synchronously, depend_on_referenced_packages, file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/cubits/Bookmark/UpdateBookmarkCubit.dart';
import 'package:news/cubits/Bookmark/bookmarkCubit.dart';
import 'package:news/cubits/appSystemSettingCubit.dart';
import 'package:news/data/models/BreakingNewsModel.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';
import 'package:news/ui/widgets/createDynamicLink.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/ui/widgets/loginRequired.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:html/parser.dart' show parse;
import '../../../../utils/internetConnectivity.dart';
import '../../../widgets/SnackBarWidget.dart';

Widget allRowBtn(
    {required bool isFromBreak,
    required BuildContext context,
    BreakingNewsModel? breakModel,
    NewsModel? model,
    required int fontVal,
    required Function updateFont,
    required bool isPlaying,
    required Function speak,
    required Function stop,
    required Function updateComEnabled}) {
  return !isFromBreak
      ? Padding(
          padding: const EdgeInsetsDirectional.only(end: 85),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              if (context.read<AppConfigurationCubit>().getCommentsMode() == "1")
                InkWell(
                  child: Column(
                    children: [
                      const Icon(Icons.insert_comment_rounded),
                      Padding(
                          padding: const EdgeInsetsDirectional.only(top: 4.0),
                          child: CustomTextLabel(
                            text: 'comLbl',
                            maxLines: 2,
                            textStyle: Theme.of(context).textTheme.bodySmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.8), fontSize: 9.0),
                          ))
                    ],
                  ),
                  onTap: () {
                    if (context.read<AuthCubit>().getUserId() != "0") {
                      updateComEnabled(true);
                    } else {
                      loginRequired(context);
                    }
                  },
                ),
              InkWell(
                child: setShareBtn(context),
                onTap: () async {
                  if (await InternetConnectivity.isNetworkAvailable()) {
                    createDynamicLink(context: context, id: model!.id!, title: model.title!, isVideoId: false, isBreakingNews: false, image: model.image!);
                  } else {
                    showSnackBar(UiUtils.getTranslatedLabel(context, 'internetmsg'), context);
                  }
                },
              ),
              BlocBuilder<BookmarkCubit, BookmarkState>(
                  bloc: context.read<BookmarkCubit>(),
                  builder: (context, bookmarkState) {
                    bool isBookmark = context.read<BookmarkCubit>().isNewsBookmark(model!.id!);
                    return BlocConsumer<UpdateBookmarkStatusCubit, UpdateBookmarkStatusState>(
                        bloc: context.read<UpdateBookmarkStatusCubit>(),
                        listener: ((context, state) {
                          if (state is UpdateBookmarkStatusSuccess) {
                            if (state.wasBookmarkNewsProcess) {
                              context.read<BookmarkCubit>().addBookmarkNews(state.news);
                            } else {
                              context.read<BookmarkCubit>().removeBookmarkNews(state.news);
                            }
                            // isBookmark = (bookmarkState as BookmarkFetchSuccess).bookmark.contains(model.id);
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
                              child: Column(children: [
                                state is UpdateBookmarkStatusInProgress
                                    ? SizedBox(
                                        height: 24,
                                        width: 24,
                                        child: showCircularProgress(true, Theme.of(context).primaryColor),
                                      )
                                    : Icon(
                                        isBookmark ? Icons.bookmark_added_rounded : Icons.bookmark_add_outlined,
                                      ),
                                Padding(
                                    padding: const EdgeInsetsDirectional.only(top: 4.0),
                                    child: CustomTextLabel(
                                      text: 'saveLbl',
                                      maxLines: 2,
                                      textStyle: Theme.of(context).textTheme.bodySmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.8), fontSize: 9.0),
                                    ))
                              ]));
                        });
                  }),
              InkWell(
                child: Column(
                  children: [
                    const Icon(Icons.text_fields_rounded),
                    Padding(
                        padding: const EdgeInsetsDirectional.only(top: 4.0),
                        child: CustomTextLabel(
                          text: 'txtSizeLbl',
                          maxLines: 2,
                          textStyle: Theme.of(context).textTheme.bodySmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.8), fontSize: 9.0),
                        ))
                  ],
                ),
                onTap: () {
                  changeFontSizeSheet(context, fontVal, updateFont);
                },
              ),
              InkWell(
                child: setSpeakBtn(context, isPlaying),
                onTap: () {
                  if (isPlaying) {
                    stop();
                  } else {
                    final document = parse("${model!.title}\n${model.desc}"); //Speak Title along with Description
                    String parsedString = parse(document.body!.text).documentElement!.text;
                    speak(parsedString);
                  }
                },
              ),
            ],
          ),
        )
      : Row(
          children: [
            InkWell(
              child: setTextSize(context),
              onTap: () {
                changeFontSizeSheet(context, fontVal, updateFont);
              },
            ),
            Padding(
              padding: const EdgeInsetsDirectional.only(start: 8.0),
              child: InkWell(
                child: setSpeakBtn(context, isPlaying),
                onTap: () {
                  if (isPlaying) {
                    stop();
                  } else {
                    final document = parse("${breakModel!.title}\n${breakModel.desc}"); //Speak Title along with Description
                    String parsedString = parse(document.body!.text).documentElement!.text;
                    speak(parsedString);
                  }
                },
              ),
            ),
            Padding(
              padding: const EdgeInsetsDirectional.only(start: 8.0),
              child: InkWell(
                child: setShareBtn(context),
                onTap: () async {
                  if (await InternetConnectivity.isNetworkAvailable()) {
                    createDynamicLink(context: context, id: breakModel!.id!, title: breakModel.title!, isVideoId: false, isBreakingNews: true, image: breakModel.image!);
                  } else {
                    showSnackBar(UiUtils.getTranslatedLabel(context, 'internetmsg'), context);
                  }
                },
              ),
            ),
          ],
        );
}

changeFontSizeSheet(BuildContext context, int fontValue, Function updateFun) {
  showModalBottomSheet<dynamic>(
      context: context,
      elevation: 5.0,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.only(topLeft: Radius.circular(50), topRight: Radius.circular(50))),
      builder: (BuildContext context) {
        return StatefulBuilder(builder: (BuildContext context, setStater) {
          return Container(
              padding: const EdgeInsetsDirectional.only(bottom: 20.0, top: 5.0, start: 20.0, end: 20.0),
              decoration: const BoxDecoration(
                borderRadius: BorderRadius.only(topLeft: Radius.circular(50), topRight: Radius.circular(50)),
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: <Widget>[
                  Padding(
                      padding: const EdgeInsets.symmetric(vertical: 30),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: <Widget>[
                          const Icon(Icons.text_fields_rounded),
                          Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 15),
                              child: Text(
                                UiUtils.getTranslatedLabel(context, 'txtSizeLbl'),
                                style: Theme.of(context).textTheme.titleLarge?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer),
                              )),
                          Text("( $fontValue )", style: Theme.of(context).textTheme.titleLarge?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer)),
                        ],
                      )),
                  SliderTheme(
                      data: SliderTheme.of(context).copyWith(
                        activeTrackColor: Colors.red[700],
                        inactiveTrackColor: Colors.red[100],
                        trackShape: const RoundedRectSliderTrackShape(),
                        trackHeight: 4.0,
                        thumbShape: const RoundSliderThumbShape(enabledThumbRadius: 12.0),
                        thumbColor: Colors.redAccent,
                        overlayColor: Colors.red.withAlpha(32),
                        overlayShape: const RoundSliderOverlayShape(overlayRadius: 28.0),
                        tickMarkShape: const RoundSliderTickMarkShape(),
                        activeTickMarkColor: Colors.red[700],
                        inactiveTickMarkColor: Colors.red[100],
                        valueIndicatorShape: const PaddleSliderValueIndicatorShape(),
                        valueIndicatorColor: Colors.redAccent,
                        valueIndicatorTextStyle: const TextStyle(
                          color: Colors.white,
                        ),
                      ),
                      child: Slider(
                        label: '$fontValue',
                        value: fontValue.toDouble(),
                        activeColor: Theme.of(context).primaryColor,
                        min: 15,
                        max: 40,
                        divisions: 10,
                        onChanged: (value) {
                          setStater(() {
                            fontValue = value.round();
                            updateFun(value.round());
                          });
                        },
                      )),
                ],
              ));
        });
      });
}

setShareBtn(BuildContext context) {
  return Column(
    children: [
      const Icon(Icons.share_rounded),
      Padding(
          padding: const EdgeInsetsDirectional.only(top: 4.0),
          child: CustomTextLabel(
            text: 'shareLbl',
            maxLines: 2,
            textStyle: Theme.of(context).textTheme.bodySmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.8), fontSize: 9.0),
          ))
    ],
  );
}

setSpeakBtn(BuildContext context, bool isPlaying) {
  return Column(
    children: [
      Icon(
        Icons.speaker_phone_rounded,
        color: isPlaying ? Theme.of(context).primaryColor : UiUtils.getColorScheme(context).primaryContainer,
      ),
      Padding(
          padding: const EdgeInsetsDirectional.only(top: 4.0),
          child: CustomTextLabel(
            text: 'speakLoudLbl',
            maxLines: 2,
            textStyle:
                Theme.of(context).textTheme.bodySmall?.copyWith(color: isPlaying ? Theme.of(context).primaryColor : UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.8), fontSize: 9.0),
          ))
    ],
  );
}

setTextSize(BuildContext context) {
  return Column(
    children: [
      const Icon(Icons.text_fields_rounded),
      Padding(
          padding: const EdgeInsetsDirectional.only(top: 4.0),
          child: CustomTextLabel(
            text: 'txtSizeLbl',
            maxLines: 2,
            textStyle: Theme.of(context).textTheme.bodySmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.8), fontSize: 9.0),
          ))
    ],
  );
}
