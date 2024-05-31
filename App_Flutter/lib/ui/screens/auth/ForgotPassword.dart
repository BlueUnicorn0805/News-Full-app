// ignore_for_file: file_names, deprecated_member_use, use_build_context_synchronously

import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import 'package:news/ui/widgets/customBackBtn.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/utils/internetConnectivity.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/ui/widgets/SnackBarWidget.dart';
import 'package:news/ui/screens/auth/Widgets/setEmail.dart';
import 'package:news/ui/screens/auth/Widgets/setLoginAndSignUpBtn.dart';

class ForgotPassword extends StatefulWidget {
  const ForgotPassword({super.key});

  @override
  FrgtPswdState createState() => FrgtPswdState();
}

class FrgtPswdState extends State<ForgotPassword> {
  TextEditingController emailC = TextEditingController();
  final FirebaseAuth _auth = FirebaseAuth.instance;
  final GlobalKey<FormState> _formkey = GlobalKey<FormState>();

  @override
  void dispose() {
    emailC.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(body: screenContent());
  }

  Widget backBtn() {
    return const Padding(padding: EdgeInsets.only(top: 10.0), child: CustomBackButton(horizontalPadding: 0));
  }

  Widget forgotIcon() {
    return Container(
      padding: const EdgeInsets.all(20.0),
      child: Center(
        child: SvgPicture.asset(
          UiUtils.getSvgImagePath("forgot"),
          semanticsLabel: 'forgot pswd icon',
          width: 150,
          height: 150,
          fit: BoxFit.fill,
          colorFilter: ColorFilter.mode(UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7), BlendMode.srcIn),
        ),
      ),
    );
  }

  Widget forgotPassLbl() {
    return Center(
      child: CustomTextLabel(
        text: 'forgotPassLbl',
        textStyle: Theme.of(context).textTheme.titleMedium?.copyWith(
              fontWeight: FontWeight.w600,
              fontSize: 22,
              color: UiUtils.getColorScheme(context).primaryContainer,
            ),
      ),
    );
  }

  Widget forgotPassHead() {
    return Padding(
        padding: const EdgeInsetsDirectional.only(top: 20.0),
        child: CustomTextLabel(
          text: 'frgtPassHead',
          textStyle: Theme.of(context).textTheme.titleMedium?.copyWith(
                fontWeight: FontWeight.bold,
                fontSize: 18,
                color: UiUtils.getColorScheme(context).primaryContainer,
              ),
        ));
  }

  Widget forgotPassSubHead() {
    return Padding(
        padding: const EdgeInsetsDirectional.only(top: 30.0),
        child: CustomTextLabel(
          text: 'forgotPassSub',
          textStyle: Theme.of(context).textTheme.bodyLarge?.copyWith(
                fontWeight: FontWeight.normal,
                fontSize: 16.0,
                color: UiUtils.getColorScheme(context).primaryContainer,
              ),
        ));
  }

  Widget emailTextCtrl() {
    return SetEmail(emailC: emailC, email: emailC.text, topPad: 25);
  }

  Widget submitBtn() {
    return SetLoginAndSignUpBtn(
        onTap: () async {
          FocusScope.of(context).unfocus(); //dismiss keyboard

          if (await InternetConnectivity.isNetworkAvailable()) {
            Future.delayed(const Duration(seconds: 1)).then((_) async {
              if (emailC.text.isEmpty) {
                showSnackBar(UiUtils.getTranslatedLabel(context, 'emailValid'), context);
              } else {
                try {
                  await _auth.sendPasswordResetEmail(email: emailC.text.trim());
                  final form = _formkey.currentState;
                  form!.save();
                  if (form.validate()) {
                    showSnackBar(UiUtils.getTranslatedLabel(context, 'passReset'), context);
                    Navigator.pop(context);
                  }
                } on FirebaseAuthException catch (e) {
                  debugPrint(e.code);
                  debugPrint(e.message);
                  if (e.code == "userNotFound") {
                    showSnackBar(UiUtils.getTranslatedLabel(context, 'userNotFound'), context);
                  } else {
                    showSnackBar(e.message!, context);
                  }
                }
              }
            });
          } else {
            showSnackBar(UiUtils.getTranslatedLabel(context, 'internetmsg'), context);
          }
        },
        text: 'submitBtn',
        topPad: 30);
  }

  Widget screenContent() {
    return Container(
        padding: const EdgeInsetsDirectional.all(20.0),
        child: SingleChildScrollView(
            child: Form(
          key: _formkey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.max,
            children: [backBtn(), const SizedBox(height: 50), forgotIcon(), forgotPassLbl(), forgotPassHead(), forgotPassSubHead(), emailTextCtrl(), submitBtn()],
          ),
        )));
  }
}
