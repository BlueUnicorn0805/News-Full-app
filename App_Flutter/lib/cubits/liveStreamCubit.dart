// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../data/models/LiveStreamingModel.dart';
import '../data/repositories/LiveStream/liveStreamRepository.dart';

abstract class LiveStreamState {}

class LiveStreamInitial extends LiveStreamState {}

class LiveStreamFetchInProgress extends LiveStreamState {}

class LiveStreamFetchSuccess extends LiveStreamState {
  final List<LiveStreamingModel> liveStream;

  LiveStreamFetchSuccess({
    required this.liveStream,
  });
}

class LiveStreamFetchFailure extends LiveStreamState {
  final String errorMessage;

  LiveStreamFetchFailure(this.errorMessage);
}

class LiveStreamCubit extends Cubit<LiveStreamState> {
  final LiveStreamRepository _liveStreamRepository;

  LiveStreamCubit(this._liveStreamRepository) : super(LiveStreamInitial());

  void getLiveStream({required BuildContext context, required String langId}) async {
    try {
      emit(LiveStreamFetchInProgress());
      final result = await _liveStreamRepository.getLiveStream(context: context, langId: langId);

      emit(LiveStreamFetchSuccess(liveStream: result['LiveStream']));
    } catch (e) {
      emit(LiveStreamFetchFailure(e.toString()));
    }
  }
}
