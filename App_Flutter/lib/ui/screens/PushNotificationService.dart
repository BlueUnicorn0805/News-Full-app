// ignore_for_file: file_names, depend_on_referenced_packages
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:news/data/repositories/Settings/settingsLocalDataRepository.dart';
import 'package:news/ui/screens/dashBoard/dashBoardScreen.dart';
import 'package:news/utils/constant.dart';
import 'package:path_provider/path_provider.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart';
import 'dart:io';
import 'package:news/cubits/NewsByIdCubit.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/app/app.dart';
import 'package:news/app/routes.dart';
import 'package:news/utils/uiUtils.dart';

FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin = FlutterLocalNotificationsPlugin();
FirebaseMessaging messaging = FirebaseMessaging.instance;
SettingsLocalDataRepository settingsRepo = SettingsLocalDataRepository();

backgroundMessage(NotificationResponse notificationResponse) {
  //for notification only
  if (notificationResponse.input?.isNotEmpty ?? false) {
    debugPrint('notification action tapped with input: ${notificationResponse.input}');
  }
  if (notificationResponse.payload!.isNotEmpty) debugPrint("payload is ${notificationResponse.payload}");
}

@pragma('vm:entry-point')
Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  if (message.data['type'] == "comment" || message.data['type'] == "comment_like") {
    var body = (message.data['message'] != null) ? message.data['message'].toString() : "";
    var payload = (message.data["news_id"] != null) ? message.data["news_id"] : "";
    generateSimpleNotification("", body, payload);
  }
}

void redirectToNewsDetailsScreen(RemoteMessage message, BuildContext context) {
  var data = message.data;
  if (data['type'] == "default" || data['type'] == "category") {
    var payload = data["news_id"];
    var lanId = data["language_id"] ?? "14";

    if (lanId == context.read<AppLocalizationCubit>().state.id) {
      //show only if Current language is Same as Notification Language
      if (payload == null) {
        Navigator.push(context, MaterialPageRoute(builder: (context) => const MyApp()));
      } else {
        context.read<NewsByIdCubit>().getNewsById(newsId: payload, langId: context.read<AppLocalizationCubit>().state.id, userId: context.read<AuthCubit>().getUserId()).then((value) {
          UiUtils.rootNavigatorKey.currentState!.pushNamed(Routes.newsDetails, arguments: {"model": value[0], "isFromBreak": false, "fromShowMore": false});
        }).catchError((e) {
          debugPrint(e.toString());
        });
      }
    }
  }
}

class PushNotificationService {
  late BuildContext context;

  PushNotificationService({required this.context});

  Future initialise() async {
    messaging.getToken();
    const AndroidInitializationSettings initializationSettingsAndroid = AndroidInitializationSettings('notification_icon');
    const DarwinInitializationSettings initializationSettingsIOS = DarwinInitializationSettings(requestAlertPermission: true, requestBadgePermission: false, requestSoundPermission: true);
    Future<dynamic> notificationHandler(RemoteMessage message) async {
      if (settingsRepo.getNotification()) {
        var data = message.data;
        var notif = message.notification;
        if (data.isNotEmpty && (data['type'] == "default" || data['type'] == "category" || data['type'] == "comment" || data['type'] == "comment_like" || data['type'] == "newlyadded")) {
          var title = (data['title'] != null) ? data['title'].toString() : appName;
          var body = (data['message'] != null) ? data['message'].toString() : "";
          var image = data['image'];
          var payload = data["news_id"];
          String lanId = (data["language_id"] != null) ? data["language_id"].toString() : "14";

          if (lanId == context.read<AppLocalizationCubit>().state.id) {
            //show only if Current language is Same as Notification Language
            (payload == null) ? payload = "" : payload = payload;
            (image != null && image != "") ? generateImageNotification(title, body, image, payload) : generateSimpleNotification(title, body, payload);
          }
        } else if (notif != null) {
          //Direct Firebase Notification
          RemoteNotification notification = notif;
          String title = notif.title.toString();
          String msg = notif.body.toString();
          String iosImg = (notification.apple != null && notification.apple!.imageUrl != null) ? notification.apple!.imageUrl! : "";
          String androidImg = (notification.android != null && notification.android!.imageUrl != null) ? notification.android!.imageUrl! : "";

          if (Platform.isIOS) {
            (iosImg != "") ? generateImageNotification(title, msg, notification.apple!.imageUrl!, '') : generateSimpleNotification(title, msg, '');
          }
          if (Platform.isAndroid) {
            (androidImg != "") ? generateImageNotification(title, msg, notification.android!.imageUrl!, '') : generateSimpleNotification(title, msg, '');
          }
        }
      }
    }

    //for android 13 - notification permission
    flutterLocalNotificationsPlugin.resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>()?.requestPermission();

    InitializationSettings initializationSettings = const InitializationSettings(
      android: initializationSettingsAndroid,
      iOS: initializationSettingsIOS,
    );

    await flutterLocalNotificationsPlugin.initialize(
      initializationSettings,
      onDidReceiveNotificationResponse: (NotificationResponse notificationResponse) {
        switch (notificationResponse.notificationResponseType) {
          case NotificationResponseType.selectedNotification:
            selectNotificationPayload(notificationResponse.payload!);
            break;
          case NotificationResponseType.selectedNotificationAction:
            debugPrint("notification-action-id--->${notificationResponse.actionId}==${notificationResponse.payload}");
            break;
        }
      },
      onDidReceiveBackgroundNotificationResponse: backgroundMessage,
    );
    messaging.getInitialMessage().then((RemoteMessage? message) async {
      if (message != null && message.data.isNotEmpty) {
        isNotificationReceivedInbg = true;
        notificationNewsId = message.data["news_id"];
      }
    });

    _startForegroundService();
    FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);
    FirebaseMessaging.onMessage.listen((RemoteMessage message) async {
      await notificationHandler(message);
    });

    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      redirectToNewsDetailsScreen(message, context);
    });
  }

  Future<void> _startForegroundService() async {
    const AndroidNotificationDetails androidNotificationDetails =
        AndroidNotificationDetails('com.sachkasath', 'news', channelDescription: 'your channel description', importance: Importance.max, priority: Priority.high, ticker: 'ticker');
    await flutterLocalNotificationsPlugin
        .resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>()
        ?.startForegroundService(1, 'plain title', 'plain body', notificationDetails: androidNotificationDetails, payload: '');
  }

  selectNotificationPayload(String? payload) async {
    if (payload != null && payload != "") {
      context.read<NewsByIdCubit>().getNewsById(newsId: payload, langId: context.read<AppLocalizationCubit>().state.id, userId: context.read<AuthCubit>().getUserId()).then((value) {
        UiUtils.rootNavigatorKey.currentState!.pushNamed(Routes.newsDetails, arguments: {"model": value[0], "isFromBreak": false, "fromShowMore": false});
      });
    }
  }
}

Future<String> _downloadAndSaveImage(String url, String fileName) async {
  if (url.isNotEmpty && url != "null") {
    final Directory directory = await getApplicationDocumentsDirectory();
    final String filePath = '${directory.path}/$fileName';
    final Response response = await get(Uri.parse(url));
    final File file = File(filePath);
    await file.writeAsBytes(response.bodyBytes);
    return filePath;
  } else {
    debugPrint("issue in downloading Notification image");
    return "";
  }
}

Future<void> generateImageNotification(String title, String msg, String image, String type) async {
  var largeIconPath = await _downloadAndSaveImage(image, Platform.isAndroid ? 'largeIcon' : 'largeIcon.png');
  var bigPicturePath = await _downloadAndSaveImage(image, Platform.isAndroid ? 'bigPicture' : 'bigPicture.png');
  var bigPictureStyleInformation =
      BigPictureStyleInformation(FilePathAndroidBitmap(bigPicturePath), hideExpandedLargeIcon: true, contentTitle: title, htmlFormatContentTitle: true, summaryText: msg, htmlFormatSummaryText: true);
  var androidPlatformChannelSpecifics = AndroidNotificationDetails('big text channel id', 'big text channel name',
      channelDescription: 'big text channel description', largeIcon: FilePathAndroidBitmap(largeIconPath), styleInformation: bigPictureStyleInformation);
  final DarwinNotificationDetails darwinNotificationDetails = DarwinNotificationDetails(
      categoryIdentifier: "", presentAlert: true, presentSound: true, attachments: <DarwinNotificationAttachment>[DarwinNotificationAttachment(bigPicturePath, hideThumbnail: false)]);
  var platformChannelSpecifics = NotificationDetails(android: androidPlatformChannelSpecifics, iOS: darwinNotificationDetails);
  await flutterLocalNotificationsPlugin.show(1, title, msg, platformChannelSpecifics, payload: type);
}

Future<void> generateSimpleNotification(String title, String msg, String type) async {
  var androidPlatformChannelSpecifics = const AndroidNotificationDetails(
      'com.sachkasath', //your package name
      'news',
      importance: Importance.max,
      priority: Priority.high,
      ticker: 'ticker');
  DarwinNotificationDetails darwinNotificationDetails = const DarwinNotificationDetails(categoryIdentifier: "", presentAlert: true, presentSound: true);
  var platformChannelSpecifics = NotificationDetails(android: androidPlatformChannelSpecifics, iOS: darwinNotificationDetails);
  await flutterLocalNotificationsPlugin.show(1, title, msg, platformChannelSpecifics, payload: type);
}
