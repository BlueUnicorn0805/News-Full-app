// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import 'package:news/data/models/NotificationModel.dart';
import 'package:news/data/repositories/Notification/notificationRepository.dart';
import 'package:news/utils/constant.dart';

abstract class NotificationState {}

class NotificationInitial extends NotificationState {}

class NotificationFetchInProgress extends NotificationState {}

class NotificationFetchSuccess extends NotificationState {
  final List<NotificationModel> notification;
  final int totalNotificationCount;
  final bool hasMoreFetchError;
  final bool hasMore;

  NotificationFetchSuccess({
    required this.notification,
    required this.totalNotificationCount,
    required this.hasMoreFetchError,
    required this.hasMore,
  });
}

class NotificationFetchFailure extends NotificationState {
  final String errorMessage;

  NotificationFetchFailure(this.errorMessage);
}

class NotificationCubit extends Cubit<NotificationState> {
  final NotificationRepository _notificationRepository;

  NotificationCubit(this._notificationRepository) : super(NotificationInitial());

  void getNotification({required BuildContext context}) async {
    try {
      emit(NotificationFetchInProgress());
      final result = await _notificationRepository.getNotification(limit: limitOfAPIData.toString(), offset: "0", context: context);
      emit(NotificationFetchSuccess(
        notification: result['Notification'],
        totalNotificationCount: int.parse(result['total']),
        hasMoreFetchError: false,
        hasMore: (result['Notification'] as List<NotificationModel>).length < int.parse(result['total']),
      ));
    } catch (e) {
      emit(NotificationFetchFailure(e.toString()));
    }
  }

  bool hasMoreNotification() {
    if (state is NotificationFetchSuccess) {
      return (state as NotificationFetchSuccess).hasMore;
    }
    return false;
  }

  void getMoreNotification({required BuildContext context}) async {
    if (state is NotificationFetchSuccess) {
      try {
        final result = await _notificationRepository.getNotification(context: context, limit: limitOfAPIData.toString(), offset: (state as NotificationFetchSuccess).notification.length.toString());
        List<NotificationModel> updatedResults = (state as NotificationFetchSuccess).notification;
        updatedResults.addAll(result['Notification'] as List<NotificationModel>);
        emit(NotificationFetchSuccess(
          notification: updatedResults,
          totalNotificationCount: int.parse(result['total']),
          hasMoreFetchError: false,
          hasMore: updatedResults.length < int.parse(result['total']),
        ));
      } catch (e) {
        //in case of any error
        emit(NotificationFetchSuccess(
          notification: (state as NotificationFetchSuccess).notification,
          hasMoreFetchError: true,
          totalNotificationCount: (state as NotificationFetchSuccess).totalNotificationCount,
          hasMore: (state as NotificationFetchSuccess).hasMore,
        ));
      }
    }
  }
}
