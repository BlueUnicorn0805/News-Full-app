// ignore_for_file: file_names

import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';

class SetNewsViewsDataRemoteDataSource {
  Future<dynamic> setNewsViews({required String userId, required String newsId, required bool isBreakingNews}) async {
    try {
      final body = {USER_ID: userId, if (isBreakingNews) BR_NEWS_ID: newsId, if (!isBreakingNews) NEWS_ID: newsId};
      final result = await Api.post(body: body, url: (isBreakingNews) ? Api.setBreakingNewsViewApi : Api.setNewsViewApi);
      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
