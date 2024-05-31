// ignore_for_file: file_names

import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:news/ui/styles/colors.dart';

Widget backBtn(BuildContext context, bool fromShowMore) {
  return Positioned.directional(
      textDirection: Directionality.of(context),
      top: 35,
      start: 20.0,
      child: InkWell(
        child: ClipRRect(
            borderRadius: BorderRadius.circular(52.0),
            child: BackdropFilter(
                filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
                child: Container(
                    alignment: Alignment.center,
                    height: 39,
                    width: 39,
                    decoration: const BoxDecoration(color: secondaryColor, shape: BoxShape.circle),
                    child: const Icon(Icons.keyboard_backspace_rounded, color: darkSecondaryColor)))),
        onTap: () {
          (fromShowMore == true) ? Navigator.of(context).popUntil((route) => route.isFirst) : Navigator.pop(context);
        },
      ));
}
