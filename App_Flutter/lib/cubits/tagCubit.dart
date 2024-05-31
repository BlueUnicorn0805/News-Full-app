// ignore_for_file: file_names

import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/data/models/TagModel.dart';
import '../data/repositories/Tag/tagRepository.dart';

abstract class TagState {}

class TagInitial extends TagState {}

class TagFetchInProgress extends TagState {}

class TagFetchSuccess extends TagState {
  final List<TagModel> tag;

  TagFetchSuccess({
    required this.tag,
  });
}

class TagFetchFailure extends TagState {
  final String errorMessage;

  TagFetchFailure(this.errorMessage);
}

class TagCubit extends Cubit<TagState> {
  final TagRepository _tagRepository;

  TagCubit(this._tagRepository) : super(TagInitial());

  void getTag({required String langId}) async {
    try {
      emit(TagFetchInProgress());
      final result = await _tagRepository.getTag(langId: langId);

      emit(TagFetchSuccess(tag: result['Tag']));
    } catch (e) {
      emit(TagFetchFailure(e.toString()));
    }
  }

  List<TagModel> tagList() {
    if (state is TagFetchSuccess) {
      return (state as TagFetchSuccess).tag;
    }
    return [];
  }
}
