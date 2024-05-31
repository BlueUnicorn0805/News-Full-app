// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_widget_from_html/flutter_widget_from_html.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../NewsDetailsVideo.dart';

Widget descView({required String desc, required double fontValue, required BuildContext context}) {
  return Padding(
      padding: const EdgeInsets.only(top: 5.0),
      child: HtmlWidget(
        desc,

        onTapUrl: (String? url) async {
          if (await canLaunchUrl(Uri.parse(url!))) {
            await launchUrl(Uri.parse(url));
            return true;
          } else {
            throw 'Could not launch $url';
          }
        },
        onErrorBuilder: (context, element, error) => Text('$element error: $error'),
        onLoadingBuilder: (context, element, loadingProgress) => showCircularProgress(true, Theme.of(context).primaryColor),

        renderMode: RenderMode.column,

        // set the default styling for text
        textStyle: TextStyle(fontSize: fontValue.toDouble()),
        customWidgetBuilder: (element) {
          if ((element.toString() == "<html iframe>") || (element.toString() == "<html video>")) {
            return FittedBox(
              fit: BoxFit.fill,
              child: Container(
                  height: 220,
                  width: MediaQuery.of(context).size.width,
                  color: Colors.transparent,
                  child: (element.toString() == "<html iframe>")
                      ? NewsDetailsVideo(
                          src: element.attributes["src"],
                          type: "1",
                        )
                      : NewsDetailsVideo(
                          type: "2",
                          src: element.outerHtml,
                        )),
            );
          }

          return null;
        },
      ));
}
