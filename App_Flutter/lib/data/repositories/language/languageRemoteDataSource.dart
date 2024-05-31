// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:news/utils/api.dart';

class LanguageRemoteDataSource {
  Future<dynamic> getLanguages({required BuildContext context}) async {
    try {
      final result = await Api.post(url: Api.getLanguagesApi, body: {});
      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
