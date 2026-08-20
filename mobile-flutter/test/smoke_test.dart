import 'package:flutter_test/flutter_test.dart';
import 'package:scoretime/core/config/app_config.dart';
import 'package:scoretime/core/i18n/app_strings.dart';

void main() {
  test('ScoreTime ships the six requested locales', () {
    expect(AppStrings.supported, ['en', 'ar', 'fr', 'es', 'de', 'tr']);
  });

  test('preview data is opt-in rather than a live fallback', () {
    expect(AppConfig.previewMode, isFalse);
  });
}
