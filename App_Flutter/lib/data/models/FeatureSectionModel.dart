// ignore_for_file: file_names

import 'package:news/data/models/BreakingNewsModel.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/data/models/adSpaceModel.dart';
import 'package:news/utils/strings.dart';

class FeatureSectionModel {
  String? id;
  String? languageId;
  String? title;
  String? shortDescription;
  String? newsType;
  String? videosType;
  String? filterType;
  String? categoryIds;
  String? subcategoryIds;
  String? newsIds;
  String? styleApp;
  String? rowOrder;
  String? createdAt;
  String? status;
  int? newsTotal;
  int? breakNewsTotal;
  int? videosTotal;
  List<NewsModel>? news;
  List<BreakingNewsModel>? breakNews;
  List<NewsModel>? videos;
  List<BreakingNewsModel>? breakVideos;
  AdSpaceModel? adSpaceDetails;

  FeatureSectionModel(
      {this.id,
      this.languageId,
      this.title,
      this.shortDescription,
      this.newsType,
      this.videosType,
      this.filterType,
      this.categoryIds,
      this.subcategoryIds,
      this.newsIds,
      this.styleApp,
      this.rowOrder,
      this.createdAt,
      this.status,
      this.newsTotal,
      this.breakNewsTotal,
      this.videosTotal,
      this.news,
      this.breakNews,
      this.videos,
      this.breakVideos,
      this.adSpaceDetails});

  factory FeatureSectionModel.fromJson(Map<String, dynamic> json) {
    List<NewsModel> newsData = [];
    if (json.containsKey(NEWS)) {
      var newsList = (json[NEWS] as List);
      if (newsList.isEmpty) {
        newsList = [];
      } else {
        newsData = newsList.map((data) => NewsModel.fromJson(data)).toList();
      }
    }

    List<BreakingNewsModel> breakNewsData = [];
    if (json.containsKey(BREAKING_NEWS)) {
      var breakNewsList = (json[BREAKING_NEWS] as List);
      if (breakNewsList.isEmpty) {
        breakNewsList = [];
      } else {
        breakNewsData = breakNewsList.map((data) => BreakingNewsModel.fromJson(data)).toList();
      }
    }

    List<NewsModel> videosData = [];
    List<BreakingNewsModel> breakVideosData = [];
    if (json.containsKey(VIDEOS)) {
      var videosList = (json[VIDEOS] as List);
      if (videosList.isEmpty) {
        videosList = [];
      } else {
        if (json[VIDEOS_TYPE] == 'news') {
          videosData = videosList.map((data) => NewsModel.fromVideos(data)).toList();
        } else {
          breakVideosData = videosList.map((data) => BreakingNewsModel.fromJson(data)).toList();
        }
      }
    }
    AdSpaceModel? adSpaceData;
    if (json.containsKey(AD_SPACES)) {
      adSpaceData = AdSpaceModel.fromJson(json[AD_SPACES]);
    }

    return FeatureSectionModel(
        id: json[ID],
        languageId: json[LANGUAGE_ID],
        title: json[TITLE],
        shortDescription: json[SHORT_DESC],
        newsType: json[NEWS_TYPE],
        videosType: json[VIDEOS_TYPE],
        filterType: json[FILTER_TYPE],
        categoryIds: json[CAT_IDS],
        subcategoryIds: json[SUBCAT_IDS],
        newsIds: json[NEWS_IDS],
        styleApp: json[STYLE_APP],
        status: json[STATUS],
        newsTotal: json[NEWS_TOTAL],
        breakNewsTotal: json[BREAK_NEWS_TOTAL],
        videosTotal: json[VIDEOS_TOTAL],
        news: newsData,
        breakNews: breakNewsData,
        videos: videosData,
        breakVideos: breakVideosData,
        adSpaceDetails: adSpaceData);
  }
}
