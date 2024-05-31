// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';

class GetUserByIdRemoteDataSource {
  Future<dynamic> getUserById({required BuildContext context, required String userId}) async {
    try {
      final body = {
        USER_ID: userId,
      };
      final result = await Api.post(body: body, url: Api.getUserByIdApi);
      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
