// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:news/ui/styles/colors.dart';
import 'package:news/ui/widgets/networkImage.dart';
import '../../../../app/routes.dart';
import '../../../../data/models/BreakingNewsModel.dart';
import '../../../../data/models/NewsModel.dart';

class ImageView extends StatefulWidget {
  final NewsModel? model;
  final BreakingNewsModel? breakModel;
  final bool isFromBreak;

  const ImageView({Key? key, this.model, this.breakModel, required this.isFromBreak}) : super(key: key);

  @override
  ImageViewState createState() => ImageViewState();
}

class ImageViewState extends State<ImageView> {
  List<String> allImage = [];
  int _curSlider = 0;

  @override
  void initState() {
    allImage.clear();
    if (!widget.isFromBreak) {
      allImage.add(widget.model!.image!);
      if (widget.model!.imageDataList!.isNotEmpty) {
        for (int i = 0; i < widget.model!.imageDataList!.length; i++) {
          allImage.add(widget.model!.imageDataList![i].otherImage!);
        }
      }
    } else {
      allImage.add(widget.breakModel!.image!);
    }
    super.initState();
  }

  Widget imageView() {
    return SizedBox(
        height: MediaQuery.of(context).size.height * 0.40,
        width: double.maxFinite,
        child: !widget.isFromBreak
            ? PageView.builder(
                itemCount: allImage.length,
                onPageChanged: (index) {
                  setState(() {
                    _curSlider = index;
                  });
                },
                itemBuilder: (BuildContext context, int index) {
                  return InkWell(
                    child: CustomNetworkImage(
                      networkImageUrl: allImage[index],
                      width: double.infinity,
                      height: MediaQuery.of(context).size.height * 0.42,
                      isVideo: false,
                      fit: BoxFit.cover,
                    ),
                    onTap: () {
                      Navigator.of(context).pushNamed(Routes.imagePreview, arguments: {"index": index, "imgList": allImage});
                    },
                  );
                })
            : CustomNetworkImage(
                networkImageUrl: widget.breakModel!.image!,
                width: double.infinity,
                height: MediaQuery.of(context).size.height * 0.42,
                isVideo: false,
                fit: BoxFit.cover,
              ));
  }

  List<T> map<T>(List list, Function handler) {
    List<T> result = [];
    for (var i = 0; i < list.length; i++) {
      result.add(handler(i, list[i]));
    }

    return result;
  }

  Widget imageSliderDot() {
    if (!widget.isFromBreak && allImage.length > 1) {
      return Align(
          alignment: Alignment.bottomCenter,
          child: Container(
              margin: EdgeInsets.only(top: MediaQuery.of(context).size.height / 2.6 - 23),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: map<Widget>(
                  allImage,
                  (index, url) {
                    return Container(
                        width: _curSlider == index ? 10.0 : 8.0,
                        height: _curSlider == index ? 10.0 : 8.0,
                        margin: const EdgeInsets.symmetric(horizontal: 1.0),
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: _curSlider == index ? secondaryColor : secondaryColor.withOpacity((0.5)),
                        ));
                  },
                ),
              )));
    } else {
      return const SizedBox.shrink();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [imageView(), imageSliderDot()],
    );
  }
}
