// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:news/data/models/appLanguageModel.dart';
import 'package:news/data/repositories/language/languageRemoteDataSource.dart';

class LanguageRepository {
  static final LanguageRepository _languageRepository = LanguageRepository._internal();

  late LanguageRemoteDataSource _languageRemoteDataSource;

  factory LanguageRepository() {
    _languageRepository._languageRemoteDataSource = LanguageRemoteDataSource();
    return _languageRepository;
  }

  LanguageRepository._internal();

  Future<Map<String, dynamic>> getLanguage({required BuildContext context}) async {
    final result = await _languageRemoteDataSource.getLanguages(context: context);

    return {
      "Language": (result['data'] as List).map((e) => LanguageModel.fromJson(e)).toList(),
    };
  }
}
