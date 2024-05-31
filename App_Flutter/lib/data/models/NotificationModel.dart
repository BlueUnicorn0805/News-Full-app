//notification model fetch category data from server side
// ignore_for_file: file_names

import 'package:news/utils/strings.dart';

class NotificationModel {
  String? id, image, message, dateSent, title, newsId, type, date;
  bool isReadMore;

  NotificationModel({this.id, this.image, this.message, this.title, this.dateSent, this.newsId, this.type, this.date, this.isReadMore = false});

  factory NotificationModel.fromJson(Map<String, dynamic> json) {
    return NotificationModel(
        id: json[ID], image: json[IMAGE], message: json[MESSAGE], dateSent: json[DATE_SENT], newsId: json[NEWS_ID], title: json[TITLE], type: json[TYPE], date: json[DATE], isReadMore: false);
  }
}
