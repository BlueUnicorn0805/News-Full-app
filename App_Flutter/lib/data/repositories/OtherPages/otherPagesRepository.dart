// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/data/models/OtherPageModel.dart';
import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';
import 'otherPageRemoteDataSorce.dart';

class OtherPageRepository {
  static final OtherPageRepository _otherPageRepository = OtherPageRepository._internal();

  late OtherPageRemoteDataSource _otherPageRemoteDataSource;

  factory OtherPageRepository() {
    _otherPageRepository._otherPageRemoteDataSource = OtherPageRemoteDataSource();
    return _otherPageRepository;
  }

  OtherPageRepository._internal();

  Future<Map<String, dynamic>> getOtherPage({required BuildContext context, required String langId}) async {
    final result = await _otherPageRemoteDataSource.getOtherPages(context: context, langId: langId);

    return {
      "OtherPage": (result['data'] as List).map((e) => OtherPageModel.fromJson(e)).toList(),
    };
  }

  //get only privacy policy & Terms Conditions only
  Future<Map<String, dynamic>> getPrivacyTermsPage({required BuildContext context, required String langId}) async {
    try {
      final body = {LANGUAGE_ID: langId};
      final result = await Api.post(body: body, url: Api.getPolicyPagesApi);
      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
