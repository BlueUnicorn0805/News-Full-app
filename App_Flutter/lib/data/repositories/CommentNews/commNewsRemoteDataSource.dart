// ignore_for_file: file_names

import 'package:flutter/material.dart';

import '../../../utils/api.dart';
import '../../../utils/strings.dart';

class CommentNewsRemoteDataSource {
  Future<dynamic> getCommentNews({required String limit, required String offset, required String userId, required String newsId, required BuildContext context}) async {
    try {
      final body = {LIMIT: limit, OFFSET: offset, USER_ID: userId, NEWS_ID: newsId};
      final result = await Api.post(body: body, url: Api.getCommentByNewsApi);
      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
