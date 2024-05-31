// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/models/NewsModel.dart';

import '../data/repositories/Videos/videosRepository.dart';
import '../utils/constant.dart';

abstract class VideoState {}

class VideoInitial extends VideoState {}

class VideoFetchInProgress extends VideoState {}

class VideoFetchSuccess extends VideoState {
  final List<NewsModel> video;
  final int totalVideoCount;
  final bool hasMoreFetchError;
  final bool hasMore;

  VideoFetchSuccess({
    required this.video,
    required this.totalVideoCount,
    required this.hasMoreFetchError,
    required this.hasMore,
  });
}

class VideoFetchFailure extends VideoState {
  final String errorMessage;

  VideoFetchFailure(this.errorMessage);
}

class VideoCubit extends Cubit<VideoState> {
  final VideoRepository _videoRepository;

  VideoCubit(this._videoRepository) : super(VideoInitial());

  void getVideo({required BuildContext context, required String langId}) async {
    try {
      emit(VideoFetchInProgress());
      final result = await _videoRepository.getVideo(limit: limitOfAPIData.toString(), offset: "0", langId: langId, context: context);
      emit(VideoFetchSuccess(
        video: result['Video'],
        totalVideoCount: int.parse(result['total']),
        hasMoreFetchError: false,
        hasMore: (result['Video'] as List<NewsModel>).length < int.parse(result['total']),
      ));
    } catch (e) {
      emit(VideoFetchFailure(e.toString()));
    }
  }

  bool hasMoreVideo() {
    if (state is VideoFetchSuccess) {
      return (state as VideoFetchSuccess).hasMore;
    }
    return false;
  }

  void getMoreVideo({required BuildContext context, required String langId}) async {
    if (state is VideoFetchSuccess) {
      try {
        final result = await _videoRepository.getVideo(context: context, langId: langId, limit: limitOfAPIData.toString(), offset: (state as VideoFetchSuccess).video.length.toString());
        List<NewsModel> updatedResults = (state as VideoFetchSuccess).video;
        updatedResults.addAll(result['Video'] as List<NewsModel>);
        emit(VideoFetchSuccess(
          video: updatedResults,
          totalVideoCount: int.parse(result['total']),
          hasMoreFetchError: false,
          hasMore: updatedResults.length < int.parse(result['total']),
        ));
      } catch (e) {
        //in case of any error
        emit(VideoFetchSuccess(
          video: (state as VideoFetchSuccess).video,
          hasMoreFetchError: true,
          totalVideoCount: (state as VideoFetchSuccess).totalVideoCount,
          hasMore: (state as VideoFetchSuccess).hasMore,
        ));
      }
    }
  }
}
