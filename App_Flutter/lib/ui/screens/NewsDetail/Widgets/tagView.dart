// ignore_for_file: file_names

import 'dart:ui';

import 'package:flutter/material.dart';
import 'package:news/data/models/NewsModel.dart';

import '../../../../app/routes.dart';
import '../../../../utils/uiUtils.dart';

Widget tagView({required NewsModel model, required BuildContext context, bool? isFromDetailsScreen = false}) {
  List<String> tagList = [];

  if (model.tagName! != "") {
    final tagName = model.tagName!;
    tagList = tagName.split(',');
  }

  List<String> tagId = [];

  if (model.tagId != null && model.tagId! != "") {
    tagId = model.tagId!.split(",");
  }

  return model.tagName! != ""
      ? Padding(
          padding: const EdgeInsetsDirectional.only(top: 15.0),
          child: SizedBox(
              height: 20.0,
              child: SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                child: Row(
                  children: List.generate(tagList.length, (index) {
                    return Padding(
                        padding: EdgeInsetsDirectional.only(start: index == 0 ? 0 : 7),
                        child: InkWell(
                          child: ClipRRect(
                              borderRadius: BorderRadius.circular(3.0),
                              child: (isFromDetailsScreen != null && isFromDetailsScreen)
                                  ? tagsContainer(context: context, tagList: tagList, index: index)
                                  : BackdropFilter(filter: ImageFilter.blur(sigmaX: 30, sigmaY: 30), child: tagsContainer(context: context, tagList: tagList, index: index))),
                          onTap: () {
                            Navigator.of(context).pushNamed(Routes.tagScreen, arguments: {"tagId": tagId[index], "tagName": tagList[index]});
                          },
                        ));
                  }),
                ),
              )))
      : const SizedBox.shrink();
}

Widget tagsContainer({required BuildContext context, required List<String> tagList, required int index}) {
  return Container(
      height: 20.0,
      width: 65,
      alignment: Alignment.center,
      padding: const EdgeInsetsDirectional.only(start: 3.0, end: 3.0),
      decoration: BoxDecoration(
        borderRadius: const BorderRadius.only(topLeft: Radius.circular(10.0), bottomRight: Radius.circular(10.0)),
        color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7),
      ),
      child: Text(
        tagList[index],
        style: Theme.of(context).textTheme.bodyMedium?.copyWith(
              color: UiUtils.getColorScheme(context).secondary,
              fontSize: 11,
            ),
        overflow: TextOverflow.ellipsis,
        softWrap: true,
      ));
}
