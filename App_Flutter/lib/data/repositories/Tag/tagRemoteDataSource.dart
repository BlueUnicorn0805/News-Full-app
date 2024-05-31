// ignore_for_file: file_names

import '../../../utils/api.dart';
import '../../../utils/strings.dart';

class TagRemoteDataSource {
  Future<dynamic> getTag({required String langId}) async {
    try {
      final body = {LANGUAGE_ID: langId};
      final result = await Api.post(body: body, url: Api.getTagsApi);
      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
