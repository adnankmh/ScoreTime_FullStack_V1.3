import 'package:flutter_test/flutter_test.dart';
import 'package:scoretime/core/config/app_config.dart';
import 'package:shared_preferences/shared_preferences.dart';

void main() {
  test('normalizes plain and Markdown API URLs', () {
    expect(
      AppConfig.normalizeApiUrl(' https://api.scoretime.org/api/v1/ '),
      'https://api.scoretime.org/api/v1',
    );
    expect(
      AppConfig.normalizeApiUrl(
        '[ScoreTime](https://api.scoretime.org/api/v1)',
      ),
      'https://api.scoretime.org/api/v1',
    );
  });

  test('accepts public HTTPS API endpoints', () {
    expect(
      AppConfig.isValidPublicApiUrl('https://api.scoretime.org/api/v1'),
      isTrue,
    );
    expect(
      AppConfig.isValidPublicApiUrl(
        'https://scoretime.org/backend/public/api/v1',
      ),
      isTrue,
    );
    expect(
      AppConfig.isValidPublicApiUrl('https://example.com/api/v1'),
      isFalse,
    );
    expect(
      AppConfig.isValidPublicApiUrl('http://api.scoretime.org/api/v1'),
      isFalse,
    );
    expect(
      AppConfig.isValidPublicApiUrl('https://api.scoretime.org/v1'),
      isFalse,
    );
  });

  test('accepts only private HTTP LAN endpoints', () {
    expect(
      AppConfig.isValidPrivateLanApiUrl('http://192.168.1.25:8000/api/v1'),
      isTrue,
    );
    expect(
      AppConfig.isValidPrivateLanApiUrl('http://10.0.0.8:8000/api/v1'),
      isTrue,
    );
    expect(
      AppConfig.isValidPrivateLanApiUrl('http://172.20.0.2/api/v1'),
      isTrue,
    );
    expect(
      AppConfig.isValidPrivateLanApiUrl('http://8.8.8.8/api/v1'),
      isFalse,
    );
    expect(
      AppConfig.isValidApiUrl('http://api.scoretime.org/api/v1'),
      isFalse,
    );
  });

  test('preview is explicit and a saved server turns it off', () async {
    SharedPreferences.setMockInitialValues({});
    await AppConfig.initialize();
    expect(AppConfig.previewMode, isFalse);

    await AppConfig.enablePreviewMode();
    expect(AppConfig.previewMode, isTrue);

    await AppConfig.saveApiBaseUrl('http://192.168.1.25:8000/api/v1');
    expect(AppConfig.previewMode, isFalse);
    expect(AppConfig.isApiConfigured, isTrue);
  });
}
