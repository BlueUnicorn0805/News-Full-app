// ignore_for_file: file_names

import 'package:news/utils/strings.dart';

class LanguageModel {
  String? id, language, languageDisplayName, image, code, isRtl;

  LanguageModel({this.id, this.image, this.language, this.languageDisplayName, this.code, this.isRtl});

  factory LanguageModel.fromJson(Map<String, dynamic> json) {
    return LanguageModel(
      id: json[ID],
      image: json[IMAGE],
      language: json[LANGUAGE],
      languageDisplayName: (json[DISPLAY_NAME_LANG] != "") ? json[DISPLAY_NAME_LANG] : json[LANGUAGE],
      code: json[CODE],
      isRtl: json[ISRTL],
    );
  }
}
