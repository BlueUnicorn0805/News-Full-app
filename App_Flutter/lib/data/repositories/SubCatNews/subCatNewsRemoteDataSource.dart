// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';

class SubCatNewsRemoteDataSource {
  Future<dynamic> getSubCatNews({required String limit, required String offset, required BuildContext context, String? catId, String? subCatId, required String userId, required String langId}) async {
    try {
      final body = {LIMIT: limit, OFFSET: offset, USER_ID: userId, LANGUAGE_ID: langId};
      if (catId != null) {
        body[CATEGORY_ID] = catId;
      }
      if (subCatId != null) {
        body[SUBCAT_ID] = subCatId;
      }
      final result = await Api.post(body: body, url: Api.getNewsByCatApi);
      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
