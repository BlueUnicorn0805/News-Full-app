// ignore_for_file: file_names, use_build_context_synchronously

import 'package:firebase_auth/firebase_auth.dart' as fAuth;
import 'package:flutter/material.dart';
import 'package:news/utils/api.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/utils/strings.dart';
import 'package:news/data/repositories/Auth/authLocalDataSource.dart';
import 'package:news/data/repositories/Auth/authRemoteDataSource.dart';

class AuthRepository {
  static final AuthRepository _authRepository = AuthRepository._internal();
  late AuthLocalDataSource _authLocalDataSource;
  late AuthRemoteDataSource _authRemoteDataSource;

  factory AuthRepository() {
    _authRepository._authLocalDataSource = AuthLocalDataSource();
    _authRepository._authRemoteDataSource = AuthRemoteDataSource();
    return _authRepository;
  }

  AuthRepository._internal();

  AuthLocalDataSource get authLocalDataSource => _authLocalDataSource;

  //to get auth detials stored in hive box
  getLocalAuthDetails() {
    return {
      "isLogIn": _authLocalDataSource.checkIsAuth(),
      ID: _authLocalDataSource.getId(),
      NAME: _authLocalDataSource.getName(),
      EMAIL: _authLocalDataSource.getEmail(),
      MOBILE: _authLocalDataSource.getMobile(),
      TYPE: _authLocalDataSource.getType(),
      PROFILE: _authLocalDataSource.getProfile(),
      STATUS: _authLocalDataSource.getStatus(),
      ROLE: _authLocalDataSource.getRole(),
    };
  }

  setLocalAuthDetails(
      {required bool authStatus,
      required String id,
      required String name,
      required String email,
      required String mobile,
      required String type,
      required String profile,
      required String status,
      required String role}) {
    _authLocalDataSource.changeAuthStatus(authStatus);
    _authLocalDataSource.setId(id);
    _authLocalDataSource.setName(name);
    _authLocalDataSource.setEmail(email);
    _authLocalDataSource.setMobile(mobile);
    _authLocalDataSource.setType(type);
    _authLocalDataSource.setProfile(profile);
    _authLocalDataSource.setStatus(status);
    _authLocalDataSource.setRole(role);
  }

  //First we signin user with given provider then add user details
  Future<Map<String, dynamic>> signInUser({required BuildContext context, required AuthProvider authProvider, String? email, String? password, String? otp, String? verifiedId}) async {
    try {
      final result = await _authRemoteDataSource.socialSignInUser(context: context, authProvider: authProvider, email: email, password: password, verifiedId: verifiedId, otp: otp);
      final user = result['user'] as fAuth.User;
      var providerData = user.providerData[0];
      if ((authProvider == AuthProvider.email && !user.emailVerified)) {
        throw ApiException(UiUtils.getTranslatedLabel(context, 'verifyEmailMsg'));
      }

      Map<String, dynamic> userDataTest = await _authRemoteDataSource.loginAuth(
          mobile: providerData.phoneNumber ?? "",
          context: context,
          email: providerData.email ?? "",
          firebaseId: user.uid,
          name: providerData.displayName ?? "",
          profile: providerData.photoURL ?? "",
          type: authProvider.name);

      if (userDataTest["data"][STATUS] != "0") {
        setLocalAuthDetails(
            type: userDataTest["data"][TYPE] ?? "",
            profile: userDataTest["data"][PROFILE] ?? "",
            name: userDataTest["data"][NAME] ?? "",
            email: userDataTest["data"][EMAIL] ?? "",
            authStatus: true,
            id: userDataTest["data"][ID] ?? "0",
            mobile: userDataTest["data"][MOBILE] ?? "",
            role: userDataTest["data"][ROLE] ?? "",
            status: userDataTest["data"][STATUS] ?? "");
      }

      return userDataTest;
    } catch (e) {
      signOut(authProvider);
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }

  //to update fcmId user's data to database. This will be in use when authenticating using fcmId
  Future<Map<String, dynamic>> updateFcmId({required String userId, required String fcmId, required BuildContext context}) async {
    final result = await _authRemoteDataSource.updateFcmId(userId: userId, fcmId: fcmId, context: context);
    return result;
  }

  Future<dynamic> updateUserData({required String userId, String? name, String? mobile, String? email, String? filePath, required BuildContext context}) async {
    final result = await _authRemoteDataSource.updateUserData(userId: userId, email: email, name: name, mobile: mobile, filePath: filePath);
    if (name != null) {
      _authLocalDataSource.setName(name);
    }
    if (mobile != null) {
      _authLocalDataSource.setMobile(mobile);
    }
    if (email != null) {
      _authLocalDataSource.setEmail(email);
    }
    if (filePath != null) {
      _authLocalDataSource.setProfile(result['file_path']);
    }

    return result;
  }

  //to update fcmId user's data to database. This will be in use when authenticating using fcmId
  Future<Map<String, dynamic>> registerToken({required String fcmId, required BuildContext context}) async {
    final result = await _authRemoteDataSource.registerToken(fcmId: fcmId, context: context);
    return result;
  }

  //to delete my account
  Future<dynamic> deleteUser({required BuildContext context, required String userId}) async {
    final result = await _authRemoteDataSource.deleteUserAcc(userId: userId, context: context);
    return result;
  }

  Future<void> signOut(AuthProvider authProvider) async {
    _authRemoteDataSource.signOut(authProvider);
    await _authLocalDataSource.changeAuthStatus(false);
    await _authLocalDataSource.setId("0");
    await _authLocalDataSource.setName("");
    await _authLocalDataSource.setEmail("");
    await _authLocalDataSource.setMobile("");
    await _authLocalDataSource.setType("");
    await _authLocalDataSource.setProfile("");
    await _authLocalDataSource.setRole("");
    await _authLocalDataSource.setStatus("");
  }
}
