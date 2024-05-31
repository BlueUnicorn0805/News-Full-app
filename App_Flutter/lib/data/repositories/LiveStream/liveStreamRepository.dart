// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:news/data/models/LiveStreamingModel.dart';

import 'liveRemoteDataSource.dart';

class LiveStreamRepository {
  static final LiveStreamRepository _liveStreamRepository = LiveStreamRepository._internal();

  late LiveStreamRemoteDataSource _liveStreamRemoteDataSource;

  factory LiveStreamRepository() {
    _liveStreamRepository._liveStreamRemoteDataSource = LiveStreamRemoteDataSource();
    return _liveStreamRepository;
  }

  LiveStreamRepository._internal();

  Future<Map<String, dynamic>> getLiveStream({required BuildContext context, required String langId}) async {
    final result = await _liveStreamRemoteDataSource.getLiveStreams(context: context, langId: langId);

    return {
      "LiveStream": (result['data'] as List).map((e) => LiveStreamingModel.fromJson(e)).toList(),
    };
  }
}
