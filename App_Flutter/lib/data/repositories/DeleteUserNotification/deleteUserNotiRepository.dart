// ignore_for_file: file_names

import 'deleteUserNotiRemoteDataSource.dart';

class DeleteUserNotiRepository {
  static final DeleteUserNotiRepository _deleteUserNotiRepository = DeleteUserNotiRepository._internal();
  late DeleteUserNotiRemoteDataSource _deleteUserNotiRemoteDataSource;

  factory DeleteUserNotiRepository() {
    _deleteUserNotiRepository._deleteUserNotiRemoteDataSource = DeleteUserNotiRemoteDataSource();
    return _deleteUserNotiRepository;
  }

  DeleteUserNotiRepository._internal();

  Future setDeleteUserNoti({
    required String id,
  }) async {
    final result = await _deleteUserNotiRemoteDataSource.deleteUserNoti(id: id);
    return result;
  }
}
