// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/data/models/FeatureSectionModel.dart';
import 'package:news/data/repositories/FeatureSection/sectionRemoteDataSource.dart';

class SectionRepository {
  static final SectionRepository _sectionRepository = SectionRepository._internal();

  late SectionRemoteDataSource _sectionRemoteDataSource;

  factory SectionRepository() {
    _sectionRepository._sectionRemoteDataSource = SectionRemoteDataSource();
    return _sectionRepository;
  }

  SectionRepository._internal();

  Future<Map<String, dynamic>> getSection({
    required BuildContext context,
    required String langId,
    required String userId,
  }) async {
    final result = await _sectionRemoteDataSource.getSections(context: context, langId: langId, userId: userId);

    return {
      "Section": (result['data'] as List).map((e) => FeatureSectionModel.fromJson(e)).toList(),
    };
  }
}
