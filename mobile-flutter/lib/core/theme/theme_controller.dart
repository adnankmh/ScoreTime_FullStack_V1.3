import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';

enum AppThemeName{stadium,midnight,light}
class ThemeSettings{final AppThemeName theme;final double fontScale;final String locale;const ThemeSettings({this.theme=AppThemeName.stadium,this.fontScale=1,this.locale='ar'});ThemeSettings copyWith({AppThemeName? theme,double? fontScale,String? locale})=>ThemeSettings(theme:theme??this.theme,fontScale:fontScale??this.fontScale,locale:locale??this.locale);}
class ThemeController extends Notifier<ThemeSettings>{
 @override ThemeSettings build(){Future.microtask(_load);return const ThemeSettings();}
 Future<void> _load()async{final p=await SharedPreferences.getInstance();final name=p.getString('theme');state=ThemeSettings(theme:AppThemeName.values.where((e)=>e.name==name).firstOrNull??AppThemeName.stadium,fontScale:p.getDouble('font_scale')??1,locale:p.getString('locale')??'ar');}
 void setTheme(AppThemeName v){state=state.copyWith(theme:v);SharedPreferences.getInstance().then((p)=>p.setString('theme',v.name));}
 void setFont(double v){state=state.copyWith(fontScale:v);SharedPreferences.getInstance().then((p)=>p.setDouble('font_scale',v));}
 void setLocale(String v){state=state.copyWith(locale:v);SharedPreferences.getInstance().then((p)=>p.setString('locale',v));}
}
final themeControllerProvider=NotifierProvider<ThemeController,ThemeSettings>(ThemeController.new);
class AppThemeFactory{
 static ThemeData build(AppThemeName name,double scale,[Map<String,dynamic>? remote]){final isLight=name==AppThemeName.light;final seed=name==AppThemeName.midnight?const Color(0xff8b5cf6):name==AppThemeName.light?const Color(0xff087f5b):const Color(0xff55e6a5);Color parse(String key,Color fallback){final v=remote?[key];if(v is String&&RegExp(r'^#[0-9A-Fa-f]{6}$').hasMatch(v)){return Color(int.parse('FF'+v.substring(1),radix:16));}return fallback;} final dynamicSeed=parse('accent',seed);final bg=parse('background',isLight?const Color(0xfff3f7f5):name==AppThemeName.midnight?const Color(0xff070611):const Color(0xff061019));final base=ThemeData(colorScheme:ColorScheme.fromSeed(seedColor:dynamicSeed,brightness:isLight?Brightness.light:Brightness.dark),useMaterial3:true,scaffoldBackgroundColor:bg);return base.copyWith(textTheme:base.textTheme.apply(fontSizeFactor:scale),cardTheme:CardThemeData(elevation:0,shape:RoundedRectangleBorder(borderRadius:BorderRadius.circular(22))),navigationBarTheme:NavigationBarThemeData(height:72,indicatorShape:RoundedRectangleBorder(borderRadius:BorderRadius.circular(14))));}
}
