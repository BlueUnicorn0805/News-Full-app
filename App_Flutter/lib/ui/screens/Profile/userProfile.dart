// ignore_for_file: use_build_context_synchronously, file_names

import 'dart:io';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:image_picker/image_picker.dart';
import 'package:news/app/routes.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/cubits/Auth/updateUserCubit.dart';
import 'package:news/data/repositories/Auth/authLocalDataSource.dart';
import 'package:news/ui/styles/colors.dart';
import 'package:news/ui/widgets/SnackBarWidget.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';
import 'package:news/ui/widgets/customAppBar.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/ui/widgets/showUploadImageBottomsheet.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/utils/validators.dart';

class UserProfileScreen extends StatefulWidget {
  final String from;
  const UserProfileScreen({Key? key, required this.from}) : super(key: key);

  @override
  State createState() => UserProfileScreenState();

  static Route route(RouteSettings routeSettings) {
    Map arguments = routeSettings.arguments as Map;
    return CupertinoPageRoute(
      builder: (_) => UserProfileScreen(from: arguments['from'] as String),
    );
  }
}

class UserProfileScreenState extends State<UserProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  AuthLocalDataSource authLocalDataSource = AuthLocalDataSource();
  dynamic size;
  FocusNode nameFocus = FocusNode();
  FocusNode mobNoFocus = FocusNode();
  FocusNode emailFocus = FocusNode();
  FocusNode crntFocus = FocusNode();

  TextEditingController? nameC, monoC, emailC = TextEditingController();
  String? profile, mobile;
  bool isEditMono = false, isEditEmail = false, isSaving = false;
  String? updateValue;
  File? image;

  @override
  void initState() {
    setControllers();
    super.initState();
  }

  setControllers() {
    nameC = TextEditingController(text: authLocalDataSource.getName());
    emailC = TextEditingController(text: authLocalDataSource.getEmail());
    monoC = TextEditingController(text: authLocalDataSource.getMobile());
    profile = context.read<AuthCubit>().getProfile();
  }

  @override
  Widget build(BuildContext context) {
    size = MediaQuery.of(context).size;
    return Scaffold(appBar: setCustomAppBar(height: 45, isBackBtn: (widget.from == "login") ? false : true, label: 'myProfile', context: context, isConvertText: true), body: buildProfileFields());
  }

  buildProfileFields() {
    return BlocConsumer<UpdateUserCubit, UpdateUserState>(
      listener: (context, state) {
        //show snackbar incase of success & failure both
        if (state is UpdateUserFetchSuccess && state.updatedUser != null) {
          showSnackBar(UiUtils.getTranslatedLabel(context, 'profileUpdateMsg'), context);
          isSaving = false;
          setState(() {});
          (widget.from == "login") ? Navigator.of(context).pushNamedAndRemoveUntil(Routes.managePref, (route) => false, arguments: {"from": 2}) : Navigator.pop(context);
        }
        if (state is UpdateUserFetchFailure) {
          isSaving = false;
          showSnackBar(state.errorMessage, context);
        }
      },
      builder: (context, state) {
        return SingleChildScrollView(
            child: Form(
                key: _formKey,
                child: Column(children: [
                  profileWidget(),
                  SizedBox(height: size.height * 0.03),
                  setTextField(
                      validatorMethod: (value) => Validators.nameValidation(value!, context),
                      focusNode: nameFocus,
                      nextFocus: (context.read<AuthCubit>().getType() != loginMbl) ? mobNoFocus : emailFocus,
                      textInputAction: TextInputAction.next,
                      keyboardType: TextInputType.name,
                      controller: nameC,
                      hintlbl: UiUtils.getTranslatedLabel(context, 'nameLbl')),
                  SizedBox(height: size.height * 0.03),
                  setMobileNumber(),
                  SizedBox(height: size.height * 0.03),
                  setTextField(
                      validatorMethod: (value) => value!.trim().isEmpty ? null : Validators.emailValidation(value, context),
                      focusNode: emailFocus,
                      textInputAction: TextInputAction.done,
                      keyboardType: TextInputType.emailAddress,
                      controller: emailC,
                      isenable: (context.read<AuthCubit>().getType() == loginMbl) ? true : false,
                      hintlbl: UiUtils.getTranslatedLabel(context, 'emailLbl')),
                  SizedBox(height: size.height * 0.05),
                  submitBtn(context)
                ])));
      },
    );
  }

  Widget setMobileNumber() {
    return SizedBox(
        height: 60,
        child: setTextField(
            validatorMethod: (value) => value!.trim().isEmpty ? null : Validators.mobValidation(value, context),
            keyboardType: TextInputType.phone,
            hintlbl: UiUtils.getTranslatedLabel(context, 'mobileLbl'),
            textInputAction: TextInputAction.next,
            controller: monoC,
            focusNode: mobNoFocus,
            nextFocus: emailFocus,
            isenable: (context.read<AuthCubit>().getType() != loginMbl) ? true : false));
  }

  //set image camera
  getFromCamera() async {
    try {
      XFile? pickedFile = await ImagePicker().pickImage(source: ImageSource.camera);
      if (pickedFile != null) {
        Navigator.of(context).pop(); //pop dialog
        setState(() {
          image = File(pickedFile.path);
          profile = image!.path;
        });
        context.read<UpdateUserCubit>().setUpdateUser(userId: context.read<AuthCubit>().getUserId(), context: context, filePath: image!.path);
      }
    } catch (e) {
      debugPrint("camera-err-${e.toString()}");
    }
  }

// set image gallery
  _getFromGallery() async {
    XFile? pickedFile = await ImagePicker().pickImage(source: ImageSource.gallery, maxWidth: 1800, maxHeight: 1800);
    if (pickedFile != null) {
      setState(() {
        image = File(pickedFile.path);
        profile = image!.path;
        Navigator.of(context).pop(); //pop dialog
      });
      context.read<UpdateUserCubit>().setUpdateUser(userId: context.read<AuthCubit>().getUserId(), context: context, filePath: image!.path);
    }
  }

  profileWidget() {
    return Container(padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 15), child: Center(child: profileImgWidget()));
  }

  profileImgWidget() {
    return GestureDetector(
      onTap: () => showUploadImageBottomsheet(context: context, onCamera: getFromCamera, onGallery: _getFromGallery),
      child: Stack(
        alignment: Alignment.center,
        children: [
          CircleAvatar(
              radius: 46,
              backgroundColor: UiUtils.getColorScheme(context).secondaryContainer,
              child: CircleAvatar(
                  radius: 44,
                  backgroundColor: Colors.transparent,
                  child: ClipOval(
                      clipBehavior: Clip.antiAliasWithSaveLayer,
                      child: image == null && profile!.toString().trim().isEmpty
                          ? Icon(Icons.person, color: UiUtils.getColorScheme(context).primaryContainer)
                          : image != null
                              ? Image.file(image!, fit: BoxFit.fill, width: 75, height: 75)
                              : Image.network(profile!, fit: BoxFit.fill, width: 85, height: 85, errorBuilder: (BuildContext context, Object exception, StackTrace? stackTrace) {
                                  return Icon(Icons.person, color: UiUtils.getColorScheme(context).primaryContainer);
                                }, loadingBuilder: (BuildContext context, Widget? child, ImageChunkEvent? loadingProgress) {
                                  if (loadingProgress == null) return child!;
                                  return Icon(Icons.person, color: UiUtils.getColorScheme(context).primaryContainer);
                                })))),
          Container(
              padding: const EdgeInsets.all(5),
              decoration: BoxDecoration(color: Theme.of(context).colorScheme.secondary, shape: BoxShape.circle, border: Border.all(color: UiUtils.getColorScheme(context).primaryContainer)),
              child: Icon(Icons.camera_alt_rounded, color: UiUtils.getColorScheme(context).primaryContainer, size: 20))
        ],
      ),
    );
  }

  submitBtn(BuildContext context) {
    return Container(
        alignment: Alignment.center,
        padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
        child: ElevatedButton(
          style: ElevatedButton.styleFrom(backgroundColor: Theme.of(context).primaryColor, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(7.0))),
          child: Container(
            height: 55.0,
            width: MediaQuery.of(context).size.width * 0.8,
            alignment: Alignment.center,
            child: (isSaving)
                ? showCircularProgress(true, UiUtils.getColorScheme(context).primaryContainer)
                : CustomTextLabel(text: 'saveLbl', textStyle: Theme.of(this.context).textTheme.titleLarge?.copyWith(color: secondaryColor, fontWeight: FontWeight.w500, letterSpacing: 0.6)),
          ),
          onPressed: () async {
            validateData();
          },
        ));
  }

  validateData() async {
    if (_formKey.currentState!.validate()) {
      profileupdateprocess();
    } else {
      debugPrint("validation failed");
    }
  }

  profileupdateprocess() async {
    isSaving = true;
    //in case of Clearing Existing mobile number -> set mobile to blank, so it can be passed to replace existing value of mobile number as NULL mobile number won't be passed to APi
    mobile = monoC!.text.trim();
    if (mobile == null && context.read<AuthCubit>().getType() != loginMbl && context.read<AuthCubit>().getMobile().isNotEmpty) {
      mobile = " ";
    }
    try {
      context.read<UpdateUserCubit>().setUpdateUser(userId: context.read<AuthCubit>().getUserId(), context: context, name: nameC!.text, email: emailC!.text, mobile: mobile);
    } catch (e) {
      showSnackBar(e.toString(), context);
    }
  }

  setTextField(
      {String? Function(String?)? validatorMethod,
      FocusNode? focusNode,
      FocusNode? nextFocus,
      TextInputAction? textInputAction,
      TextInputType? keyboardType,
      TextEditingController? controller,
      String? hintlbl,
      bool isenable = true,
      Widget? prefixWidget}) {
    return Padding(
        padding: const EdgeInsets.symmetric(horizontal: 20),
        child: TextFormField(
            enabled: isenable,
            decoration:
                InputDecoration(prefix: prefixWidget, hintText: hintlbl, hintStyle: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).outline.withOpacity(0.2))),
            validator: validatorMethod,
            inputFormatters: [(focusNode == mobNoFocus) ? FilteringTextInputFormatter.digitsOnly : FilteringTextInputFormatter.singleLineFormatter],
            focusNode: focusNode,
            textInputAction: textInputAction,
            onEditingComplete: () {
              crntFocus = FocusNode();
              (nextFocus != null) ? fieldFocusChange(context, focusNode!, nextFocus) : focusNode?.unfocus();
            },
            onChanged: (String value) {
              setState(() => crntFocus = focusNode!);
            },
            textAlignVertical: TextAlignVertical.center,
            style: Theme.of(context).textTheme.titleMedium!.copyWith(color: (isenable) ? UiUtils.getColorScheme(context).primaryContainer : borderColor),
            keyboardType: keyboardType,
            controller: controller));
  }

  fieldFocusChange(BuildContext context, FocusNode currentFocus, FocusNode nextFocus) {
    currentFocus.unfocus();
    FocusScope.of(context).requestFocus(nextFocus);
  }
}
