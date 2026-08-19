import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';

enum AppThemeName { stadium, midnight, light }

class ThemeSettings {
  final AppThemeName theme;
  final double fontScale;
  final String locale;
  const ThemeSettings({
    this.theme = AppThemeName.stadium,
    this.fontScale = 1,
    this.locale = 'en',
  });

  ThemeSettings copyWith({AppThemeName? theme, double? fontScale, String? locale}) =>
      ThemeSettings(
        theme: theme ?? this.theme,
        fontScale: fontScale ?? this.fontScale,
        locale: locale ?? this.locale,
      );
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
      theme: AppThemeName.values.firstWhere(
        (e) => e.name == name,
        orElse: () => AppThemeName.stadium,
      ),
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

final themeControllerProvider =
    NotifierProvider<ThemeController, ThemeSettings>(ThemeController.new);

class ScoreTimeColors {
  static const ink = Color(0xFF020817);
  static const ink2 = Color(0xFF051126);
  static const panel = Color(0xFF07172D);
  static const panel2 = Color(0xFF0B203B);
  static const blue = Color(0xFF0A7BFF);
  static const cyan = Color(0xFF19D8FF);
  static const gold = Color(0xFFFFC542);
  static const green = Color(0xFF31E6A1);
  static const red = Color(0xFFFF4D6D);
  static const violet = Color(0xFF8B5CF6);
}

class AppThemeFactory {
  static ThemeData build(
    AppThemeName name,
    double scale, [
    Map<String, dynamic>? remote,
  ]) {
    final isLight = name == AppThemeName.light;

    Color parse(String key, Color fallback) {
      final v = remote?[key];
      if (v is String && RegExp(r'^#[0-9A-Fa-f]{6}$').hasMatch(v)) {
        return Color(int.parse('FF${v.substring(1)}', radix: 16));
      }
      return fallback;
    }

    final primary = parse(
      'accent',
      name == AppThemeName.midnight ? ScoreTimeColors.violet : ScoreTimeColors.blue,
    );
    final secondary = parse('accent2', ScoreTimeColors.cyan);
    final background = parse(
      'background',
      isLight
          ? const Color(0xFFF4F7FC)
          : name == AppThemeName.midnight
              ? const Color(0xFF06031A)
              : ScoreTimeColors.ink,
    );
    final surface = parse(
      'surface',
      isLight ? Colors.white : ScoreTimeColors.panel,
    );
    final brightness = isLight ? Brightness.light : Brightness.dark;

    final scheme = ColorScheme.fromSeed(
      seedColor: primary,
      brightness: brightness,
    ).copyWith(
      primary: primary,
      secondary: secondary,
      tertiary: ScoreTimeColors.gold,
      surface: surface,
      error: ScoreTimeColors.red,
    );

    final base = ThemeData(
      brightness: brightness,
      colorScheme: scheme,
      useMaterial3: true,
      scaffoldBackgroundColor: background,
      fontFamily: 'Roboto',
      visualDensity: VisualDensity.standard,
    );

    final text = base.textTheme.apply(fontSizeFactor: scale).copyWith(
      displayLarge: base.textTheme.displayLarge?.copyWith(
        fontWeight: FontWeight.w900,
        letterSpacing: -2.3,
        height: .98,
      ),
      headlineLarge: base.textTheme.headlineLarge?.copyWith(
        fontWeight: FontWeight.w900,
        letterSpacing: -1.25,
      ),
      headlineMedium: base.textTheme.headlineMedium?.copyWith(
        fontWeight: FontWeight.w900,
        letterSpacing: -.85,
      ),
      titleLarge: base.textTheme.titleLarge?.copyWith(
        fontWeight: FontWeight.w900,
        letterSpacing: -.45,
      ),
      titleMedium: base.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w800),
      labelLarge: base.textTheme.labelLarge?.copyWith(fontWeight: FontWeight.w800),
    );

    final border = BorderSide(
      color: isLight
          ? const Color(0x17021B3A)
          : Colors.white.withValues(alpha: .075),
    );

    return base.copyWith(
      textTheme: text,
      dividerColor: border.color,
      cardTheme: CardThemeData(
        elevation: 0,
        color: surface.withValues(alpha: isLight ? .98 : .88),
        surfaceTintColor: Colors.transparent,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(22),
          side: border,
        ),
      ),
      appBarTheme: AppBarTheme(
        backgroundColor: Colors.transparent,
        elevation: 0,
        scrolledUnderElevation: 0,
        centerTitle: false,
        surfaceTintColor: Colors.transparent,
        titleTextStyle: text.titleLarge?.copyWith(color: scheme.onSurface),
      ),
      navigationBarTheme: NavigationBarThemeData(
        height: 70,
        backgroundColor: isLight ? Colors.white : const Color(0xFF041127),
        indicatorColor: primary.withValues(alpha: .18),
        indicatorShape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(15),
        ),
        labelTextStyle: WidgetStatePropertyAll(
          text.labelSmall?.copyWith(fontWeight: FontWeight.w800),
        ),
      ),
      navigationRailTheme: NavigationRailThemeData(
        backgroundColor: Colors.transparent,
        indicatorColor: primary.withValues(alpha: .18),
        selectedIconTheme: IconThemeData(color: secondary),
        selectedLabelTextStyle: text.labelLarge?.copyWith(color: scheme.onSurface),
        unselectedIconTheme: IconThemeData(color: scheme.onSurfaceVariant),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: isLight
            ? const Color(0xFFF1F5FA)
            : const Color(0xFF0A1930).withValues(alpha: .92),
        hintStyle: TextStyle(color: scheme.onSurfaceVariant.withValues(alpha: .72)),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide.none,
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: border,
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide(color: primary.withValues(alpha: .8), width: 1.2),
        ),
      ),
      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(
          elevation: 0,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15),
        ),
      ),
      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
          side: border,
          padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 14),
        ),
      ),
      chipTheme: base.chipTheme.copyWith(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(99)),
        side: border,
      ),
    );
  }
}
