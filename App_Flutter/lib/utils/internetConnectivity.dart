// ignore_for_file: file_names

import 'package:connectivity_plus/connectivity_plus.dart';

class InternetConnectivity {
  static Future<bool> isNetworkAvailable() async {
    final ConnectivityResult connectivityResult = await Connectivity().checkConnectivity();
    if (connectivityResult == ConnectivityResult.mobile) {
      return true;
    } else if (connectivityResult == ConnectivityResult.wifi) {
      return true;
    }
    return false;
  }
}
