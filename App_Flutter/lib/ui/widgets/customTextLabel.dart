// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/cubits/languageJsonCubit.dart';

class CustomTextLabel extends StatelessWidget {
  final String text;
  final TextStyle? textStyle;
  final TextAlign? textAlign;
  final int? maxLines;
  final TextOverflow? overflow;
  final bool? softWrap;

  const CustomTextLabel({Key? key, required this.text, this.textStyle, this.textAlign, this.maxLines, this.overflow, this.softWrap}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<LanguageJsonCubit, LanguageJsonState>(
      builder: (context, state) {
        return Text(
          context.read<LanguageJsonCubit>().getTranslatedLabels(text),
          style: textStyle,
          textAlign: textAlign,
        );
      },
    );
  }
}
