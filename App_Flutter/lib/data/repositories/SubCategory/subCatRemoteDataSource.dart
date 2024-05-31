// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';

import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';

class SubCategoryRemoteDataSource {
  Future<dynamic> getSubCategory({required BuildContext context, required String catId, required String langId}) async {
    try {
      final body = {CATEGORY_ID: catId, LANGUAGE_ID: langId};
      final result = await Api.post(body: body, url: Api.getSubCategoryApi);

      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
