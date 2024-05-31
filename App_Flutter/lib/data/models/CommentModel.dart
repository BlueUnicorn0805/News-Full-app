// ignore_for_file: unnecessary_null_comparison, file_names

import '../../utils/strings.dart';

class CommentModel {
  String? id, message, profile, date, name, status, like, dislike, totalLikes, totalDislikes, userId;
  List<ReplyModel>? replyComList;

  CommentModel({this.id, this.message, this.profile, this.date, this.name, this.replyComList, this.status, this.like, this.dislike, this.totalLikes, this.totalDislikes, this.userId});

  factory CommentModel.fromJson(Map<String, dynamic> json) {
    var replyList = (json[REPLY] as List);
    List<ReplyModel> replyData = [];
    if (replyList == null || replyList.isEmpty) {
      replyList = [];
    } else {
      replyData = replyList.map((data) => ReplyModel.fromJson(data)).toList();
    }
    return CommentModel(
        id: json[ID],
        message: json[MESSAGE],
        profile: json[PROFILE],
        name: json[NAME],
        date: json[DATE],
        status: json[STATUS],
        replyComList: replyData,
        like: json[LIKE],
        dislike: json[DISLIKE],
        totalDislikes: json[TOTAL_DISLIKE],
        totalLikes: json[TOTAL_LIKE],
        userId: json[USER_ID]);
  }
}

class ReplyModel {
  String? id, message, profile, date, name, userId, parentId, newsId, status, like, dislike, totalLikes, totalDislikes;

  ReplyModel({this.id, this.message, this.profile, this.date, this.name, this.userId, this.parentId, this.status, this.newsId, this.like, this.dislike, this.totalLikes, this.totalDislikes});

  factory ReplyModel.fromJson(Map<String, dynamic> json) {
    return ReplyModel(
        id: json[ID],
        message: json[MESSAGE],
        profile: json[PROFILE],
        name: json[NAME],
        date: json[DATE],
        userId: json[USER_ID],
        parentId: json[PARENT_ID],
        newsId: json[NEWS_ID],
        status: json[STATUS],
        like: json[LIKE],
        dislike: json[DISLIKE],
        totalDislikes: json[TOTAL_DISLIKE],
        totalLikes: json[TOTAL_LIKE]);
  }
}
