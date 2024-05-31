// ignore_for_file: file_names, use_build_context_synchronously

import 'package:country_code_picker/country_code_picker.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:news/ui/styles/colors.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/ui/widgets/SnackBarWidget.dart';
import 'package:news/app/routes.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/utils/constant.dart';
import 'package:news/utils/internetConnectivity.dart';
import 'package:news/utils/validators.dart';

class RequestOtp extends StatefulWidget {
  const RequestOtp({super.key});

  @override
  RequestOtpState createState() => RequestOtpState();
}

class RequestOtpState extends State<RequestOtp> {
  TextEditingController phoneC = TextEditingController();
  String? phone, conCode;
  final GlobalKey<FormState> _formkey = GlobalKey<FormState>();
  bool isLoading = false;
  CountryCode? code;
  String? verificationId;
  String errorMessage = '';
  final FirebaseAuth _auth = FirebaseAuth.instance;

  @override
  void initState() {
    super.initState();
  }

  @override
  void dispose() {
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        body: Stack(
      children: <Widget>[
        SafeArea(
          child: showContent(),
        ),
        showCircularProgress(isLoading, Theme.of(context).primaryColor)
      ],
    ));
  }

  //show form content
  showContent() {
    return Container(
      padding: const EdgeInsetsDirectional.all(20.0),
      child: SingleChildScrollView(
          child: Form(
              key: _formkey,
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: <Widget>[
                Align(
                    //backButton
                    alignment: Alignment.topLeft,
                    child: InkWell(
                      onTap: () => Navigator.of(context).pop(),
                      splashColor: Colors.transparent,
                      child: const Icon(Icons.keyboard_backspace_rounded),
                    )),
                const SizedBox(height: 50),
                otpVerifySet(),
                enterMblSet(),
                receiveDigitSet(),
                setCodeWithMono(),
                reqOtpBtn()
              ]))),
    );
  }

  otpVerifySet() {
    return Center(
        child: CustomTextLabel(
            text: 'loginLbl',
            textStyle: Theme.of(context).textTheme.headlineSmall?.copyWith(color: Theme.of(context).primaryColor, fontWeight: FontWeight.w800, letterSpacing: 0.5),
            textAlign: TextAlign.center));
  }

  enterMblSet() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.only(top: 35.0),
        child: CustomTextLabel(
          text: 'enterMblLbl',
          textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.w600),
        ),
      ),
    );
  }

  receiveDigitSet() {
    return Container(
      padding: const EdgeInsets.only(top: 20.0),
      alignment: Alignment.center,
      child: CustomTextLabel(
          text: 'receiveDigitLbl',
          textStyle: Theme.of(context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.8), fontSize: 14),
          textAlign: TextAlign.center),
    );
  }

  setCodeWithMono() {
    return Padding(
        padding: const EdgeInsets.only(top: 30.0),
        child: Container(
            decoration: BoxDecoration(borderRadius: BorderRadius.circular(10.0), color: Theme.of(context).colorScheme.background),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              mainAxisAlignment: MainAxisAlignment.start,
              children: <Widget>[setCountryCode(), setMono()],
            )));
  }

  setCountryCode() {
    double width = MediaQuery.of(context).size.width;
    double height = MediaQuery.of(context).size.height;
    return SizedBox(
        height: 55,
        child: CountryCodePicker(
            boxDecoration: BoxDecoration(color: Theme.of(context).colorScheme.background),
            searchDecoration: InputDecoration(hintStyle: TextStyle(color: UiUtils.getColorScheme(context).primaryContainer), fillColor: UiUtils.getColorScheme(context).primaryContainer),
            initialSelection: yourCountryCode,
            dialogSize: Size(width - 50, height - 50),
            builder: (CountryCode? code) {
              return Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Padding(
                      padding: const EdgeInsetsDirectional.only(top: 10.0, bottom: 10.0, start: 10.0, end: 4.0),
                      child:
                          ClipRRect(borderRadius: BorderRadius.circular(5.0), child: Image.asset(code!.flagUri.toString(), package: 'country_code_picker', height: 40, width: 40, fit: BoxFit.cover))),
                  Icon(Icons.arrow_drop_down, size: 21, color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7)),
                  SizedBox(
                      //divider
                      width: 5.0,
                      height: 35.0,
                      child: VerticalDivider(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7), thickness: 2.0)),
                  Container(
                      //CountryCode
                      width: 55.0,
                      height: 55.0,
                      padding: const EdgeInsetsDirectional.only(start: 5.0),
                      alignment: Alignment.center,
                      child: Text(code.dialCode.toString(),
                          style: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7)),
                          overflow: TextOverflow.ellipsis,
                          softWrap: true)),
                ],
              );
            },
            onChanged: (CountryCode countryCode) {
              conCode = countryCode.dialCode;
            },
            onInit: (CountryCode? code) {
              conCode = code?.dialCode;
            }));
  }

  setMono() {
    return Expanded(
      child: Padding(
        padding: const EdgeInsetsDirectional.only(top: 10.0, bottom: 10.0),
        child: Container(
            height: 55,
            width: MediaQuery.of(context).size.width * 0.57,
            alignment: Alignment.center,
            child: TextFormField(
              keyboardType: const TextInputType.numberWithOptions(signed: true, decimal: true),
              controller: phoneC,
              style: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7)),
              inputFormatters: [FilteringTextInputFormatter.digitsOnly],
              validator: (val) => Validators.mobValidation(val!, context),
              onSaved: (String? value) => phone = value,
              decoration: InputDecoration(
                hintText: '999-999-9999',
                hintStyle: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.5)),
                enabledBorder: InputBorder.none,
                focusedBorder: InputBorder.none,
              ),
            )),
      ),
    );
  }

  Future<void> verifyPhone(BuildContext context) async {
    try {
      await _auth.verifyPhoneNumber(
          phoneNumber: "+$conCode${phoneC.text.trim()}",
          verificationCompleted: (AuthCredential phoneAuthCredential) {
            showSnackBar(phoneAuthCredential.toString(), context);
          },
          verificationFailed: (FirebaseAuthException exception) {
            setState(() => isLoading = false);
            if (exception.code == "invalidPhoneNumber") {
              showSnackBar(UiUtils.getTranslatedLabel(context, 'invalidPhoneNumber'), context);
            } else {
              showSnackBar('${exception.message}', context);
            }
          },
          codeAutoRetrievalTimeout: (String verId) => verificationId = verId,
          codeSent: processCodeSent(),
          //smsOTPSent,
          timeout: const Duration(seconds: 60));
    } on FirebaseAuthException catch (authError) {
      setState(() => isLoading = false);
      showSnackBar(authError.message!, context);
    } on FirebaseException catch (e) {
      setState(() => isLoading = false);
      showSnackBar(e.toString(), context);
    } catch (e) {
      setState(() => isLoading = false);
      showSnackBar(e.toString(), context);
    }
  }

  processCodeSent() {
    smsOTPSent(String? verId, [int? forceCodeResend]) async {
      verificationId = verId;
      setState(() => isLoading = false);
      showSnackBar(UiUtils.getTranslatedLabel(context, 'codeSent'), context);
      await Navigator.of(context).pushNamed(Routes.verifyOtp, arguments: {"verifyId": verificationId, "countryCode": conCode, "mono": phoneC.text.trim()});
    }

    return smsOTPSent;
  }

  reqOtpBtn() {
    return Container(
      alignment: Alignment.center,
      padding: const EdgeInsets.only(top: 60.0),
      child: InkWell(
        child: Container(
          height: 55.0,
          width: MediaQuery.of(context).size.width * 0.9,
          alignment: Alignment.center,
          decoration: BoxDecoration(color: Theme.of(context).primaryColor, borderRadius: BorderRadius.circular(7.0)),
          child: CustomTextLabel(text: 'reqOtpLbl', textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(color: secondaryColor, fontWeight: FontWeight.w600, letterSpacing: 0.5, fontSize: 21)),
        ),
        onTap: () async {
          FocusScope.of(context).unfocus(); //dismiss keyboard
          if (validateAndSave()) {
            if (await InternetConnectivity.isNetworkAvailable()) {
              setState(() => isLoading = true);
              verifyPhone(context);
            } else {
              showSnackBar(UiUtils.getTranslatedLabel(context, 'internetmsg'), context);
            }
          }
        },
      ),
    );
  }

  //check validation of form data
  bool validateAndSave() {
    final form = _formkey.currentState;
    form!.save();
    return (form.validate()) ? true : false;
  }
}
