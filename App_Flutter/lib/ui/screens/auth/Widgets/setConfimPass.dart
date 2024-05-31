// ignore_for_file: file_names, must_be_immutable

import 'package:flutter/material.dart';
import 'package:news/utils/uiUtils.dart';

class SetConfirmPass extends StatefulWidget {
  final FocusNode currFocus;
  final TextEditingController confPassC;
  late String confPass;
  late String pass;

  SetConfirmPass({
    Key? key,
    required this.currFocus,
    required this.confPassC,
    required this.confPass,
    required this.pass,
  }) : super(key: key);

  @override
  State<StatefulWidget> createState() {
    return _SetConfPassState();
  }
}

class _SetConfPassState extends State<SetConfirmPass> {
  bool isObscure = true;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(top: 20),
      child: TextFormField(
        focusNode: widget.currFocus,
        textInputAction: TextInputAction.done,
        controller: widget.confPassC,
        obscureText: isObscure,
        style: Theme.of(context).textTheme.titleMedium?.copyWith(
              color: UiUtils.getColorScheme(context).primaryContainer,
            ),
        validator: (value) {
          if (value!.isEmpty) {
            return UiUtils.getTranslatedLabel(context, 'confPassRequired');
          }
          if (value.trim() != widget.pass.trim()) {
            return UiUtils.getTranslatedLabel(context, 'confPassNotMatch');
          } else {
            return null;
          }
        },
        onChanged: (String value) {
          widget.confPass = value;
        },
        decoration: InputDecoration(
          hintText: UiUtils.getTranslatedLabel(context, 'confpassLbl'),
          hintStyle: Theme.of(context).textTheme.titleMedium?.copyWith(
                color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.5),
              ),
          suffixIcon: Padding(
              padding: const EdgeInsetsDirectional.only(end: 12.0),
              child: IconButton(
                icon: isObscure ? const Icon(Icons.visibility_rounded, size: 20) : const Icon(Icons.visibility_off_rounded, size: 20),
                splashColor: Colors.transparent,
                onPressed: () {
                  setState(() {
                    isObscure = !isObscure;
                  });
                },
              )),
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
