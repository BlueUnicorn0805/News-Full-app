// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:news/data/repositories/SetUserPreferenceCat/setUserPrefCatRemoteDataSource.dart';

class SetUserPrefCatRepository {
  static final SetUserPrefCatRepository _setUserPrefCatRepository = SetUserPrefCatRepository._internal();

  late SetUserPrefCatRemoteDataSource _setUserPrefCatRemoteDataSource;

  factory SetUserPrefCatRepository() {
    _setUserPrefCatRepository._setUserPrefCatRemoteDataSource = SetUserPrefCatRemoteDataSource();
    return _setUserPrefCatRepository;
  }

  SetUserPrefCatRepository._internal();

  Future<Map<String, dynamic>> setUserPrefCat({required BuildContext context, required String catId, required String userId}) async {
    final result = await _setUserPrefCatRemoteDataSource.setUserPrefCat(context: context, userId: userId, catId: catId);

    return {
      "SetUserPrefCat": result['data'],
    };
  }
}
