import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';

enum AppThemeName { stadium, midnight, light }

class ThemeSettings {
  final AppThemeName theme;
  final double fontScale;
  final String locale;
  const ThemeSettings({this.theme = AppThemeName.stadium, this.fontScale = 1, this.locale = 'en'});
  ThemeSettings copyWith({AppThemeName? theme, double? fontScale, String? locale}) =>
      ThemeSettings(theme: theme ?? this.theme, fontScale: fontScale ?? this.fontScale, locale: locale ?? this.locale);
}

class ThemeController extends Notifier<ThemeSettings> {
  @override
  ThemeSettings build() {
    Future.microtask(_load);
    return const ThemeSettings();
  }

  Future<void> _load() async {
    final p = await SharedPreferences.getInstance();
    final name = p.getString('theme');
    state = ThemeSettings(
      theme: AppThemeName.values.firstWhere((e) => e.name == name, orElse: () => AppThemeName.stadium),
      fontScale: p.getDouble('font_scale') ?? 1,
      locale: p.getString('locale') ?? 'en',
    );
  }

  void setTheme(AppThemeName v) {
    state = state.copyWith(theme: v);
    SharedPreferences.getInstance().then((p) => p.setString('theme', v.name));
  }

  void setFont(double v) {
    state = state.copyWith(fontScale: v);
    SharedPreferences.getInstance().then((p) => p.setDouble('font_scale', v));
  }

  void setLocale(String v) {
    state = state.copyWith(locale: v);
    SharedPreferences.getInstance().then((p) => p.setString('locale', v));
  }
}

final themeControllerProvider = NotifierProvider<ThemeController, ThemeSettings>(ThemeController.new);

class AppThemeFactory {
  static const _scoreBlue = Color(0xFF0B8CFF);
  static const _scoreCyan = Color(0xFF18D7FF);
  static const _scoreGold = Color(0xFFF6C453);
  static const _dark = Color(0xFF020716);
  static const _surface = Color(0xFF08152B);

  static ThemeData build(AppThemeName name, double scale, [Map<String, dynamic>? remote]) {
    final isLight = name == AppThemeName.light;
    Color parse(String key, Color fallback) {
      final v = remote?[key];
      if (v is String && RegExp(r'^#[0-9A-Fa-f]{6}$').hasMatch(v)) {
        return Color(int.parse('FF${v.substring(1)}', radix: 16));
      }
      return fallback;
    }

    final primary = parse('accent', name == AppThemeName.midnight ? const Color(0xFF8B5CF6) : _scoreBlue);
    final background = parse('background', isLight ? const Color(0xFFF2F6FC) : name == AppThemeName.midnight ? const Color(0xFF050319) : _dark);
    final brightness = isLight ? Brightness.light : Brightness.dark;
    final scheme = ColorScheme.fromSeed(seedColor: primary, brightness: brightness).copyWith(
      primary: primary,
      secondary: parse('accent2', _scoreCyan),
      tertiary: _scoreGold,
      surface: parse('surface', isLight ? Colors.white : _surface),
    );

    final base = ThemeData(
      brightness: brightness,
      colorScheme: scheme,
      useMaterial3: true,
      scaffoldBackgroundColor: background,
      fontFamily: 'Roboto',
    );

    final text = base.textTheme.apply(fontSizeFactor: scale).copyWith(
      headlineLarge: base.textTheme.headlineLarge?.copyWith(fontWeight: FontWeight.w900, letterSpacing: -1.2),
      headlineMedium: base.textTheme.headlineMedium?.copyWith(fontWeight: FontWeight.w900, letterSpacing: -.8),
      titleLarge: base.textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w900, letterSpacing: -.4),
      titleMedium: base.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w800),
      labelLarge: base.textTheme.labelLarge?.copyWith(fontWeight: FontWeight.w800),
    );

    return base.copyWith(
      textTheme: text,
      cardTheme: CardThemeData(
        elevation: 0,
        color: scheme.surface.withValues(alpha: isLight ? 1 : .9),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(24),
          side: BorderSide(color: scheme.outlineVariant.withValues(alpha: .22)),
        ),
      ),
      appBarTheme: AppBarTheme(
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: false,
        surfaceTintColor: Colors.transparent,
        titleTextStyle: text.titleLarge?.copyWith(color: scheme.onSurface),
      ),
      navigationBarTheme: NavigationBarThemeData(
        height: 72,
        backgroundColor: isLight ? Colors.white : const Color(0xFF061127),
        indicatorColor: primary.withValues(alpha: .17),
        indicatorShape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        labelTextStyle: WidgetStatePropertyAll(text.labelSmall?.copyWith(fontWeight: FontWeight.w800)),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: scheme.surfaceContainerHighest.withValues(alpha: .42),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide.none),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: scheme.outlineVariant.withValues(alpha: .28))),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: primary.withValues(alpha: .75), width: 1.4)),
      ),
      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)), padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16)),
      ),
      chipTheme: base.chipTheme.copyWith(shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(99))),
    );
  }
}
