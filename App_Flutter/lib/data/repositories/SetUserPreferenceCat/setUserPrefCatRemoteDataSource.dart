// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';

class SetUserPrefCatRemoteDataSource {
  Future<dynamic> setUserPrefCat({required BuildContext context, required String catId, required String userId}) async {
    try {
      final body = {USER_ID: userId, CATEGORY_ID: catId};
      final result = await Api.post(body: body, url: Api.setUserCatApi);

      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
