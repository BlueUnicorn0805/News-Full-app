// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import '../../../utils/api.dart';
import '../../../utils/strings.dart';

class UserNotificationRemoteDataSource {
  Future<dynamic> getUserNotifications({required String limit, required String offset, required BuildContext context, required String userId}) async {
    try {
      final body = {LIMIT: limit, OFFSET: offset, USER_ID: userId};
      final result = await Api.post(body: body, url: Api.getUserNotificationApi);
      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
