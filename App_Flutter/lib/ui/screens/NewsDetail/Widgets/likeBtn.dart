// ignore_for_file: file_names

import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/cubits/LikeAndDislikeNews/LikeAndDislikeCubit.dart';
import 'package:news/cubits/LikeAndDislikeNews/updateLikeAndDislikeCubit.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/ui/styles/colors.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/utils/uiUtils.dart';
import '../../../widgets/loginRequired.dart';

Widget likeBtn(BuildContext context, NewsModel model) {
  bool isLike = context.read<LikeAndDisLikeCubit>().isNewsLikeAndDisLike(model.newsId!);
  return BlocConsumer<LikeAndDisLikeCubit, LikeAndDisLikeState>(
      bloc: context.read<LikeAndDisLikeCubit>(),
      listener: ((context, state) {
        if (state is LikeAndDisLikeFetchSuccess) {
          isLike = context.read<LikeAndDisLikeCubit>().isNewsLikeAndDisLike(model.newsId!);
        }
      }),
      builder: (context, likeAndDislikeState) {
        return BlocConsumer<UpdateLikeAndDisLikeStatusCubit, UpdateLikeAndDisLikeStatusState>(
            bloc: context.read<UpdateLikeAndDisLikeStatusCubit>(),
            listener: ((context, state) {
              if (state is UpdateLikeAndDisLikeStatusSuccess) {
                context.read<LikeAndDisLikeCubit>().getLikeAndDisLike(context: context, langId: context.read<AppLocalizationCubit>().state.id, userId: context.read<AuthCubit>().getUserId());
                model.totalLikes = (!isLike)
                    ? (int.parse(model.totalLikes.toString()) + 1).toString()
                    : (model.totalLikes!.isNotEmpty)
                        ? (int.parse(model.totalLikes.toString()) - 1).toString()
                        : "0";
              }
            }),
            builder: (context, state) {
              isLike = context.read<LikeAndDisLikeCubit>().isNewsLikeAndDisLike(model.newsId!);
              return Positioned.directional(
                  textDirection: Directionality.of(context),
                  top: MediaQuery.of(context).size.height / 2.90,
                  end: MediaQuery.of(context).size.width / 10.8,
                  child: Column(
                    children: [
                      InkWell(
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
                        child: ClipRRect(
                            borderRadius: BorderRadius.circular(52.0),
                            child: BackdropFilter(
                                filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
                                child: Container(
                                  alignment: Alignment.center,
                                  height: 39,
                                  width: 39,
                                  decoration: BoxDecoration(boxShadow: [
                                    BoxShadow(blurRadius: 6, offset: const Offset(5.0, 5.0), color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.4), spreadRadius: 0),
                                  ], color: secondaryColor, shape: BoxShape.circle),
                                  child: (state is UpdateLikeAndDisLikeStatusInProgress)
                                      ? SizedBox(
                                          height: 20,
                                          width: 20,
                                          child: showCircularProgress(true, Theme.of(context).primaryColor),
                                        )
                                      : isLike
                                          ? const Icon(Icons.thumb_up_alt, color: darkSecondaryColor)
                                          : const Icon(
                                              Icons.thumb_up_off_alt,
                                              color: darkSecondaryColor,
                                            ),
                                ))),
                      ),
                      SizedBox(
                        width: MediaQuery.of(context).size.width / 7.5,
                        child: Padding(
                          padding: const EdgeInsetsDirectional.only(top: 5.0),
                          child: CustomTextLabel(
                            text: (int.tryParse(model.totalLikes!)! > 0) ? "${model.totalLikes!} ${UiUtils.getTranslatedLabel(context, 'likeLbl')}" : "",
                            maxLines: 2,
                            textStyle: Theme.of(context).textTheme.bodySmall?.copyWith(
                                  color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7),
                                ),
                          ),
                        ),
                      )
                    ],
                  ));
            });
      });
}
