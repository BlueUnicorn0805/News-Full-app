// ignore_for_file: file_names

import 'package:news/data/repositories/NewsComment/LikeAndDislikeComment/likeAndDislikeCommDataSource.dart';

class LikeAndDislikeCommRepository {
  static final LikeAndDislikeCommRepository _likeAndDislikeCommRepository = LikeAndDislikeCommRepository._internal();
  late LikeAndDislikeCommRemoteDataSource _likeAndDislikeCommRemoteDataSource;

  factory LikeAndDislikeCommRepository() {
    _likeAndDislikeCommRepository._likeAndDislikeCommRemoteDataSource = LikeAndDislikeCommRemoteDataSource();
    return _likeAndDislikeCommRepository;
  }

  LikeAndDislikeCommRepository._internal();

  Future setLikeAndDislikeComm({required String userId, required String commId, required String langId, required String status}) async {
    final result = await _likeAndDislikeCommRemoteDataSource.likeAndDislikeComm(userId: userId, commId: commId, status: status, langId: langId);
    return result;
  }
}
