// ignore_for_file: file_names, use_build_context_synchronously

import 'dart:convert';
import 'dart:io';

import 'package:dio/dio.dart';
import 'package:crypto/crypto.dart';
import 'package:flutter/material.dart';
import 'package:firebase_auth/firebase_auth.dart' as fAuth;

import 'package:flutter_login_facebook/flutter_login_facebook.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:sign_in_with_apple/sign_in_with_apple.dart';

import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/ui/widgets/SnackBarWidget.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';

class AuthRemoteDataSource {
  final fAuth.FirebaseAuth _firebaseAuth = fAuth.FirebaseAuth.instance;
  final GoogleSignIn _googleSignIn = GoogleSignIn();
  final _facebookSignin = FacebookLogin();

  Future<dynamic> loginAuth(
      {required BuildContext context, required String firebaseId, required String name, required String email, required String type, required String profile, required String mobile}) async {
    try {
      final body = {FIREBASE_ID: firebaseId, NAME: name, TYPE: type, EMAIL: email};
      if (profile != "") {
        body[PROFILE] = profile;
      }
      if (mobile != "") {
        body[MOBILE] = mobile;
      }
      var result = await Api.post(body: body, url: Api.getUserSignUpApi);
      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }

  Future<dynamic> deleteUserAcc({required BuildContext context, required String userId}) async {
    try {
      final body = {USER_ID: userId};

      final result = await Api.post(body: body, url: Api.userDeleteApi);

      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }

  //to update fcmId of user's
  Future<dynamic> updateUserData({
    required String userId,
    String? name,
    String? mobile,
    String? email,
    String? filePath,
  }) async {
    try {
      Map<String, dynamic> body = {USER_ID: userId};
      Map<String, dynamic> result = {};

      if (filePath != null) {
        body[IMAGE] = await MultipartFile.fromFile(filePath);
        result = await Api.post(body: body, url: Api.setProfileApi);
      } else {
        if (name != null) {
          body[NAME] = name;
        }
        if (mobile != null) {
          body[MOBILE] = mobile;
        }
        if (email != null) {
          body[EMAIL] = email;
        }
        result = await Api.post(body: body, url: Api.setUpdateProfileApi);
      }
      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }

  //to update fcmId of user's
  Future<dynamic> updateFcmId({required String userId, required String fcmId, required BuildContext context}) async {
    try {
      //body of post request
      final body = {USER_ID: userId, FCM_ID: fcmId};
      final result = await Api.post(body: body, url: Api.updateFCMIdApi);

      return result;
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }

  Future<dynamic> registerToken({required String fcmId, required BuildContext context}) async {
    try {
      final body = {TOKEN: fcmId};
      final result = await Api.post(body: body, url: Api.setRegisterToken);
      return result;
    } on SocketException catch (e) {
      throw SocketException(e.toString());
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }

  //SignIn user will accept AuthProvider (enum)
  Future<Map<String, dynamic>> socialSignInUser({required AuthProvider authProvider, required BuildContext context, String? email, String? password, String? otp, String? verifiedId}) async {
    Map<String, dynamic> result = {};

    try {
      switch (authProvider) {
        case AuthProvider.gmail:
          fAuth.UserCredential? userCredential = await signInWithGoogle(context);
          if (userCredential != null) {
            result['user'] = userCredential.user!;
            return result;
          } else {
            throw ApiMessageAndCodeException(errorMessage: UiUtils.getTranslatedLabel(context, 'somethingMSg'));
          }

        case AuthProvider.mobile:
          fAuth.UserCredential? userCredential = await signInWithPhone(context: context, otp: otp!, verifiedId: verifiedId!);
          if (userCredential != null) {
            result['user'] = userCredential.user!;
            return result;
          } else {
            throw ApiMessageAndCodeException(errorMessage: UiUtils.getTranslatedLabel(context, 'somethingMSg'));
          }
        case AuthProvider.fb:
          final faceBookAuthResult = await signInWithFacebook();
          if (faceBookAuthResult != null) {
            result['user'] = faceBookAuthResult.user!;
            return result;
          } else {
            throw ApiMessageAndCodeException(errorMessage: UiUtils.getTranslatedLabel(context, 'somethingMSg'));
          }
        case AuthProvider.apple:
          fAuth.UserCredential? userCredential = await signInWithApple(context);
          if (userCredential != null) {
            result['user'] = userCredential.user!;
            return result;
          } else {
            throw ApiMessageAndCodeException(errorMessage: UiUtils.getTranslatedLabel(context, 'somethingMSg'));
          }
        case AuthProvider.email:
          final userCredential = await signInWithEmailPassword(email: email!, password: password!, context: context);
          if (userCredential != null) {
            result['user'] = userCredential.user!;
            return result;
          } else {
            return {};
          }
      }
    } on SocketException catch (_) {
      throw ApiMessageAndCodeException(errorMessage: UiUtils.getTranslatedLabel(context, 'internetmsg'));
    } on fAuth.FirebaseAuthException catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }

  Future<fAuth.UserCredential?> signInWithPhone({required BuildContext context, required String otp, required String verifiedId}) async {
    String code = otp.trim();

    if (code.length == 6) {
      try {
        final fAuth.PhoneAuthCredential credential = fAuth.PhoneAuthProvider.credential(
          verificationId: verifiedId,
          smsCode: otp,
        );
        final fAuth.UserCredential authResult = await _firebaseAuth.signInWithCredential(credential);
        final fAuth.User? user = authResult.user;
        if (user != null) {
          assert(!user.isAnonymous);

          final fAuth.User? currentUser = _firebaseAuth.currentUser;
          assert(user.uid == currentUser?.uid);
          showSnackBar(UiUtils.getTranslatedLabel(context, 'otpMsg'), context);
          return authResult;
        } else {
          showSnackBar(UiUtils.getTranslatedLabel(context, 'otpError'), context);
          return null;
        }
      } on fAuth.FirebaseAuthException catch (authError) {
        if (authError.code == 'invalidVerificationCode') {
          showSnackBar(UiUtils.getTranslatedLabel(context, 'invalidVerificationCode'), context);
          return null;
        } else {
          showSnackBar(authError.message.toString(), context);
          return null;
        }
      } on fAuth.FirebaseException catch (e) {
        showSnackBar(e.message.toString(), context);
        return null;
      } catch (e) {
        showSnackBar(e.toString(), context);
        return null;
      }
    } else {
      showSnackBar(UiUtils.getTranslatedLabel(context, 'enterOtpTxt'), context);
      return null;
    }
  }

  //sign in with email and password in firebase
  Future<fAuth.UserCredential?> signInWithEmailPassword({required String email, required String password, required BuildContext context}) async {
    try {
      final fAuth.UserCredential userCredential = await _firebaseAuth.signInWithEmailAndPassword(
        email: email,
        password: password,
      );

      return userCredential;
    } on fAuth.FirebaseAuthException catch (authError) {
      if (authError.code == 'userNotFound') {
        showSnackBar(UiUtils.getTranslatedLabel(context, 'userNotFound'), context);
      } else if (authError.code == 'wrongPassword') {
        showSnackBar(UiUtils.getTranslatedLabel(context, 'wrongPassword'), context);
      } else {
        throw ApiMessageAndCodeException(errorMessage: authError.message!);
      }
    } on fAuth.FirebaseException catch (e) {
      showSnackBar(e.toString(), context);
    } catch (e) {
      String errorMessage = e.toString();
      showSnackBar(errorMessage, context);
    }
    return null;
  }

  //signIn using google account
  Future<fAuth.UserCredential?> signInWithGoogle(BuildContext context) async {
    final GoogleSignInAccount? googleUser = await _googleSignIn.signIn();
    if (googleUser == null) {
      ApiMessageAndCodeException(errorMessage: UiUtils.getTranslatedLabel(context, 'somethingMSg'));
      return null;
    }
    final GoogleSignInAuthentication googleAuth = await googleUser.authentication;

    final fAuth.AuthCredential credential = fAuth.GoogleAuthProvider.credential(
      accessToken: googleAuth.accessToken,
      idToken: googleAuth.idToken,
    );
    final fAuth.UserCredential userCredential = await _firebaseAuth.signInWithCredential(credential);
    return userCredential;
  }

  Future<fAuth.UserCredential?> signInWithFacebook() async {
    final res = await _facebookSignin.logIn(permissions: [
      FacebookPermission.publicProfile,
      FacebookPermission.email,
    ]);

// Check result status
    switch (res.status) {
      case FacebookLoginStatus.success:
        // Send access token to server for validation and auth
        final FacebookAccessToken? accessToken = res.accessToken;
        fAuth.AuthCredential authCredential = fAuth.FacebookAuthProvider.credential(accessToken!.token);
        final fAuth.UserCredential userCredential = await _firebaseAuth.signInWithCredential(authCredential);
        return userCredential;
      case FacebookLoginStatus.cancel:
        return null;

      case FacebookLoginStatus.error:
        return null;
      default:
        return null;
    }
  }

  String sha256ofString(String input) {
    final bytes = utf8.encode(input);
    final digest = sha256.convert(bytes);
    return digest.toString();
  }

  Future<fAuth.UserCredential?> signInWithApple(BuildContext context) async {
    try {
      final rawNonce = generateNonce();
      final nonce = sha256ofString(rawNonce);

      final appleCredential = await SignInWithApple.getAppleIDCredential(
        scopes: [
          AppleIDAuthorizationScopes.email,
          AppleIDAuthorizationScopes.fullName,
        ],
        nonce: nonce,
      );

      final oauthCredential = fAuth.OAuthProvider("apple.com").credential(
        idToken: appleCredential.identityToken,
        rawNonce: rawNonce,
      );

      final fAuth.UserCredential authResult = await fAuth.FirebaseAuth.instance.signInWithCredential(oauthCredential);

      return authResult;
    } on fAuth.FirebaseAuthException catch (authError) {
      showSnackBar(authError.message!, context);
      return null;
    } on fAuth.FirebaseException catch (e) {
      showSnackBar(e.toString(), context);
      return null;
    } catch (e) {
      String errorMessage = e.toString();

      if (errorMessage == "Null check operator used on a null value") {
        //if user goes back from selecting Account
        //in case of User gmail not selected & back to Login screen
        showSnackBar(UiUtils.getTranslatedLabel(context, 'cancelLogin'), context);
        return null;
      } else {
        showSnackBar(errorMessage, context);
        return null;
      }
    }
  }

  Future<void> signOut(AuthProvider? authProvider) async {
    _firebaseAuth.signOut();
    if (authProvider == AuthProvider.gmail) {
      _googleSignIn.signOut();
    } else if (authProvider == AuthProvider.fb) {
      _facebookSignin.logOut();
    } else {
      _firebaseAuth.signOut();
    }
  }
}
