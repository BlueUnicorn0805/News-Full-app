// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/utils/strings.dart';
import 'package:news/utils/api.dart';

class GetSurveyAnsRemoteDataSource {
  Future<dynamic> getSurveyAns({required BuildContext context, required String userId, required String langId}) async {
    try {
      final body = {USER_ID: userId, LANGUAGE_ID: langId};
      final result = await Api.post(body: body, url: Api.getQueResultApi);

      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
