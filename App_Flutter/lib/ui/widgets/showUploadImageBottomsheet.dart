// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/utils/uiUtils.dart';

showUploadImageBottomsheet({required BuildContext context, required VoidCallback? onCamera, required VoidCallback? onGallery}) {
  return showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(side: BorderSide(color: Colors.transparent), borderRadius: BorderRadius.only(topLeft: Radius.circular(10), topRight: Radius.circular(10))),
      builder: (BuildContext bc) {
        return SafeArea(
          child: Wrap(
            children: <Widget>[
              ListTile(
                leading: Icon(
                  Icons.photo_library,
                  color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7),
                ),
                title: Text(
                  UiUtils.getTranslatedLabel(context, 'photoLibLbl'),
                  style: TextStyle(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7), fontSize: 18, fontWeight: FontWeight.bold),
                ),
                onTap: onGallery,
              ),
              ListTile(
                  leading: Icon(
                    Icons.photo_camera,
                    color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7),
                  ),
                  title: Text(
                    UiUtils.getTranslatedLabel(context, 'cameraLbl'),
                    style: TextStyle(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7), fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                  onTap: onCamera),
            ],
          ),
        );
      });
}
