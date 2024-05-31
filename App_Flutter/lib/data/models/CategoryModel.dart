// ignore_for_file: unnecessary_null_comparison, file_names
import '../../utils/strings.dart';

class CategoryModel {
  String? id, image, categoryName, categoryId;
  List<SubCategoryModel>? subData;

  CategoryModel({this.id, this.image, this.categoryName, this.categoryId, this.subData});

  factory CategoryModel.fromJson(Map<String, dynamic> json) {
    var subList = (json[SUBCATEGORY] as List);
    List<SubCategoryModel> subCatData = [];
    if (subList == null || subList.isEmpty) {
      subList = [];
    } else {
      subCatData = subList.map((data) => SubCategoryModel.fromJson(data)).toList();
    }
    return CategoryModel(
      id: json[ID],
      image: json[IMAGE],
      categoryName: json[CATEGORY_NAME],
      subData: subCatData,
    );
  }
}

class SubCategoryModel {
  String? id, image, categoryId, subCatName;

  SubCategoryModel({this.id, this.image, this.categoryId, this.subCatName});

  factory SubCategoryModel.fromJson(Map<String, dynamic> json) {
    return SubCategoryModel(id: json[ID], image: json[IMAGE], categoryId: json[CATEGORY_ID], subCatName: json[SUBCAT_NAME]);
  }
}
