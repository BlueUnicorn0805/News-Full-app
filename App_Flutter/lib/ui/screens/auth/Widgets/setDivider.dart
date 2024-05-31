// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/utils/uiUtils.dart';

//set divider on text
setDividerOr(BuildContext context) {
  return Padding(
      padding: const EdgeInsetsDirectional.only(top: 30.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          CustomTextLabel(
            text: 'orLbl',
            textStyle: Theme.of(context).textTheme.titleMedium?.merge(TextStyle(
                  color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7),
                  fontSize: 12.0,
                )),
          ),
        ],
      ));
}
