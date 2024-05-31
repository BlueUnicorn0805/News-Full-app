// ignore_for_file: file_names, library_private_types_in_public_api, use_build_context_synchronously, must_be_immutable

import 'dart:async';
import 'dart:io';
import 'package:flutter/cupertino.dart';
import 'package:intl/intl.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:image_picker/image_picker.dart';
import 'package:news/cubits/AddNewsCubit.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/cubits/deleteImageId.dart';
import 'package:news/cubits/getUserNewsCubit.dart';
import 'package:news/cubits/languageCubit.dart';
import 'package:news/cubits/tagCubit.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/cubits/categoryCubit.dart';
import 'package:news/app/routes.dart';
import 'package:news/data/models/TagModel.dart';
import 'package:news/data/models/CategoryModel.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/data/models/appLanguageModel.dart';
import 'package:news/ui/widgets/SnackBarWidget.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/ui/widgets/networkImage.dart';
import 'package:news/ui/widgets/showUploadImageBottomsheet.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';
import 'package:news/ui/widgets/dashedRect.dart';
import 'package:news/ui/screens/NewsDescription.dart';
import 'package:news/utils/internetConnectivity.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/utils/validators.dart';

class AddNews extends StatefulWidget {
  NewsModel? model;
  bool isEdit;
  String from;
  AddNews({super.key, this.model, required this.isEdit, required this.from});

  @override
  _AddNewsState createState() => _AddNewsState();

  static Route route(RouteSettings routeSettings) {
    final arguments = routeSettings.arguments as Map<String, dynamic>;
    return CupertinoPageRoute(builder: (_) => AddNews(model: arguments['model'], isEdit: arguments['isEdit'], from: arguments['from']));
  }
}

class _AddNewsState extends State<AddNews> {
  final GlobalKey<ScaffoldState> _scaffoldKey = GlobalKey<ScaffoldState>();
  final GlobalKey<FormState> _formkey = GlobalKey<FormState>();
  String catSel = "";
  String subCatSel = "";
  String conType = "";
  String conTypeId = "standard_post";
  String? title;
  String? catSelId, subSelId, showTill, url, desc;
  int? catIndex;
  String langId = "", langName = "";
  List<String> tagsName = [];
  List<String> tagsId = [];
  Map<String, String> contentType = {};
  List<File> otherImage = [];
  File? image;
  bool isNext = false;
  TextEditingController titleC = TextEditingController();
  TextEditingController urlC = TextEditingController();
  File? videoUpload;
  bool isDescLoading = true;
  List<CategoryModel> categories = [];

  clearText() {
    setState(() {
      catSel = "";
      subCatSel = "";
      conType = UiUtils.getTranslatedLabel(context, 'stdPostLbl');
      title = null;
      catSelId = null;
      subSelId = null;
      showTill = null;
      conTypeId = 'standard_post';
      url = null;
      catIndex = null;
      tagsName = [];
      tagsId = [];
      otherImage = [];
      image = null;
      isNext = false;
      titleC.clear();
      urlC.clear();
      videoUpload = null;
      desc = null;
    });
  }

  setContentType() {
    contentType = {
      "standard_post": UiUtils.getTranslatedLabel(context, 'stdPostLbl'),
      "video_youtube": UiUtils.getTranslatedLabel(context, 'videoYoutubeLbl'),
      "video_other": UiUtils.getTranslatedLabel(context, 'videoOtherUrlLbl'),
      "video_upload": UiUtils.getTranslatedLabel(context, 'videoUploadLbl'),
    };
  }

  addDataFromModel() {
    if (widget.model != null) {
      Future.delayed(Duration.zero, () {
        setState(() {
          setContentType();
          titleC.text = widget.model!.title!;
          title = widget.model!.title!;
          catSel = widget.model!.categoryName!;
          catSelId = widget.model!.categoryId!;
          subCatSel = widget.model!.subCatName!;
          subSelId = widget.model!.subCatId!;
          langId = widget.model!.langId!;
          for (final entry in contentType.entries) {
            if (entry.key == widget.model!.contentType!) {
              conType = entry.value;
              conTypeId = entry.key;
            }
          }
          if (conTypeId == "video_youtube" || conTypeId == "video_other" || conTypeId == "video_upload") urlC.text = widget.model!.contentValue!;
          if (widget.model!.tagName! != "") tagsName = widget.model!.tagName!.split(',');
          if (widget.model!.tagId! != "") tagsId = widget.model!.tagId!.split(",");
          if (widget.model!.showTill != "0000-00-00") showTill = widget.model!.showTill!;
          desc = widget.model!.desc!;
        });
      });
    }
  }

  @override
  void initState() {
    getStandardPostLabel();
    getCategory();
    getTag();
    getLanguageData();
    if (widget.isEdit) addDataFromModel();
    super.initState();
  }

  @override
  void dispose() {
    titleC.dispose();
    urlC.dispose();
    super.dispose();
  }

  Widget dropdownArrow() {
    return Align(alignment: Alignment.centerRight, child: Icon(Icons.keyboard_arrow_down_outlined, color: UiUtils.getColorScheme(context).primaryContainer));
  }

  Future<void> getStandardPostLabel() async {
    conType = UiUtils.getTranslatedLabel(context, 'stdPostLbl');
    setState(() {});
  }

  Future getLanguageData() async {
    Future.delayed(Duration.zero, () {
      if (widget.isEdit) {
        context.read<LanguageCubit>().getLanguage(context: context).then((value) {
          for (int i = 0; i < value.length; i++) {
            if (widget.model!.langId! == value[i].id) {
              setState(() => langName = value[i].language!);
            }
          }
        });
      } else {
        context.read<LanguageCubit>().getLanguage(context: context);
      }
    });
  }

  void getCategory({String? languageId}) {
    Future.delayed(Duration.zero, () {
      context.read<CategoryCubit>().getCategory(context: context, langId: languageId ?? (context.read<AppLocalizationCubit>().state.id));
    });
  }

  void getTag() {
    Future.delayed(Duration.zero, () {
      context.read<TagCubit>().getTag(langId: context.read<AppLocalizationCubit>().state.id);
    });
  }

  //set appbar
  getAppBar() {
    if (!isNext) {
      return PreferredSize(
          preferredSize: const Size(double.infinity, 45),
          child: AppBar(
            centerTitle: false,
            backgroundColor: Colors.transparent,
            title: Transform(
                transform: Matrix4.translationValues(-20.0, 0.0, 0.0),
                child: CustomTextLabel(
                    text: (widget.isEdit) ? 'editNewsLbl' : 'createNewsLbl',
                    textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.w600, letterSpacing: 0.5))),
            leading: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 10.0),
              child: InkWell(
                  onTap: () {
                    if (!isNext) {
                      Navigator.of(context).pop();
                    } else {
                      setState(() => isNext = false);
                    }
                  },
                  splashColor: Colors.transparent,
                  highlightColor: Colors.transparent,
                  child: Icon(Icons.arrow_back, color: UiUtils.getColorScheme(context).primaryContainer)),
            ),
            actions: [
              Container(
                  padding: const EdgeInsetsDirectional.only(end: 20),
                  alignment: Alignment.center,
                  child: CustomTextLabel(text: 'step1Of2Lbl', textStyle: Theme.of(context).textTheme.bodySmall!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6))))
            ],
          ));
    }
  }

  Widget languageSelName() {
    return Padding(
      padding: const EdgeInsets.only(top: 18.0),
      child: InkWell(
        onTap: () => langListBottomSheet(),
        child: Container(
          width: double.maxFinite,
          alignment: Alignment.centerLeft,
          padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 15),
          decoration: BoxDecoration(color: UiUtils.getColorScheme(context).background, borderRadius: BorderRadius.circular(10.0)),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(langName == "" ? UiUtils.getTranslatedLabel(context, 'chooseLanLbl') : langName,
                  style: Theme.of(context)
                      .textTheme
                      .titleMedium!
                      .copyWith(color: langName == "" ? UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6) : UiUtils.getColorScheme(context).primaryContainer)),
              dropdownArrow()
            ],
          ),
        ),
      ),
    );
  }

  Widget catSelectionName() {
    if (langId.isNotEmpty) {
      return BlocConsumer<CategoryCubit, CategoryState>(
        listener: (context, state) {
          if (catSel != "" || (widget.isEdit && widget.model!.categoryName != null)) catIndex = context.read<CategoryCubit>().getCategoryIndex(categoryName: catSel);
        },
        builder: (context, state) {
          if (state is CategoryFetchSuccess) {
            if (state.category.isNotEmpty && state.category.length == 1) catSelId = state.category.first.id;
            if (state.category.isNotEmpty && state.category.length == 1) catIndex = 0;
            return Padding(
              padding: const EdgeInsets.only(top: 18.0),
              child: InkWell(
                onTap: () => catListBottomSheet(),
                child: Container(
                  width: double.maxFinite,
                  alignment: Alignment.centerLeft,
                  padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 15),
                  decoration: BoxDecoration(color: UiUtils.getColorScheme(context).background, borderRadius: BorderRadius.circular(10.0)),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                          (state.category.length == 1)
                              ? catSel = state.category.first.categoryName!
                              : (catSel == "")
                                  ? UiUtils.getTranslatedLabel(context, 'catLbl')
                                  : catSel,
                          style: Theme.of(context)
                              .textTheme
                              .titleMedium!
                              .copyWith(color: catSel == "" ? UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6) : UiUtils.getColorScheme(context).primaryContainer)),
                      dropdownArrow()
                    ],
                  ),
                ),
              ),
            );
          }
          return const SizedBox.shrink();
        },
      );
    } else {
      return const SizedBox.shrink();
    }
  }

  Widget subCatSelectionName() {
    if ((subCatSel != "" && widget.isEdit) ||
        (catIndex != null) && (context.read<CategoryCubit>().getCatList().isNotEmpty) && (context.read<CategoryCubit>().getCatList()[catIndex!].subData!.isNotEmpty)) {
      return BlocBuilder<CategoryCubit, CategoryState>(
        builder: (context, state) {
          //incase of only one subcategory for selected category
          if (catIndex != null && context.read<CategoryCubit>().getCatList()[catIndex!].subData!.length == 1) subSelId = context.read<CategoryCubit>().getCatList()[catIndex!].subData!.first.id!;
          return Padding(
            padding: const EdgeInsets.only(top: 18.0),
            child: InkWell(
              onTap: () => subCatListBottomSheet(),
              child: Container(
                width: double.maxFinite,
                alignment: Alignment.centerLeft,
                padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 15),
                decoration: BoxDecoration(color: UiUtils.getColorScheme(context).background, borderRadius: BorderRadius.circular(10.0)),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                        (catIndex != null && context.read<CategoryCubit>().getCatList()[catIndex!].subData!.length == 1)
                            ? subCatSel = context.read<CategoryCubit>().getCatList()[catIndex!].subData!.first.subCatName!
                            : (subCatSel == "")
                                ? UiUtils.getTranslatedLabel(context, 'subcatLbl')
                                : subCatSel,
                        style: Theme.of(context)
                            .textTheme
                            .titleMedium!
                            .copyWith(color: subCatSel == "" ? UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6) : UiUtils.getColorScheme(context).primaryContainer)),
                    dropdownArrow()
                  ],
                ),
              ),
            ),
          );
        },
      );
    } else {
      return const SizedBox.shrink();
    }
  }

  Widget contentTypeSelName() {
    return Padding(
      padding: const EdgeInsets.only(top: 18.0),
      child: InkWell(
        onTap: () => contentTypeBottomSheet(),
        child: Container(
          width: double.maxFinite,
          alignment: Alignment.centerLeft,
          padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 15),
          decoration: BoxDecoration(color: UiUtils.getColorScheme(context).background, borderRadius: BorderRadius.circular(10.0)),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(conType == "" ? UiUtils.getTranslatedLabel(context, 'contentTypeLbl') : conType,
                  style: Theme.of(context)
                      .textTheme
                      .titleMedium!
                      .copyWith(color: conType == "" ? UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6) : UiUtils.getColorScheme(context).primaryContainer)),
              dropdownArrow()
            ],
          ),
        ),
      ),
    );
  }

  Widget contentVideoUpload() {
    return conType == UiUtils.getTranslatedLabel(context, 'videoUploadLbl')
        ? Padding(
            padding: const EdgeInsets.only(top: 18.0),
            child: InkWell(
              onTap: () => _getFromGalleryVideo(),
              child: Container(
                width: double.maxFinite,
                alignment: Alignment.centerLeft,
                padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 15),
                decoration: BoxDecoration(color: UiUtils.getColorScheme(context).background, borderRadius: BorderRadius.circular(10.0)),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Text(videoUpload == null ? UiUtils.getTranslatedLabel(context, 'uploadVideoLbl') : videoUpload!.path.split('/').last,
                          maxLines: 2,
                          softWrap: true,
                          style: Theme.of(context).textTheme.titleMedium!.copyWith(
                              overflow: TextOverflow.ellipsis,
                              color: videoUpload == null ? UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6) : UiUtils.getColorScheme(context).primaryContainer)),
                    ),
                    Padding(
                        padding: const EdgeInsetsDirectional.only(start: 20.0),
                        child: Align(alignment: Alignment.centerRight, child: Icon(Icons.file_upload_outlined, color: UiUtils.getColorScheme(context).primaryContainer)))
                  ],
                ),
              ),
            ),
          )
        : const SizedBox.shrink();
  }

  Widget contentUrlForVideoUpload() {
    if (conTypeId == "video_upload" && videoUpload == null && urlC.text.isNotEmpty) {
      return Container(
          width: double.maxFinite,
          margin: const EdgeInsetsDirectional.only(top: 18),
          padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 15),
          decoration: BoxDecoration(borderRadius: BorderRadius.circular(10.0), color: UiUtils.getColorScheme(context).background),
          child: InkWell(
            onTap: () async {
              //open url in newsVideo screen
              Navigator.of(context).pushNamed(Routes.newsVideo, arguments: {"from": 1, "model": widget.model});
            },
            child: Wrap(
              alignment: WrapAlignment.center,
              spacing: 10,
              runSpacing: 25,
              children: [
                const Icon(Icons.play_circle_fill),
                Text(UiUtils.getTranslatedLabel(context, 'previewLbl'),
                    maxLines: 3,
                    textAlign: TextAlign.center,
                    overflow: TextOverflow.ellipsis,
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer))
              ],
            ),
          ));
    } else {
      return const SizedBox.shrink();
    }
  }

  Widget contentUrlSet() {
    if (conType == UiUtils.getTranslatedLabel(context, 'videoYoutubeLbl') || conType == UiUtils.getTranslatedLabel(context, 'videoOtherUrlLbl')) {
      return Container(
          width: double.maxFinite,
          margin: const EdgeInsetsDirectional.only(top: 18),
          decoration: BoxDecoration(borderRadius: BorderRadius.circular(10.0), color: UiUtils.getColorScheme(context).background),
          child: TextFormField(
            textInputAction: TextInputAction.next,
            maxLines: 1,
            controller: urlC,
            style: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer),
            validator: (val) => Validators.urlValidation(val!, context),
            onChanged: (String value) => setState(() => url = value),
            onTapOutside: (val) => FocusScope.of(context).unfocus(),
            decoration: InputDecoration(
                hintText: conType == UiUtils.getTranslatedLabel(context, 'videoYoutubeLbl') ? UiUtils.getTranslatedLabel(context, 'youtubeUrlLbl') : UiUtils.getTranslatedLabel(context, 'otherUrlLbl'),
                hintStyle: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6)),
                filled: true,
                fillColor: UiUtils.getColorScheme(context).background,
                contentPadding: const EdgeInsets.symmetric(horizontal: 25, vertical: 17),
                focusedBorder: OutlineInputBorder(borderSide: BorderSide.none, borderRadius: BorderRadius.circular(10.0)),
                enabledBorder: OutlineInputBorder(borderSide: BorderSide.none, borderRadius: BorderRadius.circular(10.0))),
          ));
    } else {
      return const SizedBox.shrink();
    }
  }

  contentTypeBottomSheet() {
    showModalBottomSheet<dynamic>(
        context: context,
        elevation: 3.0,
        isScrollControlled: true,
        //it will be closed only when user click On Save button & not by clicking anywhere else in screen
        shape: const RoundedRectangleBorder(borderRadius: BorderRadius.only(topLeft: Radius.circular(30), topRight: Radius.circular(30))),
        enableDrag: false,
        builder: (BuildContext context) => Container(
            padding: const EdgeInsetsDirectional.only(bottom: 15.0, top: 15.0, start: 20.0, end: 20.0),
            decoration: BoxDecoration(borderRadius: const BorderRadius.only(topLeft: Radius.circular(30), topRight: Radius.circular(30)), color: UiUtils.getColorScheme(context).background),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(UiUtils.getTranslatedLabel(context, 'selContentTypeLbl'),
                    style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold, color: UiUtils.getColorScheme(context).primaryContainer)),
                Padding(
                    padding: const EdgeInsetsDirectional.only(top: 10.0, bottom: 15.0),
                    child: Column(
                        children: contentType.entries.map((entry) {
                      return Padding(
                          padding: const EdgeInsets.only(top: 10.0),
                          child: InkWell(
                              onTap: () {
                                if (conType != entry.value || conTypeId != entry.key) {
                                  urlC.clear();
                                  conType = entry.value;
                                  conTypeId = entry.key;
                                }
                                if (widget.isEdit && conTypeId == widget.model!.contentType) {
                                  urlC.text = widget.model!.contentValue!;
                                }
                                setState(() {});
                                Navigator.pop(context);
                              },
                              child: setBottomsheetContainer(listItem: entry.value, compareTo: conType)));
                    }).toList()))
              ],
            )));
  }

  Widget newsTitleName() {
    return Container(
        width: double.maxFinite,
        margin: const EdgeInsetsDirectional.only(top: 7),
        decoration: BoxDecoration(borderRadius: BorderRadius.circular(10.0), color: UiUtils.getColorScheme(context).background),
        child: TextFormField(
            textInputAction: TextInputAction.next,
            maxLines: 1,
            controller: titleC,
            style: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer),
            validator: (val) => Validators.titleValidation(val!, context),
            onChanged: (String value) => setState(() => title = value),
            onTapOutside: (val) => FocusScope.of(context).unfocus(),
            decoration: InputDecoration(
                hintText: UiUtils.getTranslatedLabel(context, 'titleLbl'),
                hintStyle: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6)),
                filled: true,
                fillColor: UiUtils.getColorScheme(context).background,
                contentPadding: const EdgeInsets.symmetric(horizontal: 25, vertical: 17),
                focusedBorder: OutlineInputBorder(borderSide: BorderSide.none, borderRadius: BorderRadius.circular(10.0)),
                enabledBorder: OutlineInputBorder(borderSide: BorderSide.none, borderRadius: BorderRadius.circular(10.0)))));
  }

  Widget tagSelectionName() {
    return Padding(
      padding: const EdgeInsets.only(top: 18.0),
      child: InkWell(
        onTap: () => tagListBottomSheet(),
        child: Container(
          width: double.maxFinite,
          constraints: const BoxConstraints(minHeight: 55),
          alignment: Alignment.centerLeft,
          padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 7),
          decoration: BoxDecoration(borderRadius: BorderRadius.circular(10.0), color: UiUtils.getColorScheme(context).background),
          child: tagsId.isEmpty
              ? CustomTextLabel(text: 'tagLbl', textStyle: Theme.of(context).textTheme.titleMedium!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6)))
              : SizedBox(
                  height: MediaQuery.of(context).size.height * 0.06,
                  child: ListView.builder(
                    physics: const AlwaysScrollableScrollPhysics(),
                    shrinkWrap: true,
                    scrollDirection: Axis.horizontal,
                    itemCount: tagsName.length,
                    itemBuilder: (context, index) {
                      return Padding(
                        padding: EdgeInsetsDirectional.only(start: index != 0 ? 10.0 : 0),
                        child: Stack(
                          children: [
                            Container(
                              margin: const EdgeInsetsDirectional.only(end: 7.5, top: 7.5),
                              padding: const EdgeInsetsDirectional.all(7.0),
                              decoration: BoxDecoration(borderRadius: BorderRadius.circular(5.0), color: UiUtils.getColorScheme(context).primaryContainer),
                              alignment: Alignment.center,
                              child: Text(tagsName[index], style: Theme.of(context).textTheme.titleSmall!.copyWith(color: UiUtils.getColorScheme(context).background)),
                            ),
                            Positioned.directional(
                                textDirection: Directionality.of(context),
                                end: 0,
                                child: Container(
                                    height: 15,
                                    width: 15,
                                    alignment: Alignment.center,
                                    margin: const EdgeInsets.all(3.0),
                                    decoration: BoxDecoration(borderRadius: BorderRadius.circular(25.0), color: Theme.of(context).primaryColor),
                                    child: InkWell(
                                      child: Icon(Icons.close, size: 11, color: UiUtils.getColorScheme(context).background),
                                      onTap: () {
                                        setState(() {
                                          tagsName.remove(tagsName[index]);
                                          tagsId.remove(tagsId[index]);
                                        });
                                      },
                                    )))
                          ],
                        ),
                      );
                    },
                  ),
                ),
        ),
      ),
    );
  }

  Widget showTillSelDate() {
    return Padding(
      padding: const EdgeInsets.only(top: 10.0),
      child: InkWell(
        onTap: () async {
          DateTime? pickedDate =
              await showDatePicker(context: context, initialDate: DateTime.now(), firstDate: DateTime.now().subtract(const Duration(days: 0)), lastDate: DateTime(DateTime.now().year + 1));
          if (pickedDate != null) {
            //pickedDate output format => 2021-03-10 00:00:00.000
            String formattedDate = DateFormat('yyyy-MM-dd').format(pickedDate); //set output date to TextField value.
            setState(() => showTill = formattedDate);
          }
        },
        child: Container(
          width: double.maxFinite,
          margin: const EdgeInsetsDirectional.only(top: 7),
          padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 17),
          decoration: BoxDecoration(borderRadius: BorderRadius.circular(10.0), color: UiUtils.getColorScheme(context).background),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(showTill == null ? UiUtils.getTranslatedLabel(context, 'showTilledDate') : showTill!,
                  style: Theme.of(context)
                      .textTheme
                      .titleMedium!
                      .copyWith(color: showTill == null ? UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6) : UiUtils.getColorScheme(context).primaryContainer)),
              Align(alignment: Alignment.centerRight, child: Icon(Icons.calendar_month_outlined, color: UiUtils.getColorScheme(context).primaryContainer))
            ],
          ),
        ),
      ),
    );
  }

  void _showPicker() {
    showUploadImageBottomsheet(context: context, onCamera: _getFromCamera, onGallery: _getFromGallery);
  }

  //set image camera
  _getFromCamera() async {
    XFile? pickedFile = await ImagePicker().pickImage(source: ImageSource.camera, maxWidth: 1800, maxHeight: 1800);
    if (pickedFile != null) {
      setState(() {
        image = File(pickedFile.path);
        Navigator.of(context).pop(); //pop dialog
      });
    }
  }

// set image gallery
  _getFromGallery() async {
    XFile? pickedFile = await ImagePicker().pickImage(
      source: ImageSource.gallery,
      maxWidth: 1800,
      maxHeight: 1800,
    );
    if (pickedFile != null) {
      setState(() {
        image = File(pickedFile.path);
        Navigator.of(context).pop(); //pop dialog
      });
    }
  }

  // set image gallery
  _getFromGalleryOther() async {
    List<XFile>? pickedFileList = await ImagePicker().pickMultiImage(maxWidth: 1800, maxHeight: 1800);
    for (int i = 0; i < pickedFileList.length; i++) {
      otherImage.add(File(pickedFileList[i].path));
    }
    setState(() {});
  }

  // set video gallery
  _getFromGalleryVideo() async {
    final XFile? file = await ImagePicker().pickVideo(source: ImageSource.gallery, maxDuration: const Duration(seconds: 10));
    if (file != null) {
      setState(() => videoUpload = File(file.path));
    }
  }

  Widget uploadMainImage() {
    return InkWell(
      onTap: () => _showPicker(),
      child: (widget.isEdit || image != null)
          ? Padding(
              padding: const EdgeInsets.only(top: 25),
              child: ClipRRect(
                  borderRadius: BorderRadius.circular(10),
                  child: (image == null)
                      ? CustomNetworkImage(
                          networkImageUrl: widget.model!.image!,
                          width: double.maxFinite,
                          height: 125,
                          fit: BoxFit.cover,
                          isVideo: false,
                        )
                      : Image.file(image!, height: 125, width: double.maxFinite, fit: BoxFit.fill)),
            )
          : Container(
              height: 125,
              width: double.maxFinite,
              decoration: BoxDecoration(borderRadius: BorderRadius.circular(10)),
              padding: const EdgeInsets.only(top: 25),
              child: DashedRect(
                color: UiUtils.getColorScheme(context).primaryContainer,
                child: Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                  Icon(Icons.image, color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7)),
                  Padding(
                    padding: const EdgeInsetsDirectional.only(start: 10),
                    child: CustomTextLabel(
                        text: 'uploadMainImageLbl', textStyle: Theme.of(context).textTheme.titleMedium!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.5))),
                  )
                ]),
              ),
            ),
    );
  }

  Widget uploadOtherImage() {
    return otherImage.isEmpty
        ? InkWell(
            onTap: () => _getFromGalleryOther(),
            child: Container(
              height: 125,
              width: double.maxFinite,
              decoration: BoxDecoration(borderRadius: BorderRadius.circular(10)),
              padding: const EdgeInsets.only(top: 25),
              child: DashedRect(
                color: UiUtils.getColorScheme(context).primaryContainer,
                child: Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                  Icon(Icons.image, color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7)),
                  Padding(
                    padding: const EdgeInsetsDirectional.only(start: 10),
                    child: CustomTextLabel(
                        text: 'uploadOtherImageLbl',
                        textAlign: TextAlign.center,
                        textStyle: Theme.of(context).textTheme.titleMedium!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.5))),
                  )
                ]),
              ),
            ),
          )
        : Padding(
            padding: const EdgeInsets.only(top: 25),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                InkWell(
                  onTap: () => _getFromGalleryOther(),
                  child: Container(
                    height: 125,
                    width: 95,
                    decoration: BoxDecoration(borderRadius: BorderRadius.circular(10)),
                    child: DashedRect(
                      color: UiUtils.getColorScheme(context).primaryContainer,
                      child: Center(
                        child: Column(crossAxisAlignment: CrossAxisAlignment.center, mainAxisSize: MainAxisSize.min, children: [
                          Icon(Icons.image, size: 15, color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7)),
                          CustomTextLabel(
                            text: 'uploadOtherImageLbl',
                            textAlign: TextAlign.center,
                            textStyle: Theme.of(context).textTheme.bodySmall!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.5)),
                          )
                        ]),
                      ),
                    ),
                  ),
                ),
                Expanded(
                  child: SizedBox(
                      height: 125,
                      child: ListView.builder(
                        physics: const AlwaysScrollableScrollPhysics(),
                        shrinkWrap: true,
                        scrollDirection: Axis.horizontal,
                        itemCount: otherImage.length,
                        itemBuilder: (context, index) {
                          return Padding(
                            padding: const EdgeInsetsDirectional.only(start: 10),
                            child: ClipRRect(
                              borderRadius: BorderRadius.circular(10),
                              child: Image.file(otherImage[index], height: 125, width: 95, fit: BoxFit.fill),
                            ),
                          );
                        },
                      )),
                )
              ],
            ),
          );
  }

  Widget modelOtherImage() {
    return widget.model!.imageDataList!.isNotEmpty
        ? Padding(
            padding: const EdgeInsets.only(top: 25),
            child: SizedBox(
                height: 125,
                child: ListView.builder(
                  physics: const AlwaysScrollableScrollPhysics(),
                  shrinkWrap: true,
                  scrollDirection: Axis.horizontal,
                  itemCount: widget.model!.imageDataList!.length,
                  itemBuilder: (context, index) {
                    return Container(
                      margin: const EdgeInsets.only(left: 0.0, right: 0.0),
                      child: Stack(
                        children: [
                          Padding(
                            padding: const EdgeInsetsDirectional.only(top: 10, end: 8),
                            child: ClipRRect(
                                borderRadius: BorderRadius.circular(10.0),
                                child: CustomNetworkImage(networkImageUrl: widget.model!.imageDataList![index].otherImage!, isVideo: false, fit: BoxFit.cover, height: 125, width: 95)),
                          ),
                          BlocConsumer<DeleteImageCubit, DeleteImageState>(
                              bloc: context.read<DeleteImageCubit>(),
                              listener: (context, state) {
                                if (state is DeleteImageSuccess) {
                                  context.read<GetUserNewsCubit>().deleteImageId(index);
                                  showSnackBar(state.message, context);
                                  setState(() {});
                                }
                              },
                              builder: (context, state) {
                                return Positioned.directional(
                                    textDirection: Directionality.of(context),
                                    end: 0,
                                    child: Container(
                                        height: 18,
                                        width: 18,
                                        alignment: Alignment.center,
                                        margin: const EdgeInsets.all(3.0),
                                        decoration: BoxDecoration(borderRadius: BorderRadius.circular(25.0), color: Theme.of(context).primaryColor),
                                        child: InkWell(
                                          child: Icon(Icons.close, size: 13, color: UiUtils.getColorScheme(context).background),
                                          onTap: () {
                                            context.read<DeleteImageCubit>().setDeleteImage(imageId: widget.model!.imageDataList![index].id!);
                                            setState(() {});
                                          },
                                        )));
                              })
                        ],
                      ),
                    );
                  },
                )))
        : const SizedBox.shrink();
  }

  Widget nextBtn() {
    return Padding(
      padding: const EdgeInsetsDirectional.all(20),
      child: InkWell(
        splashColor: Colors.transparent,
        child: Container(
            height: 55.0,
            width: MediaQuery.of(context).size.width * 0.9,
            alignment: Alignment.center,
            decoration: BoxDecoration(color: UiUtils.getColorScheme(context).primaryContainer, borderRadius: BorderRadius.circular(7.0)),
            child: CustomTextLabel(
                text: 'nxt',
                textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(color: UiUtils.getColorScheme(context).background, fontWeight: FontWeight.w600, fontSize: 21, letterSpacing: 0.6))),
        onTap: () async {
          FocusScope.of(context).unfocus(); //dismiss keyboard
          final form = _formkey.currentState;
          form!.save();
          if (form.validate()) {
            if (catSelId == null) {
              showSnackBar(UiUtils.getTranslatedLabel(context, 'plzSelCatLbl'), context);
              return;
            }
            if (langName == "") {
              showSnackBar(UiUtils.getTranslatedLabel(context, 'chooseLanLbl'), context);
              return;
            }
            if (conType == UiUtils.getTranslatedLabel(context, 'videoUploadLbl')) {
              if (!widget.isEdit && videoUpload == null) {
                showSnackBar(UiUtils.getTranslatedLabel(context, 'plzUploadVideoLbl'), context);
                return;
              }
            }
            if ((conType == UiUtils.getTranslatedLabel(context, 'videoYoutubeLbl') || conType == UiUtils.getTranslatedLabel(context, 'videoOtherUrlLbl')) && urlC.text.contains("/shorts")) {
              //do not allow to add link of Youtube shorts as of now
              showSnackBar(UiUtils.getTranslatedLabel(context, 'plzValidUrlLbl'), context);
              urlC.clear();
              return;
            }
            if (!widget.isEdit && image == null) {
              showSnackBar(UiUtils.getTranslatedLabel(context, 'plzAddMainImageLbl'), context);
              return;
            }
            //validate other or Youtube URL & set type accordingly
            if (conType == UiUtils.getTranslatedLabel(context, 'videoOtherUrlLbl') && (urlC.text.contains("youtube") || urlC.text.contains("youtu.be"))) {
              conType = UiUtils.getTranslatedLabel(context, 'videoYoutubeLbl');
              conTypeId = "video_youtube";
            } else if (conType == UiUtils.getTranslatedLabel(context, 'videoYoutubeLbl') && (!urlC.text.contains("youtube") && !urlC.text.contains("youtu.be"))) {
              conType = UiUtils.getTranslatedLabel(context, 'videoOtherUrlLbl');
              conTypeId = "video_other";
            }
            setState(() => isNext = true);
          }
        },
      ),
    );
  }

  validateFunc(String description) {
    desc = description;
    validateForm();
  }

  tagListBottomSheet() {
    showModalBottomSheet<dynamic>(
        context: context,
        elevation: 3.0,
        isScrollControlled: true,
        //it will be closed only when user click On Save button & not by clicking anywhere else in screen
        shape: const RoundedRectangleBorder(borderRadius: BorderRadius.only(topLeft: Radius.circular(30), topRight: Radius.circular(30))),
        enableDrag: false,
        builder: (BuildContext context) => Container(
            padding: const EdgeInsetsDirectional.only(bottom: 15.0, top: 15.0, start: 20.0, end: 20.0),
            decoration: BoxDecoration(borderRadius: const BorderRadius.only(topLeft: Radius.circular(30), topRight: Radius.circular(30)), color: UiUtils.getColorScheme(context).background),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              mainAxisSize: MainAxisSize.min,
              children: [
                CustomTextLabel(text: 'selTagLbl', textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold, color: UiUtils.getColorScheme(context).primaryContainer)),
                Padding(
                    padding: const EdgeInsetsDirectional.only(top: 10.0, bottom: 15.0),
                    child: ListView.builder(
                      physics: const AlwaysScrollableScrollPhysics(),
                      shrinkWrap: true,
                      itemCount: context.read<TagCubit>().tagList().length,
                      itemBuilder: (context, index) => tagListItem(index, context.read<TagCubit>().tagList()),
                    )),
              ],
            )));
  }

  subCatListBottomSheet() {
    showModalBottomSheet<dynamic>(
        context: context,
        elevation: 3.0,
        isScrollControlled: true,
        //it will be closed only when user click On Save button & not by clicking anywhere else in screen
        shape: const RoundedRectangleBorder(borderRadius: BorderRadius.only(topLeft: Radius.circular(30), topRight: Radius.circular(30))),
        enableDrag: false,
        builder: (BuildContext context) => Container(
            padding: const EdgeInsetsDirectional.only(bottom: 15.0, top: 15.0, start: 20.0, end: 20.0),
            decoration: BoxDecoration(borderRadius: const BorderRadius.only(topLeft: Radius.circular(30), topRight: Radius.circular(30)), color: UiUtils.getColorScheme(context).background),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              mainAxisSize: MainAxisSize.min,
              children: [
                CustomTextLabel(
                    text: 'selSubCatLbl', textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold, color: UiUtils.getColorScheme(context).primaryContainer)),
                Padding(
                    padding: const EdgeInsetsDirectional.only(top: 10.0, bottom: 15.0),
                    child: ListView.builder(
                      physics: const AlwaysScrollableScrollPhysics(),
                      shrinkWrap: true,
                      itemCount: context.read<CategoryCubit>().getCatList()[catIndex!].subData!.length,
                      itemBuilder: (context, index) => subCatListItem(index, context.read<CategoryCubit>().getCatList()),
                    )),
              ],
            )));
  }

  langListBottomSheet() {
    showModalBottomSheet<dynamic>(
        context: context,
        elevation: 3.0,
        isScrollControlled: true,
        //it will be closed only when user click On Save button & not by clicking anywhere else in screen
        shape: const RoundedRectangleBorder(borderRadius: BorderRadius.only(topLeft: Radius.circular(30), topRight: Radius.circular(30))),
        enableDrag: false,
        builder: (BuildContext context) => Container(
            padding: const EdgeInsetsDirectional.only(bottom: 15.0, top: 15.0, start: 20.0, end: 20.0),
            decoration: BoxDecoration(borderRadius: const BorderRadius.only(topLeft: Radius.circular(30), topRight: Radius.circular(30)), color: UiUtils.getColorScheme(context).background),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              mainAxisSize: MainAxisSize.min,
              children: [
                CustomTextLabel(
                  text: 'chooseLanLbl',
                  textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(
                        fontWeight: FontWeight.bold,
                        color: UiUtils.getColorScheme(context).primaryContainer,
                      ),
                ),
                Padding(
                    padding: const EdgeInsetsDirectional.only(top: 10.0, bottom: 15.0),
                    child: ListView.builder(
                      physics: const AlwaysScrollableScrollPhysics(),
                      shrinkWrap: true,
                      itemCount: context.read<LanguageCubit>().langList().length,
                      itemBuilder: (context, index) {
                        return langListItem(index, context.read<LanguageCubit>().langList());
                      },
                    )),
              ],
            )));
  }

  catListBottomSheet() {
    showModalBottomSheet<dynamic>(
        context: context,
        elevation: 3.0,
        isScrollControlled: true,
        //it will be closed only when user click On Save button & not by clicking anywhere else in screen
        shape: const RoundedRectangleBorder(borderRadius: BorderRadius.only(topLeft: Radius.circular(30), topRight: Radius.circular(30))),
        enableDrag: false,
        builder: (BuildContext context) => Container(
            constraints: BoxConstraints(
              maxHeight: MediaQuery.of(context).size.height * (0.90),
            ),
            padding: const EdgeInsetsDirectional.only(top: 15.0, start: 20.0, end: 20.0),
            decoration: BoxDecoration(borderRadius: const BorderRadius.only(topLeft: Radius.circular(30), topRight: Radius.circular(30)), color: UiUtils.getColorScheme(context).background),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              mainAxisSize: MainAxisSize.min,
              children: [
                CustomTextLabel(
                  text: 'selCatLbl',
                  textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(
                        fontWeight: FontWeight.bold,
                        color: UiUtils.getColorScheme(context).primaryContainer,
                      ),
                ),
                Expanded(
                  child: ListView.builder(
                    physics: const AlwaysScrollableScrollPhysics(),
                    shrinkWrap: true,
                    padding: const EdgeInsetsDirectional.only(top: 10.0, bottom: 25.0),
                    itemCount: context.read<CategoryCubit>().getCatList().length,
                    itemBuilder: (context, index) {
                      return catListItem(index, context.read<CategoryCubit>().getCatList());
                    },
                  ),
                ),
              ],
            )));
  }

  Widget tagListItem(int index, List<TagModel> tagList) {
    return Padding(
      padding: const EdgeInsets.only(top: 10.0),
      child: InkWell(
        onTap: () {
          if (!tagsId.contains(tagList[index].id!)) {
            setState(() {
              tagsName.add(tagList[index].tagName!);
              tagsId.add(tagList[index].id!);
            });
          } else {
            setState(() {
              tagsName.remove(tagList[index].tagName!);
              tagsId.remove(tagList[index].id!);
            });
          }
          Navigator.pop(context);
        },
        child: Container(
          decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(5.0),
              color: tagsId.isNotEmpty
                  ? tagsId.contains(tagList[index].id!)
                      ? Theme.of(context).primaryColor
                      : UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.2)
                  : UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.2)),
          padding: const EdgeInsets.all(10.0),
          alignment: Alignment.center,
          child: Text(tagList[index].tagName!,
              style: Theme.of(context)
                  .textTheme
                  .titleMedium!
                  .copyWith(color: (tagsId.contains(tagList[index].id!)) ? UiUtils.getColorScheme(context).secondary : UiUtils.getColorScheme(context).primaryContainer)),
        ),
      ),
    );
  }

  Widget subCatListItem(int index, List<CategoryModel> catList) {
    return Padding(
      padding: const EdgeInsets.only(top: 10.0),
      child: InkWell(
          onTap: () {
            setState(() {
              subCatSel = catList[catIndex!].subData![index].subCatName!;
              subSelId = catList[catIndex!].subData![index].id!;
            });
            Navigator.pop(context);
          },
          child: setBottomsheetContainer(listItem: catList[catIndex!].subData![index].subCatName!, compareTo: subCatSel)),
    );
  }

  Widget langListItem(int index, List<LanguageModel> langList) {
    return Padding(
      padding: const EdgeInsets.only(top: 10.0),
      child: InkWell(
        onTap: () {
          langId = langList[index].id!;
          langName = langList[index].language!;
          catSel = "";
          //load categories according to language selected
          getCategory(languageId: langId);
          setState(() {});
          Navigator.pop(context);
        },
        child: setBottomsheetContainer(listItem: langList[index].language!, compareTo: langName),
      ),
    );
  }

  Widget setBottomsheetContainer({required String listItem, required String compareTo}) {
    return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(5.0),
          color: compareTo != ""
              ? compareTo == listItem
                  ? Theme.of(context).primaryColor
                  : UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.2)
              : UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.2)),
      padding: const EdgeInsets.all(10.0),
      alignment: Alignment.center,
      child: Text(listItem,
          style: Theme.of(context).textTheme.titleMedium!.copyWith(color: (compareTo == listItem) ? UiUtils.getColorScheme(context).secondary : UiUtils.getColorScheme(context).primaryContainer)),
    );
  }

  Widget catListItem(int index, List<CategoryModel> catList) {
    return Padding(
      padding: const EdgeInsets.only(top: 10.0),
      child: InkWell(
        onTap: () {
          setState(() {
            subSelId = null;
            subCatSel = "";
            catSel = catList[index].categoryName!;
            catSelId = catList[index].id!;
            catIndex = index;
          });
          Navigator.pop(context);
        },
        child: setBottomsheetContainer(listItem: catList[index].categoryName!, compareTo: catSel),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    setContentType();
    return Scaffold(
      bottomNavigationBar: !isNext ? nextBtn() : null,
      key: _scaffoldKey,
      appBar: getAppBar(),
      body: BlocConsumer<AddNewsCubit, AddNewsState>(
          bloc: context.read<AddNewsCubit>(),
          listener: (context, state) async {
            if (state is AddNewsFetchFailure) {
              showSnackBar(
                state.errorMessage,
                context,
              );
            }
            if (state is AddNewsFetchSuccess) {
              if (!widget.isEdit) {
                showSnackBar(state.addNews["message"], context);
                if (widget.from == "myNews") {
                  //just pop it & no replacement
                  FocusScope.of(context).unfocus(); //dismiss keyboard
                  clearText();
                  Navigator.of(context).pop();
                } else {
                  Navigator.of(context).pushReplacementNamed(Routes.showNews).whenComplete(() {
                    FocusScope.of(context).unfocus(); //dismiss keyboard
                    clearText();
                  });
                }
              } else {
                //call APi of get_news to update news
                Future.delayed(Duration.zero, () {
                  context.read<GetUserNewsCubit>().getGetUserNews(userId: context.read<AuthCubit>().getUserId(), langId: context.read<AppLocalizationCubit>().state.id);
                }).then((value) {
                  showSnackBar(state.addNews["message"], context);
                  Navigator.of(context).pop();
                });
              }
            }
          },
          builder: (context, state) {
            return Form(
                key: _formkey,
                child: Stack(children: [
                  if (state is AddNewsFetchInProgress) Center(child: showCircularProgress(true, Theme.of(context).primaryColor)),
                  !isNext
                      ? WillPopScope(
                          onWillPop: () {
                            if (!isNext) {
                              return Future.value(true);
                            } else {
                              setState(() {
                                isNext = false;
                              });
                              return Future.value(false);
                            }
                          },
                          child: SingleChildScrollView(
                              padding: const EdgeInsetsDirectional.only(top: 20, start: 20, end: 20, bottom: 20),
                              physics: const AlwaysScrollableScrollPhysics(),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  newsTitleName(),
                                  languageSelName(),
                                  catSelectionName(),
                                  subCatSelectionName(),
                                  contentTypeSelName(),
                                  if (widget.isEdit) contentUrlForVideoUpload(),
                                  contentVideoUpload(),
                                  contentUrlSet(),
                                  tagSelectionName(),
                                  showTillSelDate(),
                                  uploadMainImage(),
                                  uploadOtherImage(),
                                  if (widget.isEdit) modelOtherImage()
                                ],
                              )),
                        )
                      : NewsDescription(desc ?? "", updateParent, validateFunc, 1)
                ]));
          }),
    );
  }

  updateParent(String description, bool next) {
    setState(() {
      desc = description;
      isNext = next;
    });
  }

  validateForm() async {
    if (await InternetConnectivity.isNetworkAvailable()) {
      context.read<AddNewsCubit>().addNews(
          context: context,
          userId: context.read<AuthCubit>().getUserId(),
          newsId: (widget.isEdit) ? widget.model!.id! : null,
          actionType: (widget.isEdit) ? "2" : "1",
          catId: catSelId!,
          title: title!,
          conTypeId: conTypeId,
          conType: conType,
          image: image,
          langId: langId,
          subCatId: subSelId,
          showTill: showTill,
          desc: desc,
          otherImage: otherImage,
          tagId: tagsId.isNotEmpty ? tagsId.join(',') : null,
          url: urlC.text.isNotEmpty ? urlC.text : null,
          videoUpload: videoUpload);
      getCategory(); //get default language categories again for Categories Tab
    } else {
      showSnackBar(UiUtils.getTranslatedLabel(context, 'internetmsg'), context);
    }
  }
}
