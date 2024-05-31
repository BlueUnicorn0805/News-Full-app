// ignore_for_file: file_names

import 'package:news/utils/strings.dart';

class AdSpaceModel {
  String? id, adSpace, adFeaturedSectionId, adImage, adUrl;

  AdSpaceModel({this.id, this.adSpace, this.adFeaturedSectionId, this.adImage, this.adUrl});
//place Ad just above mentioned adFeaturedSectionId

  factory AdSpaceModel.fromJson(Map<String, dynamic> json) {
    return AdSpaceModel(
      id: json[ID],
      adSpace: json[AD_SPACE],
      adFeaturedSectionId: json[AD_FEATURED_SECTION_ID],
      adImage: json[AD_IMAGE],
      adUrl: json[AD_URL],
    );
  }
}
