// ignore_for_file: file_names

import 'package:hive/hive.dart';
import 'package:news/utils/hiveBoxKeys.dart';

//AuthLocalDataSource will communicate with local database (hive)
class AuthLocalDataSource {
  bool? checkIsAuth() {
    return Hive.box(authBoxKey).get(isLogInKey, defaultValue: false) ?? false;
  }

  String? getJwtToken() {
    return Hive.box(authBoxKey).get(jwtTokenKey, defaultValue: "");
  }

  String? getId() {
    return Hive.box(authBoxKey).get(userIdKey, defaultValue: "0");
  }

  Future<void> setId(String? id) async {
    Hive.box(authBoxKey).put(userIdKey, id);
  }

  String? getName() {
    return Hive.box(authBoxKey).get(userNameKey, defaultValue: "");
  }

  Future<void> setName(String? name) async {
    Hive.box(authBoxKey).put(userNameKey, name);
  }

  String? getEmail() {
    return Hive.box(authBoxKey).get(userEmailKey, defaultValue: "");
  }

  Future<void> setEmail(String? email) async {
    Hive.box(authBoxKey).put(userEmailKey, email);
  }

  String? getMobile() {
    return Hive.box(authBoxKey).get(userMobKey, defaultValue: "");
  }

  Future<void> setMobile(String? mobile) async {
    Hive.box(authBoxKey).put(userMobKey, mobile);
  }

  String? getType() {
    return Hive.box(authBoxKey).get(userTypeKey, defaultValue: "");
  }

  Future<void> setType(String? type) async {
    Hive.box(authBoxKey).put(userTypeKey, type);
  }

  String? getProfile() {
    return Hive.box(authBoxKey).get(userProfileKey, defaultValue: "");
  }

  Future<void> setProfile(String? image) async {
    Hive.box(authBoxKey).put(userProfileKey, image);
  }

  String? getStatus() {
    return Hive.box(authBoxKey).get(userStatusKey, defaultValue: "");
  }

  Future<void> setStatus(String? status) async {
    Hive.box(authBoxKey).put(userStatusKey, status);
  }

  String? getRole() {
    return Hive.box(authBoxKey).get(userRoleKey, defaultValue: "");
  }

  Future<void> setRole(String? role) async {
    Hive.box(authBoxKey).put(userRoleKey, role);
  }

  Future<void> changeAuthStatus(bool? authStatus) async {
    Hive.box(authBoxKey).put(isLogInKey, authStatus);
  }
}
