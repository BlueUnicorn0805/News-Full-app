// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';

Widget sectionShimmer(BuildContext context) {
  return Shimmer.fromColors(
      baseColor: Colors.grey.withOpacity(0.6),
      highlightColor: Colors.grey,
      child: ListView(shrinkWrap: true, physics: const NeverScrollableScrollPhysics(), padding: const EdgeInsetsDirectional.only(top: 10), children: [
        Container(
          height: 55,
          width: double.maxFinite,
          decoration: BoxDecoration(color: Colors.grey.withOpacity(0.6), borderRadius: BorderRadius.circular(10)),
        ),
        ListView.builder(
            padding: EdgeInsets.zero,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: 5,
            itemBuilder: (context, index) {
              return Container(
                alignment: Alignment.center,
                height: MediaQuery.of(context).size.height / 3.3,
                width: MediaQuery.of(context).size.width,
                margin: const EdgeInsets.only(top: 15),
                decoration: BoxDecoration(borderRadius: BorderRadius.circular(15), color: Colors.grey.withOpacity(0.6)),
              );
            }),
      ]));
}
