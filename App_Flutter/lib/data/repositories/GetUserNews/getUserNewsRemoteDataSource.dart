// ignore_for_file: file_names

import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';

class GetUserNewsRemoteDataSource {
  Future<dynamic> getGetUserNews({required String limit, required String offset, required String userId, required String langId}) async {
    try {
      final body = {LIMIT: limit, OFFSET: offset, USER_ID: userId, USER_NEWS: userId};

      final result = await Api.post(body: body, url: Api.getNewsApi);
      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
