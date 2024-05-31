// ignore_for_file: file_names

import 'package:news/utils/api.dart';

class SystemRepository {
  Future<dynamic> fetchSettings() async {
    try {
      final result = await Api.post(
        url: Api.getSettingApi,
        body: {},
      );

      return result['data'];
    } catch (e) {
      throw ApiException(e.toString());
    }
  }
}
