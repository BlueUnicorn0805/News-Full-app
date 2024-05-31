// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';

catShimmer() {
  return Shimmer.fromColors(
      baseColor: Colors.grey.withOpacity(0.4),
      highlightColor: Colors.grey.withOpacity(0.4),
      child: SingleChildScrollView(
        padding: const EdgeInsets.only(top: 10),
        scrollDirection: Axis.horizontal,
        child: Row(
            children: [0, 1, 2, 3, 4, 5, 6]
                .map((i) => Padding(
                    padding: const EdgeInsetsDirectional.only(start: 15, top: 0),
                    child: Container(
                      decoration: BoxDecoration(borderRadius: BorderRadius.circular(5.0), color: const Color.fromARGB(255, 59, 49, 49)),
                      height: 32.0,
                      width: 70.0,
                    )))
                .toList()),
      ));
}
