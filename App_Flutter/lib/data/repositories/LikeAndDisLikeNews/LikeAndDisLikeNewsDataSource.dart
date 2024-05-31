// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:news/utils/strings.dart';
import '../../../utils/api.dart';

class LikeAndDisLikeRemoteDataSource {
  Future<dynamic> getLikeAndDisLike({required String userId, required String langId, required BuildContext context, required String offset, required String perPage}) async {
    try {
      final body = {USER_ID: userId, LANGUAGE_ID: langId, OFFSET: offset, LIMIT: perPage};

      final result = await Api.post(
        body: body,
        url: Api.getLikeNewsApi,
      );

      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }

  Future addAndRemoveLikeAndDisLike({required String userId, required String newsId, required BuildContext context, required String status}) async {
    try {
      final body = {USER_ID: userId, NEWS_ID: newsId, STATUS: status};
      final result = await Api.post(
        body: body,
        url: Api.setLikesDislikesApi,
      );
      return result['data'];
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
