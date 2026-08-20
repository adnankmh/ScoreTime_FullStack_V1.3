import 'package:flutter_test/flutter_test.dart';
import 'package:scoretime/core/config/app_config.dart';

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

  test('accepts only real HTTPS API endpoints', () {
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
}
