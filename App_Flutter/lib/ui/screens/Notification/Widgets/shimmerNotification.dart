// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';

Widget shimmerNotification(BuildContext context) {
  return Container(
    width: double.infinity,
    padding: const EdgeInsetsDirectional.only(start: 15.0, end: 15.0, top: 20.0, bottom: 10.0),
    child: Shimmer.fromColors(
      baseColor: Colors.grey.withOpacity(0.6),
      highlightColor: Colors.grey,
      child: SingleChildScrollView(
        child: Column(
          children: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
              .map((_) => Padding(
                  padding: const EdgeInsetsDirectional.only(
                    top: 5.0,
                    bottom: 10.0,
                  ),
                  child: Container(
                    padding: const EdgeInsets.all(10.0),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(10),
                      color: Colors.grey.withOpacity(0.6),
                    ),
                    child: Row(
                      children: [
                        Container(
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(5.0),
                            color: Colors.grey,
                          ),
                          width: 80.0,
                          height: 80.0,
                        ),
                        Expanded(
                            child: Padding(
                          padding: const EdgeInsetsDirectional.only(start: 13.0, end: 8.0),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Container(
                                width: double.infinity,
                                height: 13.0,
                                color: Colors.grey,
                              ),
                              const Padding(
                                padding: EdgeInsets.symmetric(vertical: 3.0),
                              ),
                              Container(
                                width: double.infinity,
                                height: 13.0,
                                color: Colors.grey,
                              ),
                              const Padding(
                                padding: EdgeInsets.symmetric(vertical: 8.0),
                              ),
                              Container(
                                width: 100,
                                height: 10.0,
                                color: Colors.grey,
                              ),
                            ],
                          ),
                        ))
                      ],
                    ),
                  )))
              .toList(),
        ),
      ),
    ),
  );
}
