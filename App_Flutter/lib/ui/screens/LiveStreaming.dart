// ignore_for_file: must_be_immutable, file_names

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:news/ui/styles/colors.dart';
import 'package:news/ui/widgets/networkImage.dart';

import '../../app/routes.dart';
import '../../data/models/LiveStreamingModel.dart';
import '../widgets/customAppBar.dart';

class LiveStreaming extends StatefulWidget {
  List<LiveStreamingModel> liveNews;

  LiveStreaming({Key? key, required this.liveNews}) : super(key: key);

  @override
  State<StatefulWidget> createState() => StateLive();

  static Route route(RouteSettings routeSettings) {
    final arguments = routeSettings.arguments as Map<String, dynamic>;
    return CupertinoPageRoute(
        builder: (_) => LiveStreaming(
              liveNews: arguments['liveNews'],
            ));
  }
}

class StateLive extends State<LiveStreaming> {
  @override
  void initState() {
    super.initState();
  }

  @override
  void dispose() {
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(appBar: setCustomAppBar(height: 45, isBackBtn: true, label: 'liveVideosLbl', context: context, horizontalPad: 15, isConvertText: true), body: mainListBuilder());
  }

  mainListBuilder() {
    return Padding(
      padding: const EdgeInsets.all(20),
      child: ListView.separated(
          itemBuilder: ((context, index) {
            return Padding(
              padding: EdgeInsets.only(top: index == 0 ? 0 : 20),
              child: ClipRRect(
                borderRadius: const BorderRadius.all(Radius.circular(10.0)),
                child: InkWell(
                  onTap: () {
                    Navigator.of(context).pushNamed(Routes.newsVideo, arguments: {"from": 2, "liveModel": widget.liveNews[index]});
                  },
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      CustomNetworkImage(
                        networkImageUrl: widget.liveNews[index].image!,
                        height: 200,
                        fit: BoxFit.cover,
                        isVideo: true,
                        width: double.infinity,
                      ),
                      const CircleAvatar(
                          radius: 30,
                          backgroundColor: Colors.black45,
                          child: Icon(
                            Icons.play_arrow,
                            size: 40,
                            color: Colors.white,
                          )),
                      Positioned.directional(
                        textDirection: Directionality.of(context),
                        bottom: 10,
                        start: 20,
                        end: 20,
                        child: Text(
                          widget.liveNews[index].title!,
                          style: Theme.of(context).textTheme.titleSmall!.copyWith(color: secondaryColor),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      )
                    ],
                  ),
                ),
              ),
            );
          }),
          separatorBuilder: (context, index) {
            return const SizedBox(height: 3.0);
          },
          itemCount: widget.liveNews.length),
    );
  }
}
