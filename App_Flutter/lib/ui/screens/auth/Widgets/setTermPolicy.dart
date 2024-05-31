// ignore_for_file: file_names

import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:news/app/routes.dart';
import 'package:news/cubits/privacyTermsCubit.dart';
import 'package:news/utils/uiUtils.dart';

setTermPolicyTxt(BuildContext context, bool isChecked, Function updateState, PrivacyTermsFetchSuccess state) {
  return Container(
    alignment: AlignmentDirectional.bottomCenter,
    padding: const EdgeInsets.only(top: 25.0),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Checkbox(
          value: isChecked,
          checkColor: UiUtils.getColorScheme(context).secondary,
          activeColor: UiUtils.getColorScheme(context).primaryContainer,
          onChanged: (bool? value) {
            isChecked = value!;
            updateState(isChecked);
          },
        ),
        RichText(
          textAlign: TextAlign.center,
          text: TextSpan(children: [
            TextSpan(
              text: "${UiUtils.getTranslatedLabel(context, 'agreeTermPolicyLbl')}\n",
              style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                    color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7),
                    overflow: TextOverflow.ellipsis,
                  ),
            ),
            TextSpan(
              text: UiUtils.getTranslatedLabel(context, 'termLbl'),
              style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                    color: Theme.of(context).primaryColor,
                    decoration: TextDecoration.underline,
                    overflow: TextOverflow.ellipsis,
                  ),
              recognizer: TapGestureRecognizer()
                ..onTap = (() {
                  Navigator.of(context).pushNamed(Routes.privacy, arguments: {"from": "login", "title": state.termsPolicy.title, "desc": state.termsPolicy.pageContent});
                }),
            ),
            TextSpan(
              text: "\t${UiUtils.getTranslatedLabel(context, 'andLbl')}\t",
              style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                    color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7),
                    overflow: TextOverflow.ellipsis,
                  ),
            ),
            TextSpan(
              text: UiUtils.getTranslatedLabel(context, 'priPolicy'),
              style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                    color: Theme.of(context).primaryColor,
                    decoration: TextDecoration.underline,
                    overflow: TextOverflow.ellipsis,
                  ),
              recognizer: TapGestureRecognizer()
                ..onTap = (() {
                  Navigator.of(context).pushNamed(Routes.privacy, arguments: {"from": "login", "title": state.privacyPolicy.title, "desc": state.privacyPolicy.pageContent});
                }),
            ),
          ]),
        ),
      ],
    ),
  );
}
