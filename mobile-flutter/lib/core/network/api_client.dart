import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';

class ApiClient {
  ApiClient() {
    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _storage.read(key: 'auth_token');
        final preferences = await SharedPreferences.getInstance();
        if (token != null && token.isNotEmpty) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        options.headers['X-Locale'] = preferences.getString('locale') ?? 'en';
        options.headers['Accept-Language'] = preferences.getString('locale') ?? 'en';
        handler.next(options);
      },
      onError: (error, handler) async {
        final options = error.requestOptions;
        final retryableType = {
          DioExceptionType.connectionTimeout,
          DioExceptionType.receiveTimeout,
          DioExceptionType.connectionError,
        }.contains(error.type);
        final retryableStatus = (error.response?.statusCode ?? 0) >= 500;
        if (options.method == 'GET' &&
            options.extra['scoretime_retried'] != true &&
            (retryableType || retryableStatus)) {
          options.extra['scoretime_retried'] = true;
          try {
            return handler.resolve(await dio.fetch(options));
          } on DioException catch (retryError) {
            return handler.next(retryError);
          }
        }
        handler.next(error);
      },
    ));
  }
  final FlutterSecureStorage _storage = const FlutterSecureStorage();
  final Dio dio = Dio(BaseOptions(
    baseUrl: AppConfig.apiBaseUrl,
    connectTimeout: const Duration(seconds: 12),
    receiveTimeout: const Duration(seconds: 12),
    headers: const {'Accept': 'application/json'},
  ));
  Future<Map<String,dynamic>> getFeed() async {
    final r = await dio.get('/feed');
    return Map<String,dynamic>.from(r.data['data']);
  }
  Future<List<dynamic>> getMatches([String? date]) async {
    final r = await dio.get('/matches', queryParameters: {if(date != null) 'date': date});
    return List<dynamic>.from(r.data['data']);
  }
}
