// ignore_for_file: file_names

import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';

class LikeAndDislikeCommRemoteDataSource {
  Future likeAndDislikeComm({required String userId, required String commId, required String langId, required String status}) async {
    try {
      final body = {USER_ID: userId, COMMENT_ID: commId, STATUS: status, LANGUAGE_ID: langId};
      final result = await Api.post(
        body: body,
        url: Api.setLikeDislikeComApi,
      );
      return result['data'];
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
