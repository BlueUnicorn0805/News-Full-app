// ignore_for_file: file_names, deprecated_member_use

import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import 'package:news/ui/styles/colors.dart';
import 'package:news/utils/uiUtils.dart';

class BottomCommButton extends StatelessWidget {
  final Function onTap;
  final String img;
  final double startPad;
  final Color? btnColor;

  const BottomCommButton({Key? key, required this.onTap, required this.img, required this.startPad, this.btnColor}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Padding(
        padding: EdgeInsetsDirectional.only(start: startPad),
        child: InkWell(
            splashColor: Colors.transparent,
            child: Container(
                height: 54,
                width: 54,
                padding: const EdgeInsets.all(9.0),
                decoration: BoxDecoration(borderRadius: BorderRadius.circular(30.0), color: secondaryColor),
                child: SvgPicture.asset(UiUtils.getSvgImagePath(img), color: btnColor)),
            onTap: () => onTap()));
  }
}
