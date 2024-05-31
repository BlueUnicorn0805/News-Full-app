// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/utils/uiUtils.dart';

class CustomBackButton extends StatelessWidget {
  final Function? onTap;
  final double? horizontalPadding;

  const CustomBackButton({Key? key, this.onTap, this.horizontalPadding}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Padding(
        padding: EdgeInsets.symmetric(horizontal: horizontalPadding ?? 0),
        child: InkWell(
            onTap: () => onTap ?? Navigator.of(context).pop(),
            splashColor: Colors.transparent,
            highlightColor: Colors.transparent,
            child: Icon(Icons.arrow_back, color: UiUtils.getColorScheme(context).primaryContainer)));
  }
}
