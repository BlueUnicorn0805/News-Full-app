// ignore_for_file: file_names

import 'package:news/data/repositories/NewsComment/FlagComment/flagCommRemoteDataSource.dart';

class SetFlagRepository {
  static final SetFlagRepository _setFlagRepository = SetFlagRepository._internal();

  late SetFlagRemoteDataSource _setFlagRemoteDataSource;

  factory SetFlagRepository() {
    _setFlagRepository._setFlagRemoteDataSource = SetFlagRemoteDataSource();
    return _setFlagRepository;
  }

  SetFlagRepository._internal();

  Future<Map<String, dynamic>> setFlag({required String userId, required String commId, required String newsId, required String message}) async {
    final result = await _setFlagRemoteDataSource.setFlag(userId: userId, commId: commId, newsId: newsId, message: message);
    return result;
  }
}
