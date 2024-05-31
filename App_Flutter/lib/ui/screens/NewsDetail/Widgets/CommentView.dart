// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/cubits/NewsComment/likeAndDislikeCommCubit.dart';
import 'package:news/cubits/NewsComment/setCommentCubit.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/cubits/commentNewsCubit.dart';
import 'package:news/utils/ErrorMessageKeys.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/data/models/CommentModel.dart';
import 'package:news/data/repositories/NewsComment/LikeAndDislikeComment/likeAndDislikeCommRepository.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';
import 'package:news/ui/widgets/loginRequired.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/delAndReportCom.dart';

class CommentView extends StatefulWidget {
  final String newsId;
  final Function updateComFun;
  final Function updateIsReplyFun;

  const CommentView({
    Key? key,
    required this.newsId,
    required this.updateComFun,
    required this.updateIsReplyFun,
  }) : super(key: key);

  @override
  CommentViewState createState() => CommentViewState();
}

class CommentViewState extends State<CommentView> {
  final TextEditingController _commentC = TextEditingController();
  TextEditingController reportC = TextEditingController();
  bool comBtnEnabled = false;
  bool isReply = false;
  int? replyComIndex;

  Widget commentsLengthView(int length) {
    return Row(
      children: [
        if (length > 0)
          Padding(
              padding: const EdgeInsets.symmetric(horizontal: 4),
              child: Row(
                children: [
                  CustomTextLabel(
                    text: 'allLbl',
                    textStyle: Theme.of(context).textTheme.bodySmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6), fontSize: 12.0, fontWeight: FontWeight.w600),
                  ),
                  Text(
                    " $length ",
                    style: Theme.of(context).textTheme.bodySmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6), fontSize: 12.0, fontWeight: FontWeight.w600),
                  ),
                  CustomTextLabel(
                    text: 'comsLbl',
                    textStyle: Theme.of(context).textTheme.bodySmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6), fontSize: 12.0, fontWeight: FontWeight.w600),
                  ),
                ],
              )),
        const Spacer(),
        Align(
            alignment: Alignment.topRight,
            child: InkWell(
              child: const Icon(Icons.close_rounded),
              onTap: () {
                widget.updateComFun(false);
              },
            ))
      ],
    );
  }

  Widget profileWithSendCom() {
    return Padding(
        padding: const EdgeInsetsDirectional.only(top: 5.0),
        child: Row(children: [
          Expanded(
              flex: 1,
              child: BlocBuilder<AuthCubit, AuthState>(
                builder: (context, state) {
                  if (state is Authenticated && context.read<AuthCubit>().getProfile() != "") {
                    return CircleAvatar(backgroundImage: NetworkImage(context.read<AuthCubit>().getProfile()));
                  } else {
                    return UiUtils.setFixedSizeboxForProfilePicture(childWidget: const Icon(Icons.account_circle, size: 35));
                  }
                },
              )),
          BlocListener<SetCommentCubit, SetCommentState>(
              bloc: context.read<SetCommentCubit>(),
              listener: (context, state) {
                if (state is SetCommentFetchSuccess) {
                  context.read<CommentNewsCubit>().commentUpdateList(state.setComment, state.total);
                  FocusScopeNode currentFocus = FocusScope.of(context);
                  if (!currentFocus.hasPrimaryFocus) {
                    currentFocus.unfocus();
                  }
                  _commentC.clear();
                  setState(() {});
                }
              },
              child: Expanded(
                  flex: 7,
                  child: Padding(
                      padding: const EdgeInsetsDirectional.only(start: 18.0),
                      child: TextField(
                        controller: _commentC,
                        style: Theme.of(context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7)),
                        onChanged: (String val) {
                          if (_commentC.text.trim().isNotEmpty) {
                            setState(() => comBtnEnabled = true);
                          } else {
                            setState(() => comBtnEnabled = false);
                          }
                        },
                        keyboardType: TextInputType.multiline,
                        maxLines: null,
                        decoration: InputDecoration(
                            contentPadding: const EdgeInsets.only(top: 10.0, bottom: 2.0),
                            isDense: true,
                            suffixIconConstraints: const BoxConstraints(
                              maxHeight: 35,
                              maxWidth: 30,
                            ),
                            enabledBorder: UnderlineInputBorder(
                              borderSide: BorderSide(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.5), width: 1.5),
                            ),
                            hintText: UiUtils.getTranslatedLabel(context, 'shareThoghtLbl'),
                            hintStyle: Theme.of(context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7)),
                            suffixIcon: IconButton(
                              icon: Icon(Icons.send, color: comBtnEnabled ? UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.8) : Colors.transparent, size: 20.0),
                              onPressed: () async {
                                if (context.read<AuthCubit>().getUserId() != "0") {
                                  context.read<SetCommentCubit>().setComment(
                                      userId: context.read<AuthCubit>().getUserId(),
                                      parentId: "0",
                                      newsId: widget.newsId,
                                      message: _commentC.text,
                                      langId: context.read<AppLocalizationCubit>().state.id);
                                } else {
                                  loginRequired(context);
                                }
                              },
                            )),
                      ))))
        ]));
  }

  _buildCommContainer({
    required CommentModel model,
    required int index,
    required int totalCurrentComm,
    required bool hasMoreCommFetchError,
    required bool hasMore,
  }) {
    if (index == totalCurrentComm - 1 && index <= 0) {
      //check if hasMore
      if (hasMore) {
        if (hasMoreCommFetchError) {
          return const SizedBox.shrink();
        } else {
          return Center(child: Padding(padding: const EdgeInsets.symmetric(horizontal: 15.0, vertical: 8.0), child: showCircularProgress(true, Theme.of(context).primaryColor)));
        }
      }
    }
    return BlocProvider<LikeAndDislikeCommCubit>(
        create: (context) => LikeAndDislikeCommCubit(LikeAndDislikeCommRepository()),
        child: Builder(builder: (context) {
          return InkWell(
            child: Row(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: <Widget>[
              (model.profile != null && model.profile != "")
                  ? UiUtils.setFixedSizeboxForProfilePicture(
                      childWidget:
                          CircleAvatar(backgroundImage: (model.profile != null) ? NetworkImage(model.profile!) : NetworkImage(const Icon(Icons.account_circle, size: 35) as String), radius: 32))
                  : UiUtils.setFixedSizeboxForProfilePicture(childWidget: const Icon(Icons.account_circle, size: 35)),
              Expanded(
                  child: Padding(
                      padding: const EdgeInsetsDirectional.only(start: 15.0),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Text(model.name!, style: Theme.of(context).textTheme.bodyMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7), fontSize: 13)),
                              Padding(
                                  padding: const EdgeInsetsDirectional.only(start: 10.0),
                                  child: Icon(Icons.circle, size: 4.0, color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7))),
                              Padding(
                                  padding: const EdgeInsetsDirectional.only(start: 10.0),
                                  child: Text(
                                    UiUtils.convertToAgo(context, DateTime.parse(model.date!), 1)!,
                                    style: Theme.of(context).textTheme.bodySmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7), fontSize: 10),
                                  )),
                            ],
                          ),
                          Padding(
                            padding: const EdgeInsets.only(top: 8.0),
                            child: Text(
                              model.message!,
                              style: Theme.of(context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.normal),
                            ),
                          ),
                          BlocConsumer<LikeAndDislikeCommCubit, LikeAndDislikeCommState>(
                              bloc: context.read<LikeAndDislikeCommCubit>(),
                              listener: (context, state) {
                                if (state is LikeAndDislikeCommSuccess) {
                                  if (state.fromLike) {
                                    if (state.comment.like == "1") {
                                      model.like = "0";
                                      if (model.totalLikes!.isNotEmpty) {
                                        model.totalLikes = (int.parse(model.totalLikes!) - 1).toString();
                                      }
                                      setState(() {});
                                    } else {
                                      if (state.comment.dislike == "1") model.dislike = "0";
                                      if (state.comment.dislike == "1" && model.totalDislikes!.isNotEmpty) model.totalDislikes = (int.parse(model.totalDislikes!) - 1).toString();
                                      model.like = "1";
                                      model.totalLikes = (int.parse(model.totalLikes!) + 1).toString();
                                      setState(() {});
                                    }
                                  } else {
                                    if (state.comment.dislike == "1") {
                                      model.dislike = "0";
                                      if (model.totalDislikes!.isNotEmpty) model.totalDislikes = (int.parse(model.totalDislikes!) - 1).toString();
                                      setState(() {});
                                    } else {
                                      if (state.comment.like == "1") model.like = "0";
                                      if (state.comment.like == "1" && model.totalLikes!.isNotEmpty) model.totalLikes = (int.parse(model.totalLikes!) - 1).toString();
                                      model.dislike = "1";
                                      model.totalDislikes = (int.parse(model.totalDislikes!) + 1).toString();
                                      setState(() {});
                                    }
                                  }
                                }
                              },
                              builder: (context, state) {
                                return Padding(
                                  padding: const EdgeInsets.only(top: 15.0),
                                  child: Row(
                                    children: [
                                      GestureDetector(
                                          child: const Icon(Icons.thumb_up_off_alt_rounded),
                                          onTap: () {
                                            if (context.read<AuthCubit>().getUserId() != "0") {
                                              context.read<LikeAndDislikeCommCubit>().setLikeAndDislikeComm(
                                                  userId: context.read<AuthCubit>().getUserId(),
                                                  comment: model,
                                                  status: (model.like == "1") ? "0" : "1",
                                                  fromLike: true,
                                                  langId: context.read<AppLocalizationCubit>().state.id);
                                            } else {
                                              loginRequired(context);
                                            }
                                          }),
                                      model.totalLikes! != "0"
                                          ? Padding(
                                              padding: const EdgeInsetsDirectional.only(start: 4.0),
                                              child: Text(model.totalLikes!, style: Theme.of(context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer)),
                                            )
                                          : const SizedBox(width: 12),
                                      Padding(
                                          padding: const EdgeInsetsDirectional.only(start: 35),
                                          child: InkWell(
                                            child: const Icon(Icons.thumb_down_alt_rounded),
                                            onTap: () {
                                              if (context.read<AuthCubit>().getUserId() != "0") {
                                                context.read<LikeAndDislikeCommCubit>().setLikeAndDislikeComm(
                                                    userId: context.read<AuthCubit>().getUserId(),
                                                    comment: model,
                                                    status: (model.dislike == "1") ? "0" : "2",
                                                    fromLike: false,
                                                    langId: context.read<AppLocalizationCubit>().state.id);
                                              } else {
                                                loginRequired(context);
                                              }
                                            },
                                          )),
                                      model.totalDislikes! != "0"
                                          ? Padding(
                                              padding: const EdgeInsetsDirectional.only(start: 4.0),
                                              child: Text(model.totalDislikes!, style: Theme.of(context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer)),
                                            )
                                          : const SizedBox(width: 12),
                                      const Padding(
                                          padding: EdgeInsetsDirectional.only(start: 35),
                                          child: InkWell(
                                            child: Icon(Icons.quickreply_rounded),
                                          )),
                                      model.replyComList!.isNotEmpty
                                          ? Padding(
                                              padding: const EdgeInsetsDirectional.only(start: 5.0),
                                              child: Text(model.replyComList!.length.toString(),
                                                  style: Theme.of(context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer)))
                                          : const SizedBox.shrink(),
                                      const Spacer(),
                                      if (context.read<AuthCubit>().getUserId() != "0")
                                        InkWell(
                                          child: Icon(Icons.more_vert_outlined, color: UiUtils.getColorScheme(context).primaryContainer, size: 17),
                                          onTap: () => delAndReportCom(index: index, newsId: widget.newsId, context: context, model: model, reportC: reportC, setState: setState),
                                        )
                                    ],
                                  ),
                                );
                              }),
                          Padding(
                              padding: const EdgeInsets.only(top: 10.0),
                              child: InkWell(
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(horizontal: 4),
                                  child: Row(
                                    children: [
                                      Text(model.replyComList!.isNotEmpty ? "${model.replyComList!.length} " : "",
                                          style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Theme.of(context).primaryColor, fontSize: 12, fontWeight: FontWeight.w600)),
                                      CustomTextLabel(
                                          text: model.replyComList!.isNotEmpty ? 'replyLbl' : "",
                                          textStyle: Theme.of(context).textTheme.bodySmall?.copyWith(color: Theme.of(context).primaryColor, fontSize: 12, fontWeight: FontWeight.w600)),
                                    ],
                                  ),
                                ),
                                onTap: () {
                                  widget.updateIsReplyFun(true, index);
                                  setState(() {
                                    isReply = true;
                                    replyComIndex = index;
                                  });
                                },
                              )),
                        ],
                      ))),
            ]),
            onTap: () {
              widget.updateIsReplyFun(true, index);
              setState(() {
                isReply = true;
                replyComIndex = index;
              });
            },
          );
        }));
  }

  Widget allComListView(CommentNewsFetchSuccess state) {
    return Padding(
        padding: const EdgeInsets.only(top: 20.0),
        child: ListView.separated(
            separatorBuilder: (BuildContext context, int index) => Divider(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.5)),
            shrinkWrap: true,
            primary: false,
            padding: const EdgeInsets.only(top: 20.0),
            physics: const NeverScrollableScrollPhysics(),
            itemCount: state.commentNews.length,
            itemBuilder: (context, index) {
              return _buildCommContainer(
                model: state.commentNews[index],
                hasMore: state.hasMore,
                hasMoreCommFetchError: (state).hasMoreFetchError,
                index: index,
                totalCurrentComm: (state.commentNews[index].replyComList!.length + state.commentNews.length), //state.commentNews.length,
              );
            }));
  }

  Widget commentView() {
    return BlocBuilder<CommentNewsCubit, CommentNewsState>(builder: (context, state) {
      if (state is CommentNewsFetchInProgress || state is CommentNewsInitial) {
        return Center(child: showCircularProgress(true, UiUtils.getColorScheme(context).primaryContainer));
      }
      return Padding(
          padding: const EdgeInsetsDirectional.only(top: 10.0, bottom: 10.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              if (state is CommentNewsFetchSuccess) commentsLengthView((state).commentNews.length),
              if (state is! CommentNewsFetchSuccess)
                Row(
                  children: [
                    const Spacer(),
                    Align(
                        alignment: Alignment.topRight,
                        child: InkWell(
                          child: const Icon(Icons.close_rounded),
                          onTap: () => widget.updateComFun(false),
                        ))
                  ],
                ),
              if ((state is CommentNewsFetchFailure && !state.errorMessage.contains(ErrorMessageKeys.noInternet) || state is CommentNewsFetchSuccess)) profileWithSendCom(),
              if (state is CommentNewsFetchFailure)
                SizedBox(
                  height: MediaQuery.of(context).size.height / 2,
                  child: Center(
                    child: CustomTextLabel(
                        text: (state.errorMessage.contains(ErrorMessageKeys.noInternet))
                            ? UiUtils.getTranslatedLabel(context, 'internetmsg')
                            : (state.errorMessage == "No Data Found")
                                ? UiUtils.getTranslatedLabel(context, 'noComments')
                                : state.errorMessage,
                        textAlign: TextAlign.center),
                  ),
                ),
              if (state is CommentNewsFetchSuccess) allComListView(state)
            ],
          ));
    });
  }

  @override
  Widget build(BuildContext context) {
    return commentView();
  }
}
