// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';

import 'package:news/data/models/NotificationModel.dart';

import 'notiRemoteDataSource.dart';

class NotificationRepository {
  static final NotificationRepository _notificationRepository = NotificationRepository._internal();

  late NotificationRemoteDataSource _notificationRemoteDataSource;

  factory NotificationRepository() {
    _notificationRepository._notificationRemoteDataSource = NotificationRemoteDataSource();
    return _notificationRepository;
  }

  NotificationRepository._internal();

  Future<Map<String, dynamic>> getNotification({required BuildContext context, required String offset, required String limit}) async {
    final result = await _notificationRemoteDataSource.getNotifications(limit: limit, offset: offset, context: context );

    return {
      "total": result['total'],
      "Notification": (result['data'] as List).map((e) => NotificationModel.fromJson(e)).toList(),
    };
  }
}
