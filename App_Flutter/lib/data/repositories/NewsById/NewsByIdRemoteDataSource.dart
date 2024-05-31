// ignore_for_file: file_names

import '../../../utils/api.dart';
import '../../../utils/strings.dart';

class NewsByIdRemoteDataSource {
  Future<dynamic> getNewsById({required String newsId, required String langId, required String userId}) async {
    try {
      final body = {USER_ID: userId, LANGUAGE_ID: langId, NEWS_ID: newsId};
      final result = await Api.post(body: body, url: Api.getNewsByIdApi);

      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
