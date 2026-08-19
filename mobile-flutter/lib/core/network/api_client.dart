import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../config/app_config.dart';

class ApiClient {
  ApiClient() {
    dio.interceptors.add(InterceptorsWrapper(onRequest: (options, handler) async {
      final token = await _storage.read(key: 'auth_token');
      if (token != null && token.isNotEmpty) options.headers['Authorization'] = 'Bearer $token';
      handler.next(options);
    }));
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
