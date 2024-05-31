// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';

class BreakingNewsRemoteDataSource {
  Future<dynamic> getBreakingNews({required BuildContext context, required String langId}) async {
    try {
      final body = {LANGUAGE_ID: langId};
      final result = await Api.post(body: body, url: Api.getBreakingNewsApi);
      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
