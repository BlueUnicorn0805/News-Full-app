// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import 'package:news/utils/constant.dart';
import 'package:news/utils/uiUtils.dart';

class CustomNetworkImage extends StatelessWidget {
  final String networkImageUrl;
  final double? width, height;
  final BoxFit? fit;
  final bool? isVideo;
  final Widget? errorBuilder;

  const CustomNetworkImage({Key? key, required this.networkImageUrl, this.width, this.height, this.fit, this.isVideo, this.errorBuilder}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return FadeInImage(
        fadeInDuration: const Duration(milliseconds: 150),
        image: NetworkImage(networkImageUrl),
        width: width ?? 100,
        height: height ?? 100,
        fit: fit ?? BoxFit.cover,
        placeholderFit: BoxFit.cover,
        placeholder: ExactAssetImage(UiUtils.getSvgImagePath((isVideo!) ? "placeholder_video" : "placeholder"), package: packageName),
        imageErrorBuilder: ((context, error, stackTrace) {
          return (errorBuilder != null)
              ? errorBuilder!
              : SvgPicture.asset(UiUtils.getSvgImagePath((isVideo!) ? "placeholder_video" : "placeholder"), width: width ?? 100, height: height ?? 100, fit: fit ?? BoxFit.contain);
        }),
        placeholderErrorBuilder: ((context, error, stackTrace) {
          return SvgPicture.asset(UiUtils.getSvgImagePath((isVideo!) ? "placeholder_video" : "placeholder"), width: width ?? 100, height: height ?? 100, fit: fit ?? BoxFit.contain);
        }));
  }
}
