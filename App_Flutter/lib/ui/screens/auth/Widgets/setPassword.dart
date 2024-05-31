// ignore_for_file: must_be_immutable, file_names

import 'package:flutter/material.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/utils/validators.dart';

import 'fieldFocusChange.dart';

class SetPassword extends StatefulWidget {
  final FocusNode currFocus;
  final FocusNode? nextFocus;
  final TextEditingController passC;
  late String pass;
  final double topPad;
  final bool isLogin;

  SetPassword({
    Key? key,
    required this.currFocus,
    this.nextFocus,
    required this.passC,
    required this.pass,
    required this.topPad,
    required this.isLogin,
  }) : super(key: key);

  @override
  State<StatefulWidget> createState() {
    return _SetPassState();
  }
}

class _SetPassState extends State<SetPassword> {
  bool isObscure = true;

  @override
  Widget build(BuildContext context) {
    return StatefulBuilder(builder: (context, StateSetter setStater) {
      return Padding(
        padding: EdgeInsets.only(top: widget.topPad),
        child: TextFormField(
          focusNode: widget.currFocus,
          textInputAction: widget.isLogin ? TextInputAction.done : TextInputAction.next,
          controller: widget.passC,
          obscureText: isObscure,
          style: Theme.of(context).textTheme.titleMedium?.copyWith(
                color: UiUtils.getColorScheme(context).primaryContainer,
              ),
          validator: (val) => Validators.passValidation(val!, context),
          onFieldSubmitted: (v) {
            if (!widget.isLogin) {
              fieldFocusChange(context, widget.currFocus, widget.nextFocus!);
            }
          },
          onChanged: (String value) {
            widget.pass = value;
            setStater(() {});
          },
          decoration: InputDecoration(
            hintText: UiUtils.getTranslatedLabel(context, 'passLbl'),
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
    });
  }
}
