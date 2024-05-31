// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/ui/widgets/customBackBtn.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/utils/uiUtils.dart';

setCustomAppBar({required double height, required bool isBackBtn, required String label, required BuildContext context, required bool isConvertText, double? horizontalPad}) {
  return PreferredSize(
      preferredSize: Size(double.infinity, height),
      child: AppBar(
        leading: (isBackBtn) ? CustomBackButton(horizontalPadding: horizontalPad ?? 15) : null,
        titleSpacing: 0.0,
        automaticallyImplyLeading: false,
        centerTitle: false,
        backgroundColor: Colors.transparent,
        title: Padding(
          padding: EdgeInsetsDirectional.only(start: isBackBtn ? 0 : 20),
          child: !isConvertText
              ? Text(label, style: Theme.of(context).textTheme.titleLarge?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.w600, letterSpacing: 0.5))
              : CustomTextLabel(
                  text: label,
                  textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.w600, letterSpacing: 0.5),
                ),
        ),
      ));
}
