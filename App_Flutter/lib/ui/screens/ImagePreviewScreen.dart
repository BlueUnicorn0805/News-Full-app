// ignore: must_be_immutable
// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:photo_view/photo_view.dart';

class ImagePreview extends StatefulWidget {
  final int index;
  final List<String> imgList;

  const ImagePreview({Key? key, required this.index, required this.imgList}) : super(key: key);

  @override
  State<StatefulWidget> createState() => StatePreview();

  static Route route(RouteSettings routeSettings) {
    final arguments = routeSettings.arguments as Map<String, dynamic>;
    return CupertinoPageRoute(
        builder: (_) => ImagePreview(
              index: arguments['index'],
              imgList: arguments['imgList'],
            ));
  }
}

class StatePreview extends State<ImagePreview> {
  final GlobalKey<ScaffoldState> _scaffoldKey = GlobalKey<ScaffoldState>();
  int? curPos;

  @override
  void initState() {
    super.initState();

    curPos = widget.index;
  }

  @override
  void dispose() {
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        key: _scaffoldKey,
        body: Stack(
          children: <Widget>[
            PageView.builder(
                itemCount: widget.imgList.length,
                controller: PageController(initialPage: curPos!),
                onPageChanged: (index) async {
                  setState(() {
                    curPos = index;
                  });
                },
                itemBuilder: (BuildContext context, int index) {
                  return PhotoView(
                      backgroundDecoration: BoxDecoration(color: Theme.of(context).colorScheme.secondary),
                      initialScale: PhotoViewComputedScale.contained * 0.9,
                      minScale: PhotoViewComputedScale.contained * 0.9,
                      imageProvider: NetworkImage(widget.imgList[index]));
                }),
            Positioned.directional(
                top: 35,
                start: 20.0,
                textDirection: Directionality.of(context),
                child: InkWell(
                  onTap: () {
                    Navigator.of(context).pop();
                  },
                  child: ClipRRect(
                      borderRadius: BorderRadius.circular(22.0),
                      child: Container(
                          height: 35,
                          width: 35,
                          alignment: Alignment.center,
                          decoration: BoxDecoration(color: UiUtils.getColorScheme(context).primaryContainer, shape: BoxShape.circle),
                          child: Icon(
                            Icons.keyboard_backspace_rounded,
                            color: UiUtils.getColorScheme(context).background,
                          ))),
                )),
            Positioned(
                bottom: 10.0,
                left: 25.0,
                right: 25.0,
                child: SelectedPhoto(
                  numberOfDots: widget.imgList.length,
                  photoIndex: curPos!,
                ))
          ],
        ));
  }
}

class SelectedPhoto extends StatelessWidget {
  final int? numberOfDots;
  final int? photoIndex;

  const SelectedPhoto({super.key, this.numberOfDots, this.photoIndex});

  Widget _inactivePhoto(BuildContext context) {
    return Padding(
      padding: const EdgeInsetsDirectional.only(start: 3.0, end: 3.0),
      child: Container(
        height: 8.0,
        width: 8.0,
        decoration: BoxDecoration(color: Theme.of(context).primaryColor.withOpacity(0.4), borderRadius: BorderRadius.circular(4.0)),
      ),
    );
  }

  Widget _activePhoto(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(left: 5.0, right: 5.0),
      child: Container(
        height: 10.0,
        width: 10.0,
        decoration:
            BoxDecoration(color: Theme.of(context).primaryColor, borderRadius: BorderRadius.circular(10.0), boxShadow: const [BoxShadow(color: Colors.grey, spreadRadius: 0.0, blurRadius: 2.0)]),
      ),
    );
  }

  List<Widget> _buildDots(BuildContext context) {
    List<Widget> dots = [];
    for (int i = 0; i < numberOfDots!; i++) {
      dots.add(i == photoIndex ? _activePhoto(context) : _inactivePhoto(context));
    }
    return dots;
  }

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: _buildDots(context),
      ),
    );
  }
}
