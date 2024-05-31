// ignore_for_file: file_names

import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';

class LanguageJsonRemoteDataSource {
  Future<dynamic> getLanguageJson({required String lanCode}) async {
    try {
      final body = {CODE: lanCode};
      final result = await Api.post(body: body, url: Api.getLangJsonDataApi);

      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
