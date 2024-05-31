// ignore_for_file: file_names

import 'package:news/data/models/FeatureSectionModel.dart';
import 'package:news/data/repositories/SectionById/sectionByIdRemoteDataSource.dart';

class SectionByIdRepository {
  static final SectionByIdRepository _sectionByIdRepository = SectionByIdRepository._internal();

  late SectionByIdRemoteDataSource _sectionByIdRemoteDataSource;

  factory SectionByIdRepository() {
    _sectionByIdRepository._sectionByIdRemoteDataSource = SectionByIdRemoteDataSource();
    return _sectionByIdRepository;
  }

  SectionByIdRepository._internal();

  Future<List<FeatureSectionModel>> getSectionById({required String userId, required String langId, required String sectionId}) async {
    final result = await _sectionByIdRemoteDataSource.getSectionById(langId: langId, userId: userId, sectionId: sectionId);

    return (result['data'] as List).map((e) => FeatureSectionModel.fromJson(e)).toList();
  }
}
