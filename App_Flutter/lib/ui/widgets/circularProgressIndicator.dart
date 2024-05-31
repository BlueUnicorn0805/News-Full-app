// ignore_for_file: file_names

import 'package:flutter/material.dart';

Widget showCircularProgress(bool isProgress, Color color) {
  if (isProgress) {
    return Center(child: CircularProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(color)));
  }
  return const SizedBox.shrink();
}
