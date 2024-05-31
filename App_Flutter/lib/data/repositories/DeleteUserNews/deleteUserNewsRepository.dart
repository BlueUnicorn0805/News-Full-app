// ignore_for_file: file_names

import 'deleteUserNewsRemoteDataSource.dart';

class DeleteUserNewsRepository {
  static final DeleteUserNewsRepository _deleteUserNewsRepository = DeleteUserNewsRepository._internal();
  late DeleteUserNewsRemoteDataSource _deleteUserNewsRemoteDataSource;

  factory DeleteUserNewsRepository() {
    _deleteUserNewsRepository._deleteUserNewsRemoteDataSource = DeleteUserNewsRemoteDataSource();
    return _deleteUserNewsRepository;
  }

  DeleteUserNewsRepository._internal();

  Future setDeleteUserNews({
    required String newsId,
  }) async {
    final result = await _deleteUserNewsRemoteDataSource.deleteUserNews(newsId: newsId);
    return result;
  }
}
