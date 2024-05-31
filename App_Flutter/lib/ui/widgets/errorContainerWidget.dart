// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:lottie/lottie.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/utils/uiUtils.dart';

class ErrorContainerWidget extends StatelessWidget {
  final String errorMsg;
  final Function onRetry;
  const ErrorContainerWidget({Key? key, required this.errorMsg, required this.onRetry}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Align(
        alignment: Alignment.center,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          mainAxisSize: MainAxisSize.min,
          children: [
            (errorMsg.contains(UiUtils.getTranslatedLabel(context, 'internetmsg')))
                ? Container(
                    width: MediaQuery.of(context).size.width * (0.7),
                    margin: EdgeInsets.only(top: MediaQuery.of(context).padding.top + MediaQuery.of(context).size.height * (0.05)),
                    height: MediaQuery.of(context).size.height * (0.4),
                    child: Lottie.asset("assets/animations/noInternet.json"),
                  )
                : SizedBox(
                    height: MediaQuery.of(context).size.height * (0.2),
                  ),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 10),
              child: Text(
                errorMsg,
                textAlign: TextAlign.center,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(color: Theme.of(context).colorScheme.primaryContainer, fontSize: 16, fontWeight: FontWeight.w300),
              ),
            ),
            SizedBox(
              height: MediaQuery.of(context).size.height * (0.035),
            ),
            TextButton(
                onPressed: () {
                  onRetry.call();
                },
                style: ButtonStyle(
                    backgroundColor: MaterialStateProperty.all(UiUtils.getColorScheme(context).primary),
                    shape: MaterialStateProperty.all(RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)))),
                child: CustomTextLabel(
                  text: 'RetryLbl',
                  textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(color: UiUtils.getColorScheme(context).background, fontWeight: FontWeight.w600, fontSize: 21, letterSpacing: 0.6),
                )),
            SizedBox(
              height: MediaQuery.of(context).size.height * (0.15),
            ),
          ],
        ));
  }
}
