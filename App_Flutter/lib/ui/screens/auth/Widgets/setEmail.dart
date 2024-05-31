// ignore_for_file: file_names, must_be_immutable

import 'package:flutter/material.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/utils/validators.dart';

import 'fieldFocusChange.dart';

class SetEmail extends StatelessWidget {
  final FocusNode? currFocus;
  final FocusNode? nextFocus;
  final TextEditingController emailC;
  late String email;
  final double topPad;

  SetEmail({
    Key? key,
    this.currFocus,
    this.nextFocus,
    required this.emailC,
    required this.email,
    required this.topPad,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(top: topPad),
      child: TextFormField(
        focusNode: currFocus,
        textInputAction: TextInputAction.next,
        controller: emailC,
        style: Theme.of(context).textTheme.titleMedium?.copyWith(
              color: UiUtils.getColorScheme(context).primaryContainer,
            ),
        validator: (val) => Validators.emailValidation(val!, context),
        onFieldSubmitted: (v) {
          if (currFocus != null || nextFocus != null) fieldFocusChange(context, currFocus!, nextFocus!);
        },
        decoration: InputDecoration(
          hintText: UiUtils.getTranslatedLabel(context, 'emailLbl'),
          hintStyle: Theme.of(context).textTheme.titleMedium?.copyWith(
                color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.5),
              ),
          filled: true,
          fillColor: Theme.of(context).colorScheme.background,
          contentPadding: const EdgeInsets.symmetric(horizontal: 25, vertical: 17),
          focusedBorder: OutlineInputBorder(
            borderSide: BorderSide(color: UiUtils.getColorScheme(context).outline.withOpacity(0.7)),
            borderRadius: BorderRadius.circular(10.0),
          ),
          enabledBorder: OutlineInputBorder(
            borderSide: BorderSide.none,
            borderRadius: BorderRadius.circular(10.0),
          ),
        ),
      ),
    );
  }
}
