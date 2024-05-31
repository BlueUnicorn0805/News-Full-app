// ignore_for_file: file_names, use_build_context_synchronously

import 'dart:io';
import 'package:firebase_auth/firebase_auth.dart' as fAuth;
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:in_app_review/in_app_review.dart';
import 'package:news/app/app.dart';
import 'package:news/app/routes.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/cubits/Auth/deleteUserCubit.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/cubits/otherPagesCubit.dart';
import 'package:news/cubits/settingCubit.dart';
import 'package:news/data/repositories/Auth/authLocalDataSource.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/utils/internetConnectivity.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:share_plus/share_plus.dart';
import 'package:news/cubits/themeCubit.dart';
import 'package:news/utils/constant.dart';
import 'package:news/ui/styles/appTheme.dart';
import 'package:news/ui/widgets/SnackBarWidget.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  ProfileScreenState createState() => ProfileScreenState();

  static Route route(RouteSettings routeSettings) {
    return CupertinoPageRoute(
      builder: (_) => const ProfileScreen(),
    );
  }
}

class ProfileScreenState extends State<ProfileScreen> {
  File? image;
  String? name, mobile, email, profile;
  TextEditingController? nameC, monoC, emailC = TextEditingController();
  AuthLocalDataSource authLocalDataSource = AuthLocalDataSource();
  bool isEditMono = false;
  bool isEditEmail = false;
  String? updateValue;
  final fAuth.FirebaseAuth _auth = fAuth.FirebaseAuth.instance;
  final InAppReview _inAppReview = InAppReview.instance;

  @override
  void initState() {
    getOtherPagesData();
    super.initState();
  }

  getOtherPagesData() {
    Future.delayed(Duration.zero, () {
      context.read<OtherPageCubit>().getOtherPage(context: context, langId: context.read<AppLocalizationCubit>().state.id);
    });
  }

  Widget pagesBuild() {
    return BlocBuilder<OtherPageCubit, OtherPageState>(builder: (context, state) {
      if (state is OtherPageFetchSuccess) {
        return ScrollConfiguration(
          behavior: GlobalScrollBehavior(),
          child: ListView.builder(
              shrinkWrap: true,
              padding: EdgeInsets.zero,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: state.otherPage.length,
              itemBuilder: ((context, index) =>
                  setDrawerItem(state.otherPage[index].title!, Icons.info_rounded, false, true, false, 7, image: state.otherPage[index].image!, desc: state.otherPage[index].pageContent))),
        );
      } else {
        //state is OtherPageFetchInProgress || state is OtherPageInitial || state is OtherPageFetchFailure
        return const SizedBox.shrink();
      }
    });
  }

  switchTheme(bool value) async {
    if (value) {
      if (await InternetConnectivity.isNetworkAvailable()) {
        SystemChrome.setSystemUIOverlayStyle(SystemUiOverlayStyle.light);
        context.read<ThemeCubit>().changeTheme(AppTheme.Dark);
        UiUtils.setUIOverlayStyle(appTheme: AppTheme.Dark);
        //for non-appbar screens
      } else {
        showSnackBar(UiUtils.getTranslatedLabel(context, 'internetmsg'), context);
      }
    } else {
      if (await InternetConnectivity.isNetworkAvailable()) {
        SystemChrome.setSystemUIOverlayStyle(SystemUiOverlayStyle.dark);
        context.read<ThemeCubit>().changeTheme(AppTheme.Light);
        UiUtils.setUIOverlayStyle(appTheme: AppTheme.Light);
        //for non-appbar screens
      } else {
        showSnackBar(UiUtils.getTranslatedLabel(context, 'internetmsg'), context);
      }
    }
  }

  bool getTheme() {
    return (context.read<ThemeCubit>().state.appTheme == AppTheme.Dark) ? true : false;
  }

  bool getNotification() {
    if (context.read<SettingsCubit>().state.settingsModel!.notification == true) {
      return true;
    } else {
      return false;
    }
  }

  switchNotification(bool value) {
    context.read<SettingsCubit>().changeNotification(value);
    setState(() {});
  }

  //set drawer item list press
  Widget setDrawerItem(String title, IconData? icon, bool isTrailing, bool isNavigate, bool isSwitch, int id, {String? image, String? desc}) {
    return ListTile(
      dense: true,
      leading: (image != null && image != "")
          ? Image.network(
              image,
              width: 25,
              height: 25,
              color: UiUtils.getColorScheme(context).primaryContainer,
              errorBuilder: (context, error, stackTrace) {
                return Icon(icon);
              },
            )
          : Icon(icon, size: 25),
      iconColor: UiUtils.getColorScheme(context).primaryContainer,
      trailing: (isTrailing)
          ? SizedBox(
              height: 45,
              width: 55,
              child: FittedBox(
                  fit: BoxFit.fill,
                  child: Switch.adaptive(
                      onChanged: (id == 0) ? switchTheme : switchNotification,
                      value: (id == 0) ? getTheme() : getNotification(),
                      activeColor: Theme.of(context).primaryColor,
                      activeTrackColor: Theme.of(context).primaryColor,
                      inactiveThumbColor: Colors.grey,
                      inactiveTrackColor: Colors.grey)))
          : const SizedBox.shrink(),
      title: CustomTextLabel(text: title, textStyle: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer)),
      onTap: () {
        if (isNavigate) {
          switch (id) {
            case 2:
              Navigator.of(context).pushNamed(Routes.languageList, arguments: {"from": 2});
              break;
            case 3:
              Navigator.of(context).pushNamed(Routes.bookmark);
              break;
            case 4:
              Navigator.of(context).pushNamed(Routes.addNews, arguments: {"isEdit": false, "from": "profile"});
              break;
            case 5:
              Navigator.of(context).pushNamed(Routes.showNews);
              break;
            case 6:
              Navigator.of(context).pushNamed(Routes.managePref, arguments: {"from": 1});
              break;
            case 7:
              Navigator.of(context).pushNamed(Routes.privacy, arguments: {"from": "setting", "title": title, "desc": desc});
              break;
            case 8:
              _openStoreListing();
              break;
            case 9:
              var str = "$appName\n\n${UiUtils.getTranslatedLabel(context, 'shareMsg')}\n\n$androidLbl\n$androidLink$packageName\n\n$iosLbl\n$iosLink";
              Share.share(str);
              break;
            case 10:
              logOutDailog();
              break;
            case 11:
              deleteAccount();
              break;
            default:
              break;
          }
        }
      },
    );
  }

  logOutDailog() async {
    await showDialog(
        context: context,
        builder: (BuildContext context) {
          return StatefulBuilder(builder: (BuildContext context, StateSetter setStater) {
            return AlertDialog(
              backgroundColor: UiUtils.getColorScheme(context).background,
              shape: const RoundedRectangleBorder(borderRadius: BorderRadius.all(Radius.circular(5.0))),
              content: CustomTextLabel(text: 'logoutTxt', textStyle: Theme.of(this.context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer)),
              actions: <Widget>[
                TextButton(
                    child: CustomTextLabel(
                        text: 'noLbl', textStyle: Theme.of(this.context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.bold)),
                    onPressed: () => Navigator.of(context).pop(false)),
                TextButton(
                    child: CustomTextLabel(
                        text: 'yesLbl', textStyle: Theme.of(this.context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.bold)),
                    onPressed: () async {
                      UiUtils.userLogOut(contxt: context);
                    })
              ],
            );
          });
        });
  }

  //set Delete dialogue
  deleteAccount() async {
    await showDialog(
        context: context,
        builder: (BuildContext context) {
          return StatefulBuilder(builder: (BuildContext context, StateSetter setStater) {
            return AlertDialog(
              backgroundColor: UiUtils.getColorScheme(context).background,
              shape: const RoundedRectangleBorder(borderRadius: BorderRadius.all(Radius.circular(5.0))),
              content: CustomTextLabel(
                  text: (_auth.currentUser != null) ? 'deleteConfirm' : 'deleteRelogin',
                  textStyle: Theme.of(this.context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer)),
              title: (_auth.currentUser != null) ? const CustomTextLabel(text: 'deleteAcc') : const CustomTextLabel(text: 'deleteAlertTitle'),
              titleTextStyle: Theme.of(this.context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w800, color: UiUtils.getColorScheme(context).primaryContainer),
              actions: <Widget>[
                TextButton(
                    child: CustomTextLabel(
                        text: (_auth.currentUser != null) ? 'noLbl' : 'cancelBtn',
                        textStyle: Theme.of(this.context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.bold)),
                    onPressed: () {
                      Navigator.of(context).pop(false);
                    }),
                TextButton(
                    child: CustomTextLabel(
                        text: (_auth.currentUser != null) ? 'yesLbl' : 'logoutLbl',
                        textStyle: Theme.of(this.context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.bold)),
                    onPressed: () async {
                      (_auth.currentUser != null) ? proceedToDeleteProfile() : askToLoginAgain();
                    })
              ],
            );
          });
        });
  }

  askToLoginAgain() {
    showSnackBar(UiUtils.getTranslatedLabel(context, 'loginReqMsg'), context);
    Navigator.of(context).pushNamedAndRemoveUntil(Routes.login, (route) => false);
  }

  proceedToDeleteProfile() async {
    //delete user from firebase
    try {
      await _auth.currentUser!.delete().then((value) {
        //delete user prefs from App-local
        context.read<DeleteUserCubit>().setDeleteUser(userId: context.read<AuthCubit>().getUserId(), context: context).then((value) {
          showSnackBar(value["message"], context);
          for (int i = 0; i < AuthProvider.values.length; i++) {
            if (AuthProvider.values[i].name == context.read<AuthCubit>().getType()) {
              context.read<AuthCubit>().signOut(AuthProvider.values[i]).then((value) {
                Navigator.of(context).pushNamedAndRemoveUntil(Routes.login, (route) => false);
              });
            }
          }
        });
      });
    } on fAuth.FirebaseAuthException catch (error) {
      if (error.code == "requires-recent-login") {
        for (int i = 0; i < AuthProvider.values.length; i++) {
          if (AuthProvider.values[i].name == context.read<AuthCubit>().getType()) {
            context.read<AuthCubit>().signOut(AuthProvider.values[i]).then((value) {
              Navigator.of(context).pushNamedAndRemoveUntil(Routes.login, (route) => false);
            });
          }
        }
      } else {
        throw showSnackBar('${error.message}', context);
      }
    } catch (e) {
      debugPrint("unable to delete user - ${e.toString()}");
    }
  }

  Future<void> _openStoreListing() => _inAppReview.openStoreListing(
        appStoreId: appStoreId,
        microsoftStoreId: 'microsoftStoreId',
      );

  Widget setHeader() {
    return BlocBuilder<AuthCubit, AuthState>(builder: (context, authState) {
      if (authState is Authenticated && context.read<AuthCubit>().getUserId() != "0") {
        return Row(crossAxisAlignment: CrossAxisAlignment.start, mainAxisAlignment: MainAxisAlignment.spaceBetween, children: <Widget>[
          Align(
            alignment: Alignment.centerLeft,
            child: CircleAvatar(
              radius: 36,
              backgroundColor: UiUtils.getColorScheme(context).secondaryContainer,
              child: CircleAvatar(
                radius: 34,
                backgroundColor: Colors.transparent,
                child: ClipOval(
                    clipBehavior: Clip.antiAliasWithSaveLayer,
                    child: (authState.authModel.profile != null && authState.authModel.profile.toString().trim().isNotEmpty)
                        ? Image.network(
                            authState.authModel.profile!,
                            fit: BoxFit.fill,
                            width: 80,
                            height: 80,
                            filterQuality: FilterQuality.high,
                            errorBuilder: (context, error, stackTrace) {
                              return const Icon(Icons.person);
                            },
                          )
                        : Icon(Icons.person, color: UiUtils.getColorScheme(context).primaryContainer)),
              ),
            ),
          ),
          SizedBox(
            width: MediaQuery.of(context).size.width * 0.6,
            child: Padding(
              padding: const EdgeInsetsDirectional.only(start: 20),
              child: Column(
                mainAxisSize: MainAxisSize.max,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  if (authState.authModel.name != null && authState.authModel.name != "")
                    Text(authState.authModel.name!,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w800, color: UiUtils.getColorScheme(context).primaryContainer)),
                  const SizedBox(height: 3),
                  if (authState.authModel.mobile != null && authState.authModel.mobile!.trim().isNotEmpty)
                    Text(authState.authModel.mobile!, style: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7))),
                  const SizedBox(height: 3),
                  if (authState.authModel.email != null && authState.authModel.email != "")
                    Text(authState.authModel.email!,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7))),
                ],
              ),
            ),
          ),
          GestureDetector(onTap: () => Navigator.of(context).pushNamed(Routes.editUserProfile, arguments: {'from': 'profile'}), child: const Icon(Icons.edit_rounded))
        ]);
      } else {
        return Row(mainAxisAlignment: MainAxisAlignment.spaceAround, children: <Widget>[
          //For Guest User
          Container(
              margin: const EdgeInsets.only(top: 10.0, bottom: 10.0),
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(shape: BoxShape.circle, border: Border.all(color: UiUtils.getColorScheme(context).primaryContainer)),
              alignment: Alignment.center,
              child: Icon(Icons.person, size: 40.0, color: UiUtils.getColorScheme(context).primaryContainer)),
          Column(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  CustomTextLabel(
                      text: 'plzLbl', textStyle: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, overflow: TextOverflow.ellipsis)),
                  InkWell(
                    child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 4),
                        child: CustomTextLabel(
                            text: 'loginBtn',
                            textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(color: Theme.of(context).primaryColor, fontWeight: FontWeight.w600, overflow: TextOverflow.ellipsis))),
                    onTap: () {
                      Future.delayed(const Duration(milliseconds: 500), () {
                        setState(() {
                          Navigator.of(context).pushNamed(Routes.login);
                        });
                      });
                    },
                  ),
                  CustomTextLabel(text: 'firstAccLbl', textStyle: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer)),
                ],
              ),
              CustomTextLabel(
                  text: 'allFunLbl', textStyle: Theme.of(context).textTheme.titleMedium?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, overflow: TextOverflow.ellipsis)),
            ],
          ),
        ]);
      }
    });
  }

  Widget setBody() {
    return Padding(
      padding: const EdgeInsets.only(top: 20.0),
      child: Container(
          padding: const EdgeInsetsDirectional.only(start: 20.0, end: 20.0),
          decoration: BoxDecoration(borderRadius: BorderRadius.circular(15.0), color: Theme.of(context).colorScheme.background),
          child: ScrollConfiguration(
            behavior: GlobalScrollBehavior(),
            child: BlocBuilder<AuthCubit, AuthState>(
              builder: (context, state) {
                return ListView(
                  padding: const EdgeInsetsDirectional.only(top: 10.0),
                  shrinkWrap: true,
                  physics: const BouncingScrollPhysics(),
                  children: <Widget>[
                    setDrawerItem('darkModeLbl', Icons.swap_horizontal_circle, true, false, true, 0),
                    setDrawerItem('notificationLbl', Icons.notifications_rounded, true, false, true, 1),
                    setDrawerItem('changeLang', Icons.g_translate_rounded, false, true, false, 2),
                    if (context.read<AuthCubit>().getUserId() != "0") setDrawerItem('bookmarkLbl', Icons.bookmarks_rounded, false, true, false, 3),
                    if (context.read<AuthCubit>().getUserId() != "0" && context.read<AuthCubit>().getRole() != "0") setDrawerItem('createNewsLbl', Icons.add_box_rounded, false, true, false, 4),
                    if (context.read<AuthCubit>().getUserId() != "0" && context.read<AuthCubit>().getRole() != "0") setDrawerItem('manageNewsLbl', Icons.edit_document, false, true, false, 5),
                    if (context.read<AuthCubit>().getUserId() != "0") setDrawerItem('managePreferences', Icons.thumbs_up_down_rounded, false, true, false, 6),
                    pagesBuild(),
                    setDrawerItem('rateUs', Icons.stars_sharp, false, true, false, 8),
                    setDrawerItem('shareApp', Icons.share_rounded, false, true, false, 9),
                    if (context.read<AuthCubit>().getUserId() != "0") setDrawerItem('logoutLbl', Icons.logout_rounded, false, true, false, 10),
                    if (context.read<AuthCubit>().getUserId() != "0") setDrawerItem('deleteAcc', Icons.delete_forever_rounded, false, true, false, 11),
                  ],
                );
              },
            ),
          )),
    );
  }

  @override
  Widget build(BuildContext context) {
    return SafeArea(
        child: Scaffold(
            body: Stack(
      children: [
        SingleChildScrollView(
            padding: const EdgeInsetsDirectional.only(start: 15.0, end: 15.0, top: 25.0, bottom: 10.0),
            child: Column(mainAxisAlignment: MainAxisAlignment.start, mainAxisSize: MainAxisSize.min, children: <Widget>[setHeader(), setBody()])),
      ],
    )));
  }
}
