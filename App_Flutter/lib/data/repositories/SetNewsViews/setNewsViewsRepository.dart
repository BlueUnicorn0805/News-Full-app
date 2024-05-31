// ignore_for_file: file_names
import 'package:news/data/repositories/SetNewsViews/setNewsViewsDataRemoteSource.dart';

class SetNewsViewsRepository {
  static final SetNewsViewsRepository setNewsViewsRepository = SetNewsViewsRepository._internal();

  late SetNewsViewsDataRemoteDataSource setNewsViewsDataRemoteDataSource;

  factory SetNewsViewsRepository() {
    setNewsViewsRepository.setNewsViewsDataRemoteDataSource = SetNewsViewsDataRemoteDataSource();
    return setNewsViewsRepository;
  }

  SetNewsViewsRepository._internal();

  Future<Map<String, dynamic>> setNewsViews({required String userId, required String newsId, required bool isBreakingNews}) async {
    final result = await setNewsViewsDataRemoteDataSource.setNewsViews(userId: userId, newsId: newsId, isBreakingNews: isBreakingNews);
    return result;
  }
}
