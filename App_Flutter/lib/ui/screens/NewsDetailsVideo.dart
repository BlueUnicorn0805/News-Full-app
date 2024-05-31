// ignore_for_file: must_be_immutable, file_names, prefer_typing_uninitialized_variables

import 'package:flutter/material.dart';
import 'package:flutter_inappwebview/flutter_inappwebview.dart';

import '../../utils/internetConnectivity.dart';

class NewsDetailsVideo extends StatefulWidget {
  String? src;
  String type;

  NewsDetailsVideo({Key? key, this.src, required this.type}) : super(key: key);

  @override
  State<StatefulWidget> createState() => StateNewsDetailsVideo();
}

class StateNewsDetailsVideo extends State<NewsDetailsVideo> {
  final GlobalKey<ScaffoldState> _scaffoldKey = GlobalKey<ScaffoldState>();
  bool _isNetworkAvail = true;
  var iframe;

  @override
  void initState() {
    super.initState();

    checkNetwork();
    if ((widget.type == "1") || (widget.type == "3")) {
      iframe = '''
        <html>
          <iframe src="${widget.src!}" width="100%" height="100%" allowfullscreen="allowfullscreen"></iframe>
        </html>
        ''';
    } else {
      iframe = '''
        <html>
        <video controls="controls" width="100%" height="100%">
        <source src="${widget.src!}"></video>
        </html>
        ''';
    }
  }

  checkNetwork() async {
    if (await InternetConnectivity.isNetworkAvailable()) {
      setState(() {
        _isNetworkAvail = true;
      });
    } else {
      setState(() {
        _isNetworkAvail = false;
      });
    }
  }

  @override
  void dispose() {
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Scaffold(
        key: _scaffoldKey,
        body: _isNetworkAvail ? viewVideo() : const SizedBox.shrink(),
      ),
    );
  }

  //news video link set
  viewVideo() {
    Uri frm;
    frm = Uri.dataFromString(
      iframe,
      mimeType: 'text/html',
    );
    return Center(
      child: InAppWebView(
        initialUrlRequest: URLRequest(url: frm),
      ),
    );
  }
}
