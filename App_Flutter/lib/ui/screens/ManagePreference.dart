// ignore_for_file: file_names

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/ui/widgets/errorContainerWidget.dart';
import 'package:news/utils/ErrorMessageKeys.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/app/routes.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/cubits/UserPreferences/setUserPreferenceCatCubit.dart';
import 'package:news/cubits/UserPreferences/userByCategoryCubit.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/cubits/categoryCubit.dart';
import 'package:news/data/models/CategoryModel.dart';
import 'package:news/ui/styles/colors.dart';
import 'package:news/ui/widgets/SnackBarWidget.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/ui/widgets/networkImage.dart';
import 'package:news/ui/widgets/customBackBtn.dart';
import 'package:news/ui/widgets/customTextBtn.dart';
import 'package:news/ui/widgets/loginRequired.dart';

class ManagePref extends StatefulWidget {
  final int? from;

  const ManagePref({Key? key, this.from}) : super(key: key);

  @override
  State<StatefulWidget> createState() {
    return StateManagePref();
  }

  static Route route(RouteSettings routeSettings) {
    final arguments = routeSettings.arguments as Map<String, dynamic>;
    return CupertinoPageRoute(
        builder: (_) => ManagePref(
              from: arguments['from'],
            ));
  }
}

class StateManagePref extends State<ManagePref> with TickerProviderStateMixin {
  final GlobalKey<ScaffoldState> _scaffoldKey = GlobalKey<ScaffoldState>();
  String catId = "";
  List<String> selectedChoices = [];
  String selCatId = "";

  static int _count = 0;
  List<bool> _checks = [];
  late final ScrollController _categoryScrollController = ScrollController()..addListener(hasMoreCategoryScrollListener);

  @override
  void initState() {
    super.initState();

    getUserCatData();
  }

  void getUserCatData() {
    Future.delayed(Duration.zero, () {
      context.read<UserByCatCubit>().getUserByCat(context: context, userId: context.read<AuthCubit>().getUserId());
    });
  }

  @override
  void dispose() {
    _categoryScrollController.dispose();
    super.dispose();
  }

  void hasMoreCategoryScrollListener() {
    if (_categoryScrollController.offset >= _categoryScrollController.position.maxScrollExtent && !_categoryScrollController.position.outOfRange) {
      if (context.read<CategoryCubit>().hasMoreCategory()) {
        context.read<CategoryCubit>().getMoreCategory(context: context, langId: context.read<AppLocalizationCubit>().state.id);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(key: _scaffoldKey, appBar: setAppBar(), body: contentView());
  }

  setAppBar() {
    return PreferredSize(
        preferredSize: const Size(double.infinity, 70),
        child: AppBar(
          leading: widget.from == 1 ? const CustomBackButton(horizontalPadding: 15) : const SizedBox.shrink(),
          titleSpacing: 0.0,
          centerTitle: false,
          backgroundColor: Colors.transparent,
          title: CustomTextLabel(
              text: 'managePreferences',
              textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.w600, letterSpacing: 0.5)),
          actions: [skipBtn()],
        ));
  }

//set skip login btn
  skipBtn() {
    if (widget.from != 1) {
      return Align(
          alignment: Alignment.topRight,
          child: CustomTextButton(
            onTap: () {
              Navigator.of(context).pushReplacementNamed(Routes.home, arguments: false);
            },
            text: 'skip',
            color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7),
          ));
    } else {
      return const SizedBox.shrink();
    }
  }

  contentView() {
    return BlocConsumer<UserByCatCubit, UserByCatState>(
        bloc: context.read<UserByCatCubit>(),
        listener: (context, state) {
          if (state is UserByCatFetchSuccess) {
            for (int i = 0; i < (state).userByCat.length; i++) {
              catId = (state).userByCat[i]["category_id"];
            }
            setState(() {
              selectedChoices = catId == "" ? catId.split('') : catId.split(',');
            });
            context.read<CategoryCubit>().getCategory(context: context, langId: context.read<AppLocalizationCubit>().state.id);
          }
        },
        builder: (context, state) {
          return BlocConsumer<CategoryCubit, CategoryState>(
              bloc: context.read<CategoryCubit>(),
              listener: (context, state) {
                if (state is CategoryFetchSuccess) {
                  if ((state).category.isNotEmpty) {
                    setState(() {
                      _count = (state).category.length;
                      _checks = List.generate(_count, (i) => (selectedChoices.contains((state).category[i].id)) ? true : false);
                    });
                  }
                }
              },
              builder: (context, state) {
                if (state is CategoryFetchInProgress || state is CategoryInitial) {
                  return showCircularProgress(true, Theme.of(context).primaryColor);
                }
                if (state is CategoryFetchFailure) {
                  return ErrorContainerWidget(
                      errorMsg: (state.errorMessage.contains(ErrorMessageKeys.noInternet)) ? UiUtils.getTranslatedLabel(context, 'internetmsg') : state.errorMessage, onRetry: getUserCatData);
                }
                return SingleChildScrollView(
                  padding: const EdgeInsetsDirectional.only(start: 15.0, end: 15.0, bottom: 20.0),
                  controller: _categoryScrollController,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      Padding(
                          padding: const EdgeInsetsDirectional.only(top: 25.0),
                          child: GridView.builder(
                              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(crossAxisCount: 2, crossAxisSpacing: 20, mainAxisSpacing: 20),
                              shrinkWrap: true,
                              itemCount: (state as CategoryFetchSuccess).category.length,
                              physics: const ClampingScrollPhysics(),
                              itemBuilder: (context, index) {
                                return _buildCategoryContainer(
                                  category: state.category[index],
                                  hasMore: state.hasMore,
                                  hasMoreCategoryFetchError: state.hasMoreFetchError,
                                  index: index,
                                  totalCurrentCategory: state.category.length,
                                );
                              })),
                      nxtBtn()
                    ],
                  ),
                );
              });
        });
  }

  selectCatTxt() {
    return Transform(
      transform: Matrix4.translationValues(-50.0, 0.0, 0.0),
      child: CustomTextLabel(
        text: 'sel_pref_cat',
        textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer, fontWeight: FontWeight.w100, letterSpacing: 0.5),
      ),
    );
  }

  _buildCategoryContainer({
    required CategoryModel category,
    required int index,
    required int totalCurrentCategory,
    required bool hasMoreCategoryFetchError,
    required bool hasMore,
  }) {
    if (index == totalCurrentCategory - 1 && index != 0) {
      //check if hasMore
      if (hasMore) {
        if (hasMoreCategoryFetchError) {
          return const SizedBox.shrink();
        } else {
          return Center(child: Padding(padding: const EdgeInsets.symmetric(horizontal: 15.0, vertical: 8.0), child: showCircularProgress(true, Theme.of(context).primaryColor)));
        }
      }
    }
    return Container(
        padding: EdgeInsets.zero,
        decoration: const BoxDecoration(
          borderRadius: BorderRadius.all(Radius.circular(15)),
        ),
        child: InkWell(
          onTap: () {
            _checks[index] = !_checks[index];
            if (selectedChoices.contains(category.id)) {
              selectedChoices.remove(category.id);
              setState(() {});
            } else {
              selectedChoices.add(category.id!);
              setState(() {});
            }
            if (selectedChoices.isEmpty) {
              setState(() {
                selectedChoices.add("0");
              });
            } else {
              if (selectedChoices.contains("0")) {
                selectedChoices = List.from(selectedChoices)..remove("0");
              }
            }
          },
          child: Stack(
            children: [
              ClipRRect(
                  borderRadius: const BorderRadius.all(Radius.circular(15.0)),
                  child: CustomNetworkImage(
                    networkImageUrl: category.image!,
                    fit: BoxFit.cover,
                    isVideo: false,
                    height: MediaQuery.of(context).size.height / 2.9,
                    width: double.maxFinite,
                  )),
              Column(
                children: [
                  Align(
                      //checkbox
                      alignment: Alignment.topRight,
                      child: Container(
                        height: 20,
                        width: 20,
                        margin: const EdgeInsets.only(right: 10, top: 10),
                        decoration: BoxDecoration(
                          borderRadius: const BorderRadius.all(Radius.circular(5)),
                          color: selectedChoices.contains(category.id) ? Theme.of(context).primaryColor : secondaryColor,
                        ),
                        child: selectedChoices.contains(category.id)
                            ? const Icon(
                                Icons.check_rounded,
                                color: secondaryColor,
                                size: 20,
                              )
                            : null,
                      )),
                  const Spacer(),
                  ClipRRect(
                    //Text with shadermask
                    borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(15.0), bottomRight: Radius.circular(15.0)),
                    child: ShaderMask(
                      shaderCallback: (bounds) {
                        return LinearGradient(begin: Alignment.topCenter, end: Alignment.bottomCenter, colors: [Colors.transparent, darkSecondaryColor.withOpacity(0.8)]).createShader(bounds);
                      },
                      blendMode: BlendMode.overlay,
                      child: Container(
                        height: 60,
                        width: double.infinity,
                        color: Colors.transparent,
                        padding: const EdgeInsets.only(top: 30),
                        child: Padding(
                          padding: const EdgeInsetsDirectional.only(start: 10),
                          child: Text(
                            category.categoryName!,
                            style: const TextStyle(fontSize: 14, fontWeight: FontWeight.normal, color: Colors.white),
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              )
            ],
          ),
        ));
  }

  nxtBtn() {
    return BlocConsumer<SetUserPrefCatCubit, SetUserPrefCatState>(
        bloc: context.read<SetUserPrefCatCubit>(),
        listener: (context, state) {
          if (state is SetUserPrefCatFetchSuccess) {
            showSnackBar(UiUtils.getTranslatedLabel(context, 'preferenceSave'), context);
            if (widget.from == 2) {
              Navigator.of(context).pushReplacementNamed(Routes.home, arguments: false);
            } else {
              Navigator.pop(context);
            }
          }
        },
        builder: (context, state) {
          return Padding(
            padding: const EdgeInsets.only(top: 30.0),
            child: InkWell(
                child: Container(
                  height: 55.0,
                  width: MediaQuery.of(context).size.width * 0.95,
                  alignment: Alignment.center,
                  decoration: BoxDecoration(color: Theme.of(context).primaryColor, borderRadius: BorderRadius.circular(15.0)),
                  child: (state is SetUserPrefCatFetchInProgress)
                      ? showCircularProgress(true, secondaryColor)
                      : CustomTextLabel(
                          text: (widget.from == 2) //@Start
                              ? 'nxt'
                              : 'saveLbl',
                          //from Settings
                          textStyle: Theme.of(context).textTheme.titleLarge?.copyWith(color: secondaryColor, fontWeight: FontWeight.w600, fontSize: 21),
                        ),
                ),
                onTap: () async {
                  if (context.read<AuthCubit>().getUserId() != "0") {
                    if (selectedChoices.isEmpty) {
                      //no preference selected
                      Navigator.of(context).pushNamedAndRemoveUntil(Routes.home, (Route<dynamic> route) => false);
                      return;
                    } else if (selectedChoices.length == 1) {
                      setState(() {
                        selCatId = selectedChoices.join();
                      });
                    } else {
                      setState(() {
                        selCatId = selectedChoices.join(',');
                      });
                    }
                    context.read<SetUserPrefCatCubit>().setUserPrefCat(context: context, catId: selCatId, userId: context.read<AuthCubit>().getUserId());
                  } else {
                    loginRequired(context);
                  }
                }),
          );
        });
  }
}
