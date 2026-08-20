import 'package:shared_preferences/shared_preferences.dart';

class AppConfig {
  static const _compiledApiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: '',
  );

  static const _apiPreferenceKey = 'scoretime_api_base_url';
  static const _previewPreferenceKey = 'scoretime_preview_mode';
  static String _savedApiBaseUrl = '';
  static bool _previewMode = false;

  static String get apiBaseUrl {
    if (_savedApiBaseUrl.isNotEmpty) {
      return _savedApiBaseUrl;
    }
    return normalizeApiUrl(_compiledApiBaseUrl);
  }

  static bool get isApiConfigured => isValidApiUrl(apiBaseUrl);
  static bool get previewMode => webDemoMode || _previewMode;

  static Future<void> initialize() async {
    try {
      final preferences = await SharedPreferences.getInstance();
      final saved = normalizeApiUrl(
        preferences.getString(_apiPreferenceKey) ?? '',
      );
      _savedApiBaseUrl = isValidApiUrl(saved) ? saved : '';
      _previewMode = preferences.getBool(_previewPreferenceKey) ?? false;
    } catch (_) {
      _savedApiBaseUrl = '';
      _previewMode = false;
    }
  }

  static Future<void> saveApiBaseUrl(String value) async {
    final normalized = normalizeApiUrl(value);
    if (!isValidApiUrl(normalized)) {
      throw const FormatException(
        'Use HTTPS, or a private local-network HTTP address, ending in /api/v1.',
      );
    }
    final preferences = await SharedPreferences.getInstance();
    await preferences.setString(_apiPreferenceKey, normalized);
    await preferences.setBool(_previewPreferenceKey, false);
    _savedApiBaseUrl = normalized;
    _previewMode = false;
  }

  static Future<void> enablePreviewMode() async {
    final preferences = await SharedPreferences.getInstance();
    await preferences.setBool(_previewPreferenceKey, true);
    _previewMode = true;
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

  static bool isValidPrivateLanApiUrl(String raw) {
    final value = normalizeApiUrl(raw);
    final uri = Uri.tryParse(value);
    if (uri == null ||
        uri.scheme != 'http' ||
        uri.host.isEmpty ||
        uri.userInfo.isNotEmpty ||
        uri.hasQuery ||
        uri.hasFragment ||
        !uri.path.endsWith('/api/v1')) {
      return false;
    }

    final host = uri.host.toLowerCase();
    if (host == 'localhost' || host == '127.0.0.1' || host == '10.0.2.2') {
      return true;
    }
    final parts = host.split('.').map(int.tryParse).toList();
    if (parts.length != 4 ||
        parts.any((part) => part == null || part < 0 || part > 255)) {
      return false;
    }
    final first = parts[0]!;
    final second = parts[1]!;
    return first == 10 ||
        (first == 172 && second >= 16 && second <= 31) ||
        (first == 192 && second == 168);
  }

  static bool isValidApiUrl(String raw) {
    return isValidPublicApiUrl(raw) || isValidPrivateLanApiUrl(raw);
  }

  static const webDemoMode = bool.fromEnvironment(
    'WEB_DEMO_MODE',
    defaultValue: false,
  );
}
