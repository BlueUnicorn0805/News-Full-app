// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/utils/uiUtils.dart';

showSnackBar(String msg, BuildContext context) {
  ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(
      content: Text(
        msg,
        textAlign: TextAlign.center,
        style: TextStyle(
          color: Theme.of(context).colorScheme.background,
        ),
      ),
      showCloseIcon: false,
      duration: const Duration(milliseconds: 1000), //bydefault 4000 ms
      backgroundColor: UiUtils.getColorScheme(context).primaryContainer,
      elevation: 1.0,
    ),
  );
}
