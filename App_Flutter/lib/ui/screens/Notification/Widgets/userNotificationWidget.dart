// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/cubits/UserNotification/deleteUserNotification.dart';
import 'package:news/ui/screens/Notification/Widgets/shimmerNotification.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/cubits/UserNotification/userNotificationCubit.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';
import 'package:news/ui/screens/Notification/Widgets/UserNotiList.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/ui/widgets/errorContainerWidget.dart';
import 'package:news/utils/ErrorMessageKeys.dart';
import 'package:news/utils/uiUtils.dart';

class UserNotificationWidget extends StatefulWidget {
  const UserNotificationWidget({
    Key? key,
  }) : super(key: key);

  @override
  State<StatefulWidget> createState() {
    return _UserNotificationState();
  }
}

class _UserNotificationState extends State<UserNotificationWidget> {
  List<String> selectedList = [];

  late final ScrollController userController = ScrollController()..addListener(hasMoreUserNotiScrollListener);

  @override
  void initState() {
    super.initState();
  }

  @override
  void dispose() {
    userController.dispose();
    super.dispose();
  }

  void refreshUserNotification() {
    context.read<UserNotificationCubit>().getUserNotification(context: context, userId: context.read<AuthCubit>().getUserId());
  }

  void hasMoreUserNotiScrollListener() {
    if (userController.position.maxScrollExtent == userController.offset) {
      if (context.read<UserNotificationCubit>().hasMoreUserNotification()) {
        context.read<UserNotificationCubit>().getMoreUserNotification(context: context, userId: context.read<AuthCubit>().getUserId());
      } else {
        debugPrint("No more user notification");
      }
    }
  }

  Widget slideLeftBackground() {
    return Padding(
      padding: const EdgeInsetsDirectional.only(
        top: 5.0,
        bottom: 10.0,
      ),
      child: Container(
        decoration: BoxDecoration(color: Theme.of(context).primaryColor, borderRadius: BorderRadius.circular(10)),
        child: const Align(
          alignment: Alignment.centerRight,
          child: Padding(
            padding: EdgeInsets.all(8.0),
            child: Icon(
              Icons.delete,
              color: Colors.white,
            ),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<UserNotificationCubit, UserNotificationState>(
      builder: (context, state) {
        if (state is UserNotificationFetchSuccess) {
          return RefreshIndicator(
              onRefresh: () async {
                refreshUserNotification();
              },
              child: Padding(
                  padding: const EdgeInsetsDirectional.only(start: 15.0, end: 15.0, bottom: 10.0),
                  child: Column(children: <Widget>[
                    if (selectedList.isNotEmpty)
                      BlocConsumer<DeleteUserNotiCubit, DeleteUserNotiState>(
                          bloc: context.read<DeleteUserNotiCubit>(),
                          listener: (context, state) {
                            if (state is DeleteUserNotiSuccess) {
                              context.read<UserNotificationCubit>().deleteUserNoti(selectedList.join(','));
                              selectedList.clear();
                            }
                          },
                          builder: (context, state) {
                            return Align(
                              alignment: Alignment.topRight,
                              child: IconButton(
                                icon: const Icon(Icons.delete),
                                onPressed: () {
                                  context.read<DeleteUserNotiCubit>().setDeleteUserNoti(id: selectedList.join(','));
                                },
                              ),
                            );
                          }),
                    Expanded(
                        child: ListView.builder(
                      controller: userController,
                      itemCount: state.userUserNotification.length,
                      physics: const AlwaysScrollableScrollPhysics(),
                      itemBuilder: (context, index) {
                        if (index == state.userUserNotification.length - 1 && index != 0) {
                          //check if hasMore
                          if (state.hasMore) {
                            if (state.hasMoreFetchError) {
                              return Center(
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(horizontal: 15.0, vertical: 8.0),
                                  child: IconButton(
                                      onPressed: () {
                                        context.read<UserNotificationCubit>().getMoreUserNotification(context: context, userId: context.read<AuthCubit>().getUserId());
                                      },
                                      icon: Icon(
                                        Icons.error,
                                        color: Theme.of(context).primaryColor,
                                      )),
                                ),
                              );
                            } else {
                              return Center(child: Padding(padding: const EdgeInsets.symmetric(horizontal: 15.0, vertical: 8.0), child: showCircularProgress(true, Theme.of(context).primaryColor)));
                            }
                          }
                        }
                        return BlocConsumer<DeleteUserNotiCubit, DeleteUserNotiState>(
                            bloc: context.read<DeleteUserNotiCubit>(),
                            listener: (context, deleteState) {
                              if (deleteState is DeleteUserNotiSuccess) {
                                context.read<UserNotificationCubit>().deleteUserNoti(state.userUserNotification[index].id!);
                              }
                            },
                            builder: (context, deleteState) {
                              return Dismissible(
                                key: UniqueKey(),
                                direction: DismissDirection.endToStart,
                                onDismissed: (direction) {
                                  context.read<DeleteUserNotiCubit>().setDeleteUserNoti(id: state.userUserNotification[index].id!);
                                },
                                background: slideLeftBackground(),
                                secondaryBackground: slideLeftBackground(),
                                child: ListItemNoti(
                                    userNoti: state.userUserNotification[index],
                                    isSelected: (bool value) {
                                      setState(() {
                                        if (value) {
                                          selectedList.add(state.userUserNotification[index].id!);
                                        } else {
                                          selectedList.remove(state.userUserNotification[index].id!);
                                        }
                                      });
                                    },
                                    key: Key(state.userUserNotification[index].id.toString())),
                              );
                            });
                      },
                    ))
                  ])));
        }
        if (state is UserNotificationFetchFailure) {
          if (state.errorMessage.contains(ErrorMessageKeys.noInternet)) {
            return ErrorContainerWidget(errorMsg: UiUtils.getTranslatedLabel(context, 'internetmsg'), onRetry: refreshUserNotification);
          } else {
            return const Center(
                child: CustomTextLabel(
              text: 'notiNotAvail',
              textAlign: TextAlign.center,
            ));
          }
        }
        //state is UserNotificationFetchInProgress || state is UserNotificationInitial
        return Padding(padding: const EdgeInsets.only(bottom: 10.0, left: 10.0, right: 10.0), child: shimmerNotification(context));
      },
    );
  }
}
