// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';

videoShimmer(BuildContext context) {
  return Shimmer.fromColors(
      baseColor: Colors.grey.withOpacity(0.6),
      highlightColor: Colors.grey,
      child: SingleChildScrollView(
        padding: const EdgeInsetsDirectional.only(top: 15.0),
        child: ListView.builder(
            shrinkWrap: true,
            physics: const AlwaysScrollableScrollPhysics(),
            itemBuilder: (_, i) => Padding(
                padding: EdgeInsetsDirectional.only(top: i == 0 ? 0 : 15.0),
                child: Stack(children: [Container(decoration: BoxDecoration(borderRadius: BorderRadius.circular(10.0), color: Colors.grey.withOpacity(0.6)), height: 215.0)])),
            itemCount: 6),
      ));
}
