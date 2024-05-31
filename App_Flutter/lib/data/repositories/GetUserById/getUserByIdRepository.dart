// ignore_for_file: file_names

import 'package:flutter/material.dart';

import 'getUserByIdDataSource.dart';

class GetUserByIdRepository {
  static final GetUserByIdRepository _getUserByIdRepository = GetUserByIdRepository._internal();

  late GetUserByIdRemoteDataSource _getUserByIdRemoteDataSource;

  factory GetUserByIdRepository() {
    _getUserByIdRepository._getUserByIdRemoteDataSource = GetUserByIdRemoteDataSource();
    return _getUserByIdRepository;
  }

  GetUserByIdRepository._internal();

  Future<List<dynamic>> getUserById({required BuildContext context, required String userId}) async {
    final result = await _getUserByIdRemoteDataSource.getUserById(context: context, userId: userId);
    return result['data'];
  }
}
