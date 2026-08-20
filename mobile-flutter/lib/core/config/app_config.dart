import 'package:shared_preferences/shared_preferences.dart';

class AppConfig {
  static const _compiledApiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: '',
  );

  static const _apiPreferenceKey = 'scoretime_api_base_url';
  static String _savedApiBaseUrl = '';

  static String get apiBaseUrl {
    if (_savedApiBaseUrl.isNotEmpty) {
      return _savedApiBaseUrl;
    }
    return normalizeApiUrl(_compiledApiBaseUrl);
  }

  static bool get isApiConfigured => isValidPublicApiUrl(apiBaseUrl);

  static Future<void> initialize() async {
    try {
      final preferences = await SharedPreferences.getInstance();
      final saved = normalizeApiUrl(
        preferences.getString(_apiPreferenceKey) ?? '',
      );
      _savedApiBaseUrl = isValidPublicApiUrl(saved) ? saved : '';
    } catch (_) {
      _savedApiBaseUrl = '';
    }
  }

  static Future<void> saveApiBaseUrl(String value) async {
    final normalized = normalizeApiUrl(value);
    if (!isValidPublicApiUrl(normalized)) {
      throw const FormatException(
        'Use your real HTTPS Laravel URL ending in /api/v1.',
      );
    }
    final preferences = await SharedPreferences.getInstance();
    await preferences.setString(_apiPreferenceKey, normalized);
    _savedApiBaseUrl = normalized;
  }

  static String normalizeApiUrl(String raw) {
    var value = raw.trim();
    final markdown = RegExp(r'^\[([^\]]+)\]\(([^)]+)\)$').firstMatch(value);
    if (markdown != null) {
      value = markdown.group(2)!.trim();
    }
    if ((value.startsWith('"') && value.endsWith('"')) ||
        (value.startsWith("'") && value.endsWith("'"))) {
      value = value.substring(1, value.length - 1).trim();
    }
    while (value.endsWith('/')) {
      value = value.substring(0, value.length - 1);
    }
    return value;
  }

  static bool isValidPublicApiUrl(String raw) {
    final value = normalizeApiUrl(raw);
    final uri = Uri.tryParse(value);
    if (uri == null ||
        uri.scheme != 'https' ||
        uri.host.isEmpty ||
        uri.userInfo.isNotEmpty ||
        uri.hasQuery ||
        uri.hasFragment ||
        !uri.path.endsWith('/api/v1')) {
      return false;
    }
    final host = uri.host.toLowerCase();
    return host != 'example.com' &&
        !host.endsWith('.example.com') &&
        host != 'localhost' &&
        !host.endsWith('.invalid') &&
        !host.endsWith('.test') &&
        !host.endsWith('.example');
  }

  static const webDemoMode = bool.fromEnvironment(
    'WEB_DEMO_MODE',
    defaultValue: false,
  );
}
