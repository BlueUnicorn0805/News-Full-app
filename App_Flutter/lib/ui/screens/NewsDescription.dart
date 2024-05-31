// ignore_for_file: must_be_immutable, file_names

import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:html_editor_enhanced/html_editor.dart';
import 'package:file_picker/file_picker.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:shimmer/shimmer.dart';
import '../styles/colors.dart';

class NewsDescription extends StatefulWidget {
  String? description;
  Function changeDesc;
  Function? validateDesc;
  int? from;

  NewsDescription(this.description, this.changeDesc, this.validateDesc, this.from, {super.key});

  @override
  NewsDescriptionState createState() => NewsDescriptionState();
}

class NewsDescriptionState extends State<NewsDescription> {
  String result = '';
  bool isLoading = true;
  bool isSubmitted = false;

  final HtmlEditorController controller = HtmlEditorController();

  @override
  void initState() {
    setValue();

    super.initState();
  }

  setValue() async {
    Future.delayed(
      const Duration(seconds: 4),
      () {
        setState(() {
          isLoading = false;
        });
      },
    );

    Future.delayed(
      const Duration(seconds: 6),
      () {
        setState(() {});
      },
    );
  }

  //set appbar
  getAppBar() {
    return PreferredSize(
        preferredSize: const Size(double.infinity, 45),
        child: AppBar(
          centerTitle: false,
          backgroundColor: Colors.transparent,
          title: Transform(
            transform: Matrix4.translationValues(-20.0, 0.0, 0.0),
            child: CustomTextLabel(
              text: widget.from == 2 ? 'editNewsLbl' : 'createNewsLbl',
              textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.w600, letterSpacing: 0.5),
            ),
          ),
          leading: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 10.0),
            child: InkWell(
              onTap: () {
                controller.getText().then((value) {
                  widget.changeDesc(value, false);
                });
              },
              splashColor: Colors.transparent,
              highlightColor: Colors.transparent,
              child: Icon(Icons.arrow_back, color: UiUtils.getColorScheme(context).primaryContainer),
            ),
          ),
          actions: [
            Container(
              padding: const EdgeInsetsDirectional.only(end: 20),
              alignment: Alignment.center,
              child: CustomTextLabel(text: 'step2of2Lbl', textStyle: Theme.of(context).textTheme.bodySmall!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6))),
            )
          ],
        ));
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
          child: (isSubmitted)
              ? showCircularProgress(true, Theme.of(context).primaryColor)
              : CustomTextLabel(
                  text: 'submitBtn',
                  textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(color: UiUtils.getColorScheme(context).background, fontWeight: FontWeight.w600, fontSize: 21, letterSpacing: 0.6),
                ),
        ),
        onTap: () async {
          controller.getText().then((value) {
            isSubmitted = true;
            widget.validateDesc!(value);
          });
        },
      ),
    );
  }

  Widget shimmer() {
    return SizedBox(
      width: double.infinity,
      child: Shimmer.fromColors(
          baseColor: Colors.grey[300]!,
          highlightColor: Colors.grey[100]!,
          child: Container(
            height: MediaQuery.of(context).size.height * 0.741,
            decoration: BoxDecoration(borderRadius: BorderRadius.circular(10), color: Theme.of(context).cardColor),
          )),
    );
  }

  @override
  Widget build(BuildContext context) {
    //check the same in edit news
    if (widget.description != null && widget.description != "") controller.setText(widget.description!); //incase of Edit
    return Scaffold(
      appBar: getAppBar(),
      bottomNavigationBar: nextBtn(),
      body: WillPopScope(
        onWillPop: () {
          controller.getText().then((value) {
            widget.changeDesc(value, false);
          });

          return Future.value(false);
        },
        child: GestureDetector(
            onTap: () {
              if (!kIsWeb) {
                FocusScope.of(context).unfocus(); //dismiss keyboard
              }
            },
            child: Padding(
              padding: const EdgeInsetsDirectional.all(20),
              child: isLoading
                  ? shimmer()
                  : Theme(
                      data: Theme.of(context).copyWith(textTheme: TextTheme(titleSmall: Theme.of(context).textTheme.titleMedium!.copyWith(color: Colors.orange))),
                      child: HtmlEditor(
                        controller: controller,
                        htmlEditorOptions: HtmlEditorOptions(
                          hint: UiUtils.getTranslatedLabel(context, 'descLbl'),
                          adjustHeightForKeyboard: true,
                          autoAdjustHeight: true,
                          shouldEnsureVisible: true,
                          spellCheck: true,
                        ),
                        htmlToolbarOptions: HtmlToolbarOptions(
                          toolbarPosition: ToolbarPosition.aboveEditor,
                          toolbarType: ToolbarType.nativeExpandable,
                          gridViewHorizontalSpacing: 0,
                          gridViewVerticalSpacing: 0,
                          toolbarItemHeight: 30,
                          buttonColor: UiUtils.getColorScheme(context).primaryContainer,
                          buttonFocusColor: Theme.of(context).primaryColor,
                          buttonBorderColor: Colors.red,
                          buttonFillColor: secondaryColor,
                          dropdownIconColor: Theme.of(context).primaryColor,
                          dropdownIconSize: 26,
                          textStyle: Theme.of(context).textTheme.titleMedium!.copyWith(color: UiUtils.getColorScheme(context).primaryContainer),
                          onButtonPressed: (ButtonType type, bool? status, Function? updateStatus) {
                            return true;
                          },
                          onDropdownChanged: (DropdownType type, dynamic changed, Function(dynamic)? updateSelectedItem) {
                            return true;
                          },
                          mediaLinkInsertInterceptor: (String url, InsertFileType type) {
                            return true;
                          },
                          mediaUploadInterceptor: (PlatformFile file, InsertFileType type) async {
                            return true;
                          },
                        ),
                        otherOptions: OtherOptions(
                          height: MediaQuery.of(context).size.height * 0.725,
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(10),
                            color: UiUtils.getColorScheme(context).background,
                          ),
                        ),
                        callbacks: Callbacks(
                          onChangeCodeview: (String? changed) {
                            result = changed!;
                          },
                          onImageUploadError: (
                            FileUpload? file,
                            String? base64Str,
                            UploadError error,
                          ) {},
                          onNavigationRequestMobile: (String url) {
                            return NavigationActionPolicy.ALLOW;
                          },
                        ),
                      ),
                    ),
            )),
      ),
    );
  }
}
