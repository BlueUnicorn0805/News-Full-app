// ignore_for_file: file_names, must_be_immutable

import 'package:flutter/material.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/utils/validators.dart';
import 'package:news/ui/screens/auth/Widgets/fieldFocusChange.dart';

class SetName extends StatelessWidget {
  final FocusNode currFocus;
  final FocusNode nextFocus;
  final TextEditingController nameC;
  late String name;

  SetName({
    Key? key,
    required this.currFocus,
    required this.nextFocus,
    required this.nameC,
    required this.name,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(top: 40),
      child: TextFormField(
        focusNode: currFocus,
        textInputAction: TextInputAction.next,
        controller: nameC,
        style: Theme.of(context).textTheme.titleMedium?.copyWith(
              color: UiUtils.getColorScheme(context).primaryContainer,
            ),
        validator: (val) => Validators.nameValidation(val!, context),
       
        onFieldSubmitted: (v) {
          fieldFocusChange(context, currFocus, nextFocus);
        },
        decoration: InputDecoration(
          hintText: UiUtils.getTranslatedLabel(context, 'nameLbl'),
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
