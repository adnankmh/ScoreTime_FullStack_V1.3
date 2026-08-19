import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:scoretime/core/config/app_config.dart';
class AuthRepository{
 final Dio _dio=Dio(BaseOptions(baseUrl:AppConfig.apiBaseUrl,headers:{'Accept':'application/json'}));
 final FlutterSecureStorage _storage=const FlutterSecureStorage();
 Future<void> login(String login,String password)async{final r=await _dio.post('/auth/login',data:{'login':login,'password':password});final token=r.data['token'] as String;await _storage.write(key:'auth_token',value:token);}
 Future<void> register({required String name,required String username,required String email,required String password})async{final r=await _dio.post('/auth/register',data:{'name':name,'username':username,'email':email,'password':password,'password_confirmation':password});await _storage.write(key:'auth_token',value:r.data['token'] as String);}
 Future<String?> token()=>_storage.read(key:'auth_token');
 Future<void> logout()async{final t=await token();if(t!=null){try{await _dio.post('/auth/logout',options:Options(headers:{'Authorization':'Bearer $t'}));}catch(_){}}await _storage.delete(key:'auth_token');}
}
