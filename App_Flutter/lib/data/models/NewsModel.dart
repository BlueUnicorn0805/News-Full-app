// ignore_for_file: unnecessary_null_comparison, file_names
import 'package:news/utils/strings.dart';
import 'SurveyModel.dart';

class NewsModel {
  String? id;
  String? userId;
  String? newsId;
  String? categoryId;
  String? title;
  String? date;
  String? contentType;
  String? contentValue;
  String? image;
  String? desc;
  String? categoryName;
  String? counter;
  String? dateSent;
  String? totalLikes;
  String? like;
  String? bookmark;
  String? keyName;
  String? tagId;
  String? tagName;
  String? dislike;
  String? totalDislike;
  String? subCatId;
  String? img;
  String? subCatName;
  String? showTill;
  String? langId;
  String? totalViews;

  List<ImageDataModel>? imageDataList;

  bool? history = false;
  String? question, status, type;
  List<OptionModel>? optionDataList;
  int? from;

  NewsModel(
      {this.id,
      this.userId,
      this.newsId,
      this.categoryId,
      this.title,
      this.date,
      this.contentType,
      this.contentValue,
      this.image,
      this.desc,
      this.categoryName,
      this.counter,
      this.dateSent,
      this.imageDataList,
      this.totalLikes,
      this.like,
      this.keyName,
      this.tagName,
      this.dislike,
      this.subCatId,
      this.totalDislike,
      this.tagId,
      this.history,
      this.optionDataList,
      this.question,
      this.status,
      this.type,
      this.from,
      this.img,
      this.subCatName,
      this.showTill,
      this.bookmark,
      this.langId,
      this.totalViews});

  factory NewsModel.history(String history) {
    return NewsModel(title: history, history: true);
  }

  factory NewsModel.fromSurvey(Map<String, dynamic> json) {
    List<OptionModel> optionList = (json[OPTION] as List).map((data) => OptionModel.fromJson(data)).toList();

    return NewsModel(id: json[ID], question: json[QUESTION], status: json[STATUS], optionDataList: optionList, type: "survey", from: 1);
  }

  factory NewsModel.fromVideos(Map<String, dynamic> json) {
    return NewsModel(
      id: json[ID],
      newsId: json[ID], //for bookmark get/set
      date: json[DATE],
      image: json[IMAGE],
      title: json[TITLE],
      contentType: json[CONTENT_TYPE],
      contentValue: json[CONTENT_VALUE],
    );
  }

  factory NewsModel.fromJson(Map<String, dynamic> json) {
    String? tagName;
    if (json[TAG_NAME] == null) {
      tagName = "";
    } else {
      tagName = json[TAG_NAME];
    }
    List<ImageDataModel> imageData = [];
    var imageList = (json[IMAGE_DATA] as List);
    if (imageList == null || imageList.isEmpty) {
      imageList = [];
    } else {
      imageData = imageList.map((data) => ImageDataModel.fromJson(data)).toList();
    }

    return NewsModel(
        id: json[ID],
        userId: json[USER_ID],
        newsId: json[NEWS_ID] ?? json[ID], //incase of null newsId in Response
        categoryId: json[CATEGORY_ID],
        title: json[TITLE],
        date: json[DATE],
        contentType: json[CONTENT_TYPE],
        contentValue: json[CONTENT_VALUE],
        image: json[IMAGE],
        desc: json[DESCRIPTION],
        categoryName: json[CATEGORY_NAME],
        counter: json[COUNTER],
        dateSent: json[DATE_SENT],
        imageDataList: imageData,
        totalLikes: json[TOTAL_LIKE],
        like: json[LIKE],
        bookmark: json[BOOKMARK],
        tagId: json[TAG_ID],
        tagName: tagName,
        dislike: json[DISLIKE],
        subCatId: json[SUBCAT_ID],
        totalDislike: json[TOTAL_DISLIKE],
        history: false,
        type: "news",
        img: "",
        subCatName: json[SUBCAT_NAME],
        showTill: json[SHOW_TILL],
        langId: json[LANGUAGE_ID],
        totalViews: json[TOTAL_VIEWS]);
  }
}

class ImageDataModel {
  String? id;
  String? otherImage;

  ImageDataModel({this.otherImage, this.id});

  factory ImageDataModel.fromJson(Map<String, dynamic> json) {
    return ImageDataModel(otherImage: json[OTHER_IMAGE], id: json[ID]);
  }
}
