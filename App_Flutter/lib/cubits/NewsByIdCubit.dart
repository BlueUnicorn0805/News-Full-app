// ignore_for_file: file_names

import 'package:news/data/models/NewsModel.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/repositories/NewsById/NewsByIdRepository.dart';

abstract class NewsByIdState {}

class NewsByIdInitial extends NewsByIdState {}

class NewsByIdFetchInProgress extends NewsByIdState {}

class NewsByIdFetchSuccess extends NewsByIdState {
  final List<NewsModel> newsById;

  NewsByIdFetchSuccess({
    required this.newsById,
  });
}

class NewsByIdFetchFailure extends NewsByIdState {
  final String errorMessage;

  NewsByIdFetchFailure(this.errorMessage);
}

class NewsByIdCubit extends Cubit<NewsByIdState> {
  final NewsByIdRepository _newsByIdRepository;

  NewsByIdCubit(this._newsByIdRepository) : super(NewsByIdInitial());

  Future<List<NewsModel>> getNewsById({required String newsId, required String langId, required String userId}) async {
    try {
      emit(NewsByIdFetchInProgress());
      final result = await _newsByIdRepository.getNewsById(langId: langId, newsId: newsId, userId: userId);
      emit(NewsByIdFetchSuccess(newsById: result['NewsById']));
      return result['NewsById'];
    } catch (e) {
      emit(NewsByIdFetchFailure(e.toString()));
      return [];
    }
  }
}
