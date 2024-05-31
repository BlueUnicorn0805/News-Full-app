// ignore_for_file: file_names

import 'package:flutter/material.dart';

import '../styles/colors.dart';

class CustomTextButton extends StatelessWidget {
  final Function onTap;
  final Color color;
  final String text;

  const CustomTextButton({Key? key, required this.onTap, required this.color, required this.text}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return TextButton(
      style: ButtonStyle(
        overlayColor: MaterialStateProperty.all(Colors.transparent),
        foregroundColor: MaterialStateProperty.all(darkSecondaryColor),
      ),
      onPressed: () {
        onTap();
      },
      child: Text(text, style: Theme.of(context).textTheme.bodyMedium!.copyWith(color: color, fontWeight: FontWeight.normal)),
    );
  }
}
