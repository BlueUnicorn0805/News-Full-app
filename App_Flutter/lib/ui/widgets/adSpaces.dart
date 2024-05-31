// ignore_for_file: must_be_immutable, file_names
//sponsored Ads

import 'package:flutter/material.dart';
import 'package:news/data/models/adSpaceModel.dart';
import 'package:news/ui/widgets/networkImage.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:url_launcher/url_launcher.dart';

class AdSpaces extends StatelessWidget {
  AdSpaceModel adsModel;
  AdSpaces({Key? key, required this.adsModel}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(top: 10),
      child: InkWell(
          splashColor: Colors.transparent,
          onTap: () async {
            if (await canLaunchUrl(Uri.parse(adsModel.adUrl!))) {
              await launchUrl(Uri.parse(adsModel.adUrl!));
            }
          },
          child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, mainAxisAlignment: MainAxisAlignment.start, children: [
            Container(
              alignment: AlignmentDirectional.centerEnd,
              padding: const EdgeInsetsDirectional.only(end: 5),
              child: Text(
                UiUtils.getTranslatedLabel(context, 'sponsoredLbl'),
                style: TextStyle(fontWeight: FontWeight.w800, color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.6)),
              ),
            ),
            Padding(
              padding: const EdgeInsets.only(top: 2),
              child: CustomNetworkImage(
                networkImageUrl: adsModel.adImage!,
                isVideo: false,
                width: MediaQuery.of(context).size.width,
                fit: BoxFit.values.first,
              ),
            ),
          ])),
    );
  }
}
