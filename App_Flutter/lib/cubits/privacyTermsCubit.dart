// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../data/models/OtherPageModel.dart';
import '../data/repositories/OtherPages/otherPagesRepository.dart';

abstract class PrivacyTermsState {}

class PrivacyTermsInitial extends PrivacyTermsState {}

class PrivacyTermsFetchInProgress extends PrivacyTermsState {}

class PrivacyTermsFetchSuccess extends PrivacyTermsState {
  final OtherPageModel termsPolicy;
  final OtherPageModel privacyPolicy;

  PrivacyTermsFetchSuccess({
    required this.termsPolicy,
    required this.privacyPolicy,
  });
}

class PrivacyTermsFetchFailure extends PrivacyTermsState {
  final String errorMessage;

  PrivacyTermsFetchFailure(this.errorMessage);
}

class PrivacyTermsCubit extends Cubit<PrivacyTermsState> {
  final OtherPageRepository _otherPageRepository;

  PrivacyTermsCubit(this._otherPageRepository) : super(PrivacyTermsInitial());

  void getPrivacyTerms({required BuildContext context, required String langId}) async {
    emit(PrivacyTermsFetchInProgress());
    try {
      final Map<String, dynamic> result = await _otherPageRepository.getPrivacyTermsPage(context: context, langId: langId);
      emit(PrivacyTermsFetchSuccess(privacyPolicy: OtherPageModel.fromPrivacyTermsJson(result['privacy_policy']), termsPolicy: OtherPageModel.fromPrivacyTermsJson(result['terms_policy'])));
    } catch (e) {
      emit(PrivacyTermsFetchFailure(e.toString()));
    }
  }
}
