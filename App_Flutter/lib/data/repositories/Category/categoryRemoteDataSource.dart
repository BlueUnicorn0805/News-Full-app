// ignore_for_file: file_names

import 'package:flutter/material.dart';

import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';

class CategoryRemoteDataSource {
  Future<dynamic> getCategory({required String limit, required String offset, required String langId, required BuildContext context}) async {
    try {
      final body = {
        LIMIT: limit,
        OFFSET: offset,
        LANGUAGE_ID: langId,
      };
      final result = await Api.post(body: body, url: Api.getCatApi);

      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
