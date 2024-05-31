// ignore_for_file: file_names, non_constant_identifier_names

import 'package:flutter/cupertino.dart';
import 'package:news/data/models/NotificationModel.dart';
import 'package:news/data/repositories/UserNotification/userNotiRemoteDataSource.dart';

class UserNotificationRepository {
  static final UserNotificationRepository _UserNotificationRepository = UserNotificationRepository._internal();

  late UserNotificationRemoteDataSource _UserNotificationRemoteDataSource;

  factory UserNotificationRepository() {
    _UserNotificationRepository._UserNotificationRemoteDataSource = UserNotificationRemoteDataSource();
    return _UserNotificationRepository;
  }

  UserNotificationRepository._internal();

  Future<Map<String, dynamic>> getUserNotification({required BuildContext context, required String offset, required String limit, required String userId}) async {
    final result = await _UserNotificationRemoteDataSource.getUserNotifications(limit: limit, offset: offset, context: context, userId: userId);

    return {
      "total": result['total'],
      "UserNotification": (result['data'] as List).map((e) => NotificationModel.fromJson(e)).toList(),
    };
  }
}
