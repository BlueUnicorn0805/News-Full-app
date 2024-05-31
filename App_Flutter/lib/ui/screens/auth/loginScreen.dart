// ignore_for_file: file_names, use_build_context_synchronously

import 'dart:io';

import 'package:firebase_auth/firebase_auth.dart' as fAuth;
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/cubits/Auth/updateFCMCubit.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/cubits/privacyTermsCubit.dart';
import 'package:news/cubits/settingCubit.dart';
import 'package:news/ui/screens/auth/Widgets/bottomComBtn.dart';
import 'package:news/ui/screens/auth/Widgets/fieldFocusChange.dart';
import 'package:news/ui/screens/auth/Widgets/setConfimPass.dart';
import 'package:news/ui/screens/auth/Widgets/setDivider.dart';
import 'package:news/ui/screens/auth/Widgets/setEmail.dart';
import 'package:news/ui/screens/auth/Widgets/setForgotPass.dart';
import 'package:news/ui/screens/auth/Widgets/setLoginAndSignUpBtn.dart';
import 'package:news/ui/screens/auth/Widgets/setName.dart';
import 'package:news/ui/screens/auth/Widgets/setPassword.dart';
import 'package:news/ui/screens/auth/Widgets/setTermPolicy.dart';
import 'package:news/ui/styles/colors.dart';
import 'package:news/ui/widgets/SnackBarWidget.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';
import 'package:news/ui/widgets/customTextBtn.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/utils/constant.dart';
import 'package:news/utils/internetConnectivity.dart';
import 'package:news/utils/uiUtils.dart';

import 'package:news/app/routes.dart';
import 'package:news/cubits/Auth/socialSignUpCubit.dart';
import 'package:news/utils/validators.dart';

class LoginScreen extends StatefulWidget {
  final bool? isFromApp;
  const LoginScreen({super.key, this.isFromApp});

  @override
  LoginScreenState createState() => LoginScreenState();

  static Route route(RouteSettings routeSettings) {
    if (routeSettings.arguments == null) {
      return CupertinoPageRoute(builder: (_) => const LoginScreen());
    } else {
      final arguments = routeSettings.arguments as Map<String, dynamic>;
      return CupertinoPageRoute(
          builder: (_) => LoginScreen(
                isFromApp: arguments['isFromApp'] ?? false,
              ));
    }
  }
}

class LoginScreenState extends State<LoginScreen> with SingleTickerProviderStateMixin {
  final GlobalKey<FormState> _formkey = GlobalKey<FormState>();
  TabController? _tabController;
  FocusNode emailFocus = FocusNode();
  FocusNode passFocus = FocusNode();
  FocusNode nameFocus = FocusNode();
  FocusNode emailSFocus = FocusNode();
  FocusNode passSFocus = FocusNode();
  FocusNode confPassFocus = FocusNode();
  TextEditingController? emailC, passC, sEmailC, sPassC, sNameC, sConfPassC;
  String? name, email, pass, mobile, profile, confPass;
  bool isChecked = false;
  bool isPolicyAvailable = false;
  bool isObscure = true; //setPassword widget

  @override
  void initState() {
    //load privacy pages
    Future.delayed(Duration.zero, () {
      context.read<PrivacyTermsCubit>().getPrivacyTerms(context: context, langId: context.read<AppLocalizationCubit>().state.id);
    });

    _tabController = TabController(length: 2, vsync: this, initialIndex: 0);
    assignAllTextController();
    _tabController!.addListener(() {
      FocusScope.of(context).unfocus(); //dismiss keyboard
      clearLoginTextFields();
      clearSignUpTextFields();
    });
    super.initState();
  }

  updateCheck(bool isCheck) {
    setState(() {
      isChecked = isCheck;
    });
  }

  assignAllTextController() {
    emailC = TextEditingController();
    passC = TextEditingController();
    sEmailC = TextEditingController();
    sPassC = TextEditingController();
    sNameC = TextEditingController();
    sConfPassC = TextEditingController();
  }

  clearSignUpTextFields() {
    setState(() {
      sNameC!.clear();
      sEmailC!.clear();
      sPassC!.clear();
      sConfPassC!.clear();
    });
  }

  clearLoginTextFields() {
    setState(() {
      emailC!.clear();
      passC!.clear();
    });
  }

  disposeAllTextController() {
    emailC!.dispose();
    passC!.dispose();
    sEmailC!.dispose();
    sPassC!.dispose();
    sNameC!.dispose();
    sConfPassC!.dispose();
  }

  @override
  void dispose() {
    _tabController?.dispose();
    disposeAllTextController();

    super.dispose();
  }

  //show form content
  showContent() {
    return BlocConsumer<SocialSignUpCubit, SocialSignUpState>(
        bloc: context.read<SocialSignUpCubit>(),
        listener: (context, state) async {
          if (state is SocialSignUpFailure) {
            showSnackBar(
              state.errorMessage,
              context,
            );
          }
          if (state is SocialSignUpSuccess) {
            context.read<AuthCubit>().checkAuthStatus();
            if (state.authModel.status == "0") {
              showSnackBar(UiUtils.getTranslatedLabel(context, 'deactiveMsg'), context);
            } else {
              FirebaseMessaging.instance.getToken().then((token) async {
                if (token != context.read<SettingsCubit>().getSettings().token && token != null) {
                  context.read<UpdateFcmIdCubit>().updateFcmId(userId: context.read<AuthCubit>().getUserId(), fcmId: token, context: context);
                }
              });
              if (state.authModel.isFirstLogin == "1") {
                // Add set / update user details screen here
                Navigator.of(context).pushNamedAndRemoveUntil(Routes.editUserProfile, (route) => false, arguments: {"from": "login"});
              } else if (widget.isFromApp == true) {
                Navigator.pop(context);
              } else {
                Navigator.pushNamedAndRemoveUntil(context, Routes.home, (route) => false);
              }
            }
          }
        },
        builder: (context, state) {
          return Form(
              key: _formkey,
              child: Stack(
                children: [
                  Container(
                    padding: const EdgeInsetsDirectional.only(top: 35.0, bottom: 20.0, start: 20.0, end: 20.0),
                    width: MediaQuery.of(context).size.width,
                    child: Column(crossAxisAlignment: CrossAxisAlignment.center, children: <Widget>[
                      skipBtn(),
                      showTabs(),
                      showTabBarView(),
                    ]),
                  ),
                  if (state is SocialSignUpProgress) showCircularProgress(true, Theme.of(context).primaryColor),
                ],
              ));
        });
  }

  //set skip login btn
  skipBtn() {
    return Align(
        alignment: Alignment.topRight,
        child: CustomTextButton(
          onTap: () {
            Navigator.of(context).pushReplacementNamed(Routes.home, arguments: false);
          },
          text: UiUtils.getTranslatedLabel(context, 'skip'),
          color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7),
        ));
  }

  showTabs() {
    return Align(
        alignment: Alignment.centerLeft,
        child: DefaultTabController(
          length: 2,
          child: Container(
              padding: const EdgeInsetsDirectional.only(start: 10.0),
              width: MediaQuery.of(context).size.width / 1.7,
              child: TabBar(
                  overlayColor: MaterialStateProperty.all(Colors.transparent),
                  controller: _tabController,
                  labelStyle: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.w600, letterSpacing: 0.5),
                  labelPadding: EdgeInsets.zero,
                  labelColor: backgroundColor,
                  unselectedLabelColor: UiUtils.getColorScheme(context).primaryContainer,
                  indicator: BoxDecoration(borderRadius: BorderRadius.circular(50), color: UiUtils.getColorScheme(context).secondaryContainer),
                  tabs: [Tab(text: UiUtils.getTranslatedLabel(context, 'signInTab')), Tab(text: UiUtils.getTranslatedLabel(context, 'signupBtn'))])),
        ));
  }

  //check validation of form data
  bool validateAndSave() {
    final form = _formkey.currentState;
    form!.save();
    if (isChecked) {
      //checkbox value should be 1 before Login/SignUp
      if (form.validate()) {
        return true;
      } else {
        return false;
      }
    } else if (!isPolicyAvailable) {
      showSnackBar(UiUtils.getTranslatedLabel(context, 'addTCFirst'), context);
    } else {
      showSnackBar(UiUtils.getTranslatedLabel(context, 'agreeTCFirst'), context);
    }
    return false;
  }

  Widget setPassword({required FocusNode currFocus, FocusNode? nextFocus, required TextEditingController passC, required String pass, required double topPad, required bool isLogin}) {
    return Padding(
      padding: EdgeInsets.only(top: topPad),
      child: TextFormField(
        focusNode: currFocus,
        textInputAction: isLogin ? TextInputAction.done : TextInputAction.next,
        controller: passC,
        obscureText: isObscure,
        style: Theme.of(context).textTheme.titleMedium?.copyWith(
              color: UiUtils.getColorScheme(context).primaryContainer,
            ),
        validator: (val) => Validators.passValidation(val!, context),
        onFieldSubmitted: (v) {
          if (!isLogin) {
            fieldFocusChange(context, currFocus, nextFocus!);
          }
        },
        onChanged: (String value) {
          pass = value;
          setState(() {});
        },
        decoration: InputDecoration(
          hintText: UiUtils.getTranslatedLabel(context, 'passLbl'),
          hintStyle: Theme.of(context).textTheme.titleMedium?.copyWith(
                color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.5),
              ),
          suffixIcon: Padding(
              padding: const EdgeInsetsDirectional.only(end: 12.0),
              child: IconButton(
                icon: isObscure ? const Icon(Icons.visibility_rounded, size: 20) : const Icon(Icons.visibility_off_rounded, size: 20),
                splashColor: Colors.transparent,
                onPressed: () {
                  setState(() {
                    isObscure = !isObscure;
                  });
                },
              )),
          filled: true,
          fillColor: Theme.of(context).colorScheme.background,
          contentPadding: const EdgeInsets.symmetric(horizontal: 25, vertical: 17),
          focusedBorder: OutlineInputBorder(
            borderSide: BorderSide(color: UiUtils.getColorScheme(context).outline.withOpacity(0.7)),
            borderRadius: BorderRadius.circular(10.0),
          ),
          enabledBorder: OutlineInputBorder(
            borderSide: BorderSide.none,
            borderRadius: BorderRadius.circular(10.0),
          ),
        ),
      ),
    );
  }

  showTabBarView() {
    return Expanded(
      child: Container(
          alignment: Alignment.center,
          height: MediaQuery.of(context).size.height * 1.0,
          child: TabBarView(
            controller: _tabController,
            dragStartBehavior: DragStartBehavior.start,
            children: [
              //Login
              SingleChildScrollView(
                  child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 5.0),
                child: Column(children: [
                  loginTxt(),
                  SetEmail(currFocus: emailFocus, nextFocus: passFocus, emailC: emailC!, email: email ?? '', topPad: 20),
                  SetPassword(currFocus: passFocus, passC: passC!, pass: pass ?? '', topPad: 20, isLogin: true),
                  setForgotPass(context),
                  SetLoginAndSignUpBtn(
                      onTap: () async {
                        FocusScope.of(context).unfocus(); //dismiss keyboard
                        if (validateAndSave()) {
                          if (await InternetConnectivity.isNetworkAvailable()) {
                            context.read<SocialSignUpCubit>().socialSignUpUser(email: emailC!.text.trim(), password: passC!.text, authProvider: AuthProvider.email, context: context);
                          } else {
                            showSnackBar(UiUtils.getTranslatedLabel(context, 'internetmsg'), context);
                          }
                        }
                      },
                      text: 'loginTxt',
                      topPad: 20),
                  setDividerOr(context),
                  bottomBtn(),
                  BlocConsumer<PrivacyTermsCubit, PrivacyTermsState>(listener: (context, state) {
                    if (state is PrivacyTermsFetchSuccess) {
                      isPolicyAvailable = true;
                    }
                    if (state is PrivacyTermsFetchFailure) {
                      isPolicyAvailable = false;
                    }
                  }, builder: (context, state) {
                    return (state is PrivacyTermsFetchSuccess) ? setTermPolicyTxt(context, isChecked, updateCheck, state) : const SizedBox.shrink();
                  })
                ]),
              )),
              //SignUp
              SingleChildScrollView(
                  child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 5.0),
                child: Column(
                  children: [
                    signUpTxt(),
                    SetName(currFocus: nameFocus, nextFocus: emailSFocus, nameC: sNameC!, name: sNameC!.text),
                    SetEmail(currFocus: emailSFocus, nextFocus: passSFocus, emailC: sEmailC!, email: sEmailC!.text, topPad: 20),
                    setPassword(currFocus: passSFocus, nextFocus: confPassFocus, passC: sPassC!, pass: sPassC!.text, topPad: 20, isLogin: false),
                    SetConfirmPass(currFocus: confPassFocus, confPassC: sConfPassC!, confPass: sConfPassC!.text, pass: sPassC!.text),
                    SetLoginAndSignUpBtn(
                        onTap: () async {
                          FocusScope.of(context).unfocus(); //dismiss keyboard
                          final form = _formkey.currentState;
                          if (form!.validate()) {
                            form.save();
                            if (await InternetConnectivity.isNetworkAvailable()) {
                              registerWithEmailPassword(sEmailC!.text.trim(), sPassC!.text.trim());
                            } else {
                              showSnackBar(UiUtils.getTranslatedLabel(context, 'internetmsg'), context);
                            }
                          }
                        },
                        text: 'signupBtn',
                        topPad: 25)
                  ],
                ),
              ))
            ],
          )),
    );
  }

  registerWithEmailPassword(String email, String password) async {
    try {
      final credential = await fAuth.FirebaseAuth.instance.createUserWithEmailAndPassword(
        email: email,
        password: password,
      );
      fAuth.User? user = credential.user;
      user!.updateDisplayName(sNameC!.text.trim()).then((value) => debugPrint("updated name is - ${user.displayName}"));
      user.reload();

      user.sendEmailVerification().then((value) => showSnackBar('${UiUtils.getTranslatedLabel(context, 'verifSentMail')} $email', context));
      clearSignUpTextFields();
      _tabController!.animateTo(0);
      FocusScope.of(context).requestFocus(emailFocus);
    } on fAuth.FirebaseAuthException catch (e) {
      if (e.code == 'weakPassword') {
        showSnackBar(UiUtils.getTranslatedLabel(context, 'weakPassword'), context);
      }
      if (e.code == 'email-already-in-use') {
        showSnackBar(UiUtils.getTranslatedLabel(context, 'emailAlreadyInUse'), context);
      }
    } catch (e) {
      debugPrint(e.toString());
    }
  }

  loginTxt() {
    return Align(
        alignment: Alignment.centerLeft,
        child: Padding(
          padding: const EdgeInsetsDirectional.only(top: 35.0, start: 10.0),
          child: CustomTextLabel(
            text: 'loginDescr',
            textStyle: Theme.of(context).textTheme.headlineSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.w800, letterSpacing: 0.5),
            textAlign: TextAlign.left,
          ),
        ));
  }

  signUpTxt() {
    return Align(
        alignment: Alignment.centerLeft,
        child: Padding(
          padding: const EdgeInsetsDirectional.only(top: 35.0, start: 10.0),
          child: CustomTextLabel(
            text: 'signupDescr',
            textStyle: Theme.of(context).textTheme.headlineSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.w800, letterSpacing: 0.5),
            textAlign: TextAlign.left,
          ),
        ));
  }

  bottomBtn() {
    return Padding(
      padding: const EdgeInsets.only(top: 20.0, left: 30, right: 30),
      child: Row(
        mainAxisAlignment: (fblogInEnabled) ? MainAxisAlignment.spaceBetween : MainAxisAlignment.spaceAround,
        children: [
          BottomCommButton(
              onTap: () {
                if (!isPolicyAvailable) {
                  showSnackBar(UiUtils.getTranslatedLabel(context, 'addTCFirst'), context);
                } else if (isChecked) {
                  context.read<SocialSignUpCubit>().socialSignUpUser(authProvider: AuthProvider.gmail, context: context);
                } else {
                  showSnackBar(UiUtils.getTranslatedLabel(context, 'agreeTCFirst'), context);
                }
              },
              img: 'google_button',
              startPad: 0),
          if (fblogInEnabled)
            BottomCommButton(
                onTap: () {
                  if (!isPolicyAvailable) {
                    showSnackBar(UiUtils.getTranslatedLabel(context, 'addTCFirst'), context);
                  } else if (isChecked) {
                    context.read<SocialSignUpCubit>().socialSignUpUser(authProvider: AuthProvider.fb, context: context);
                  } else {
                    showSnackBar(UiUtils.getTranslatedLabel(context, 'agreeTCFirst'), context);
                  }
                },
                img: 'facebook_button',
                startPad: 10),
          if (Platform.isIOS)
            BottomCommButton(
                onTap: () {
                  if (!isPolicyAvailable) {
                    showSnackBar(UiUtils.getTranslatedLabel(context, 'addTCFirst'), context);
                  } else if (isChecked) {
                    context.read<SocialSignUpCubit>().socialSignUpUser(authProvider: AuthProvider.apple, context: context);
                  } else {
                    showSnackBar(UiUtils.getTranslatedLabel(context, 'agreeTCFirst'), context);
                  }
                },
                img: 'apple_logo',
                startPad: 10),
          BottomCommButton(
              onTap: () {
                if (!isPolicyAvailable) {
                  showSnackBar(UiUtils.getTranslatedLabel(context, 'addTCFirst'), context);
                } else if (isChecked) {
                  Navigator.of(context).pushNamed(Routes.requestOtp);
                } else {
                  showSnackBar(UiUtils.getTranslatedLabel(context, 'agreeTCFirst'), context);
                }
              },
              img: 'phone_button',
              startPad: 10,
              btnColor: UiUtils.getColorScheme(context).secondaryContainer)
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: showContent(),
    );
  }
}
