// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';

class SurveyQuestionRemoteDataSource {
  Future<dynamic> getSurveyQuestions({required BuildContext context, required String userId, required String langId}) async {
    try {
      final body = {USER_ID: userId, LANGUAGE_ID: langId};
      final result = await Api.post(body: body, url: Api.getQueApi);

      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
