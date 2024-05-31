// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/models/NotificationModel.dart';
import '../../data/repositories/UserNotification/userNotiRepository.dart';
import '../../utils/constant.dart';

abstract class UserNotificationState {}

class UserNotificationInitial extends UserNotificationState {}

class UserNotificationFetchInProgress extends UserNotificationState {}

class UserNotificationFetchSuccess extends UserNotificationState {
  final List<NotificationModel> userUserNotification;
  final int totalUserNotificationCount;
  final bool hasMoreFetchError;
  final bool hasMore;

  UserNotificationFetchSuccess({
    required this.userUserNotification,
    required this.totalUserNotificationCount,
    required this.hasMoreFetchError,
    required this.hasMore,
  });
}

class UserNotificationFetchFailure extends UserNotificationState {
  final String errorMessage;

  UserNotificationFetchFailure(this.errorMessage);
}

class UserNotificationCubit extends Cubit<UserNotificationState> {
  final UserNotificationRepository _userUserNotificationRepository;

  UserNotificationCubit(this._userUserNotificationRepository) : super(UserNotificationInitial());

  void getUserNotification({required BuildContext context, required String userId}) async {
    try {
      emit(UserNotificationFetchInProgress());
      final result = await _userUserNotificationRepository.getUserNotification(limit: limitOfAPIData.toString(), offset: "0", context: context, userId: userId);

      emit(UserNotificationFetchSuccess(
        userUserNotification: result['UserNotification'],
        totalUserNotificationCount: int.parse(result['total']),
        hasMoreFetchError: false,
        hasMore: (result['UserNotification'] as List<NotificationModel>).length < int.parse(result['total']),
      ));
    } catch (e) {
      emit(UserNotificationFetchFailure(e.toString()));
    }
  }

  bool hasMoreUserNotification() {
    if (state is UserNotificationFetchSuccess) {
      return (state as UserNotificationFetchSuccess).hasMore;
    }
    return false;
  }

  void getMoreUserNotification({required BuildContext context, required String userId}) async {
    if (state is UserNotificationFetchSuccess) {
      try {
        final result = await _userUserNotificationRepository.getUserNotification(
            context: context, limit: limitOfAPIData.toString(), userId: userId, offset: (state as UserNotificationFetchSuccess).userUserNotification.length.toString());
        List<NotificationModel> updatedResults = (state as UserNotificationFetchSuccess).userUserNotification;
        updatedResults.addAll(result['UserNotification'] as List<NotificationModel>);
        emit(UserNotificationFetchSuccess(
          userUserNotification: updatedResults,
          totalUserNotificationCount: int.parse(result['total']),
          hasMoreFetchError: false,
          hasMore: updatedResults.length < int.parse(result['total']),
        ));
      } catch (e) {
        //in case of any error
        emit(UserNotificationFetchSuccess(
          userUserNotification: (state as UserNotificationFetchSuccess).userUserNotification,
          hasMoreFetchError: true,
          totalUserNotificationCount: (state as UserNotificationFetchSuccess).totalUserNotificationCount,
          hasMore: (state as UserNotificationFetchSuccess).hasMore,
        ));
      }
    }
  }

  void deleteUserNoti(String id) {
    if (state is UserNotificationFetchSuccess) {
      List<NotificationModel> userNotiList = (state as UserNotificationFetchSuccess).userUserNotification;
      var delItem = [];
      for (int i = 0; i < userNotiList.length; i++) {
        if (id.contains(",") && id.contains(userNotiList[i].id!)) {
          delItem.add(i);
        } else {
          if (userNotiList[i].id == id) {
            userNotiList.removeAt(i);
          }
        }
      }

      for (int j = 0; j < delItem.length; j++) {
        userNotiList.removeAt(delItem[j]);
      }

      emit(UserNotificationFetchSuccess(
          userUserNotification: userNotiList,
          hasMore: (state as UserNotificationFetchSuccess).hasMore,
          hasMoreFetchError: false,
          totalUserNotificationCount: (state as UserNotificationFetchSuccess).totalUserNotificationCount - (delItem.isEmpty ? 1 : delItem.length)));
    }
  }
}
