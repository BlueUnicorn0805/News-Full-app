// ignore_for_file: override_on_non_overriding_member, file_names

import 'package:flutter/material.dart';
import 'package:news/utils/uiUtils.dart';
import '../../../../data/models/NotificationModel.dart';

class ListItemNoti extends StatefulWidget {
  @override
  final NotificationModel? userNoti;
  final ValueChanged<bool>? isSelected;

  const ListItemNoti({super.key, this.userNoti, this.isSelected});

  @override
  ListItemNotiState createState() => ListItemNotiState();
}

class ListItemNotiState extends State<ListItemNoti> {
  bool isSelected = false;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () {
        setState(() {
          isSelected = !isSelected;
          widget.isSelected!(isSelected);
        });
      },
      child: listItem1(),
    );
  }

  //list of notification shown
  Widget listItem1() {
    DateTime time1 = DateTime.parse(widget.userNoti!.date!);
    return Padding(
      padding: const EdgeInsetsDirectional.only(
        top: 5.0,
        bottom: 10.0,
      ),
      child: Container(
        decoration: BoxDecoration(color: UiUtils.getColorScheme(context).background, borderRadius: BorderRadius.circular(10)),
        child: Padding(
          padding: const EdgeInsets.all(10.0),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: <Widget>[
              if (isSelected)
                Icon(
                  Icons.check_circle,
                  color: Theme.of(context).primaryColor,
                  size: 22,
                ),
              Padding(
                padding: const EdgeInsetsDirectional.only(start: 10.0),
                child: widget.userNoti!.type == "comment" ? Icon(Icons.message, color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.8)) : const Icon(Icons.thumb_up_alt),
              ),
              Expanded(
                  child: Padding(
                padding: const EdgeInsetsDirectional.only(start: 13.0, end: 8.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: <Widget>[
                    Text(widget.userNoti!.message!,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: Theme.of(context)
                            .textTheme
                            .titleMedium
                            ?.copyWith(fontWeight: FontWeight.bold, color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.9), fontSize: 15.0, letterSpacing: 0.1)),
                    Padding(
                        padding: const EdgeInsetsDirectional.only(top: 8.0),
                        child: Text(UiUtils.convertToAgo(context, time1, 2)!,
                            style:
                                Theme.of(context).textTheme.bodySmall?.copyWith(fontWeight: FontWeight.normal, color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7), fontSize: 11)))
                  ],
                ),
              )),
            ],
          ),
        ),
      ),
    );
  }
}
