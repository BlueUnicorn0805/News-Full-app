// ignore_for_file: file_names, use_build_context_synchronously

import 'dart:io';
import 'package:dio/dio.dart';
import 'package:flutter/cupertino.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';

class AddNewsRemoteDataSource {
  //to update fcmId of user's
  Future<dynamic> addNewsData(
      {required BuildContext context,
      required String actionType,
      required String userId,
      required String catId,
      required String title,
      required String conTypeId,
      required String conType,
      required String langId,
      File? image,
      String? newsId,
      String? subCatId,
      String? showTill,
      String? tagId,
      String? url,
      File? videoUpload,
      String? desc,
      List<File>? otherImage}) async {
    try {
      Map<String, dynamic> body = {ACTION_TYPE: actionType, USER_ID: userId, CATEGORY_ID: catId, TITLE: title, CONTENT_TYPE: conTypeId, LANGUAGE_ID: langId};
      Map<String, dynamic> result = {};

      if (image != null) body[IMAGE] = await MultipartFile.fromFile(image.path);
      if (newsId != null) body[NEWS_ID] = newsId;
      if (subCatId != null) body[SUBCAT_ID] = subCatId;
      if (showTill != null) body[SHOW_TILL] = showTill;
      if (tagId != null) body[TAG_ID] = tagId;
      if (desc != null) body[DESCRIPTION] = desc;

      if (url != null && (conType == UiUtils.getTranslatedLabel(context, 'videoOtherUrlLbl') || conType == UiUtils.getTranslatedLabel(context, 'videoYoutubeLbl'))) {
        body[CONTENT_DATA] = url;
      } else if (conType == UiUtils.getTranslatedLabel(context, 'videoUploadLbl')) {
        if (url != null) body[CONTENT_DATA] = url;
        if (videoUpload != null) body[CONTENT_DATA] = await MultipartFile.fromFile(videoUpload.path);
      }

      if (otherImage!.isNotEmpty) {
        for (var i = 0; i < otherImage.length; i++) {
          body["ofile[$i]"] = await MultipartFile.fromFile(otherImage[i].path);
        }
      }

      result = await Api.post(body: body, url: Api.setNewsApi);
      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
