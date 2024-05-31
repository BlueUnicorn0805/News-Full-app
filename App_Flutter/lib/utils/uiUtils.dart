// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:hive/hive.dart';
import 'package:intl/intl.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:news/app/routes.dart';
import 'package:news/cubits/Auth/authCubit.dart';
import 'package:news/cubits/Bookmark/bookmarkCubit.dart';
import 'package:news/cubits/LikeAndDislikeNews/LikeAndDislikeCubit.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/cubits/appSystemSettingCubit.dart';
import 'package:news/cubits/languageJsonCubit.dart';
import 'package:news/cubits/privacyTermsCubit.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/InterstitialAds/fbInterstitialAds.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/InterstitialAds/googleInterstitialAds.dart';
import 'package:news/ui/screens/NewsDetail/Widgets/InterstitialAds/unityInterstitialAds.dart';
import 'package:news/ui/styles/colors.dart';
import 'package:news/utils/labelKeys.dart';
import 'package:news/ui/styles/appTheme.dart';
import 'package:news/utils/hiveBoxKeys.dart';

class UiUtils {
  static GlobalKey<NavigatorState> rootNavigatorKey = GlobalKey<NavigatorState>();

  static Future<void> setDynamicStringValue(String key, String value) async {
    Hive.box(settingsBoxKey).put(key, value);
  }

  static Future<void> setDynamicListValue(String key, String value) async {
    List<String>? valueList = getDynamicListValue(key);
    if (!valueList.contains(value)) {
      if (valueList.length > 4) valueList.removeAt(0);
      valueList.add(value);

      Hive.box(settingsBoxKey).put(key, valueList);
    }
  }

  static List<String> getDynamicListValue(String key) {
    return Hive.box(settingsBoxKey).get(key);
  }

  //used for IntroSlider Images only
  static String getImagePath(String imageName) {
    return "assets/images/$imageName";
  }

  static String getSvgImagePath(String imageName) {
    return "assets/images/svgImage/$imageName.svg";
  }

  static ColorScheme getColorScheme(BuildContext context) {
    return Theme.of(context).colorScheme;
  }

// get app theme
  static String getThemeLabelFromAppTheme(AppTheme appTheme) {
    if (appTheme == AppTheme.Dark) {
      return darkThemeKey;
    }
    return lightThemeKey;
  }

  static AppTheme getAppThemeFromLabel(String label) {
    if (label == darkThemeKey) {
      return AppTheme.Dark;
    }
    return AppTheme.Light;
  }

  static String getTranslatedLabel(BuildContext context, String labelKey) {
    return context.read<LanguageJsonCubit>().getTranslatedLabels(labelKey);
  }

  static String? convertToAgo(BuildContext context, DateTime input, int from) {
    Duration diff = DateTime.now().difference(input);
    initializeDateFormatting(); //locale according to location
    final langCode = Hive.box(settingsBoxKey).get(currentLanguageCodeKey);
    bool isNegative = diff.isNegative;
    if (diff.inDays >= 1 || (isNegative && diff.inDays < 1)) {
      if (from == 0) {
        var newFormat = DateFormat("MMM dd, yyyy", langCode);
        final newsDate1 = newFormat.format(input);
        return newsDate1;
      } else if (from == 1) {
        return "${diff.inDays} ${getTranslatedLabel(context, 'days')} ${getTranslatedLabel(context, 'ago')}";
      } else if (from == 2) {
        var newFormat = DateFormat("dd MMMM yyyy HH:mm:ss", langCode);
        final newsDate1 = newFormat.format(input);
        return newsDate1;
      }
    } else if (diff.inHours >= 1 || (isNegative && diff.inMinutes < 1)) {
      if (input.minute == 00) {
        return "${diff.inHours} ${getTranslatedLabel(context, 'hours')} ${getTranslatedLabel(context, 'ago')}";
      } else {
        if (from == 2) {
          return "${getTranslatedLabel(context, 'about')} ${diff.inHours} ${getTranslatedLabel(context, 'hours')} ${input.minute} ${getTranslatedLabel(context, 'minutes')} ${getTranslatedLabel(context, 'ago')}";
        } else {
          return "${diff.inHours} ${getTranslatedLabel(context, 'hours')} ${input.minute} ${getTranslatedLabel(context, 'minutes')} ${getTranslatedLabel(context, 'ago')}";
        }
      }
    } else if (diff.inMinutes >= 1 || (isNegative && diff.inMinutes < 1)) {
      return "${diff.inMinutes} ${getTranslatedLabel(context, 'minutes')} ${getTranslatedLabel(context, 'ago')}";
    } else if (diff.inSeconds >= 1) {
      return "${diff.inSeconds} ${getTranslatedLabel(context, 'seconds')} ${getTranslatedLabel(context, 'ago')}";
    } else {
      return getTranslatedLabel(context, 'justNow');
    }
    return null;
  }

  static setUIOverlayStyle({required AppTheme appTheme}) {
    appTheme == AppTheme.Light
        ? SystemChrome.setSystemUIOverlayStyle(SystemUiOverlayStyle(statusBarColor: backgroundColor.withOpacity(0.8), statusBarBrightness: Brightness.light, statusBarIconBrightness: Brightness.dark))
        : SystemChrome.setSystemUIOverlayStyle(
            SystemUiOverlayStyle(statusBarColor: darkSecondaryColor.withOpacity(0.8), statusBarBrightness: Brightness.dark, statusBarIconBrightness: Brightness.light));
  }

  static userLogOut({required BuildContext contxt}) {
    Future.delayed(Duration.zero, () {
      contxt.read<PrivacyTermsCubit>().getPrivacyTerms(context: contxt, langId: contxt.read<AppLocalizationCubit>().state.id);
    });
    for (int i = 0; i < AuthProvider.values.length; i++) {
      if (AuthProvider.values[i].name == contxt.read<AuthCubit>().getType()) {
        contxt.read<BookmarkCubit>().resetState();
        contxt.read<LikeAndDisLikeCubit>().resetState();
        contxt.read<AuthCubit>().signOut(AuthProvider.values[i]).then((value) {
          Navigator.of(contxt).pushNamedAndRemoveUntil(Routes.login, (route) => false);
        });
      }
    }
  }

//widget for User Profile Picture in Comments
  static Widget setFixedSizeboxForProfilePicture({required Widget childWidget}) {
    return SizedBox(height: 35, width: 35, child: childWidget);
  }

  //Interstitial Ads
  static showInterstitialAds({required BuildContext context}) {
    if (context.read<AppConfigurationCubit>().getInAppAdsMode() == "1") {
      if (context.read<AppConfigurationCubit>().checkAdsType() == "google") {
        showGoogleInterstitialAd(context);
      } else if (context.read<AppConfigurationCubit>().checkAdsType() == "fb") {
        showFBInterstitialAd();
      } else {
        showUnityInterstitialAds(context.read<AppConfigurationCubit>().interstitialId()!);
      }
    }
  }

  //calculate time in Minutes to Read News Article
  static int calculateReadingTime(String text) {
    const wordsPerMinute = 200;
    final wordCount = text.trim().split(' ').length;
    final readTime = (wordCount / wordsPerMinute).ceil();
    return readTime;
  }
}
