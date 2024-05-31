// ignore_for_file: unnecessary_null_comparison, file_names

import 'package:news/utils/strings.dart';

class SurveyModel {
  String? id, question, status;
  List<OptionModel>? optionDataList;

  SurveyModel({
    this.id,
    this.question,
    this.status,
    this.optionDataList,
  });

  factory SurveyModel.fromJson(Map<String, dynamic> json) {
    var optionList = (json[OPTION] as List);
    List<OptionModel> optionData = [];
    if (optionList == null || optionList.isEmpty) {
      optionList = [];
    } else {
      optionData = optionList.map((data) => OptionModel.fromJson(data)).toList();
    }

    return SurveyModel(id: json[ID], question: json[QUESTION], status: json[STATUS], optionDataList: optionData);
  }
}

class OptionModel {
  String? id;
  String? options;
  String? counter;
  String? percentage;
  String? questionId;

  OptionModel({this.id, this.options, this.counter, this.percentage, this.questionId});

  factory OptionModel.fromJson(Map<String, dynamic> json) {
    return OptionModel(id: json[ID], options: json[OPTIONS], counter: json[COUNTER], percentage: json[PERCENTAGE], questionId: json[QUESTION_ID]);
  }
}
