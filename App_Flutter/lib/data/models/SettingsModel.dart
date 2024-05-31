// ignore_for_file: file_names

class SettingsModel {
  bool showIntroSlider;
  bool notification;
  String languageCode;
  String theme;
  String token;

  SettingsModel({
    required this.languageCode,
    required this.showIntroSlider,
    required this.theme,
    required this.notification,
    required this.token,
  });

  static SettingsModel fromJson(var settingsJson) {
    //to see the json response go to getCurrentSettings() function in settingsRepository
    return SettingsModel(
      theme: settingsJson['theme'],
      showIntroSlider: settingsJson['showIntroSlider'],
      notification: settingsJson['notification'],
      languageCode: settingsJson['languageCode'],
      token: settingsJson['token'],
    );
  }

  SettingsModel copyWith({
    String? theme,
    bool? showIntroSlider,
    bool? notification,
    String? languageCode,
    String? token,
  }) {
    return SettingsModel(
      theme: theme ?? this.theme,
      notification: notification ?? this.notification,
      showIntroSlider: showIntroSlider ?? this.showIntroSlider,
      languageCode: languageCode ?? this.languageCode,
      token: token ?? this.token,
    );
  }
}
