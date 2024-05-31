// ignore_for_file: file_names

import 'package:news/utils/strings.dart';

class UserModel {
  String? id, image, message, dateSent, title, newsId, type, date;

  UserModel({this.id, this.image, this.message, this.title, this.dateSent, this.newsId, this.type, this.date});

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(id: json[ID], image: json[IMAGE], message: json[MESSAGE], dateSent: json[DATE_SENT], newsId: json[NEWS_ID], title: json[TITLE], type: json[TYPE], date: json[DATE]);
  }
}
