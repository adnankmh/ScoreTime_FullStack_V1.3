class AppConfig {
  static const apiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://10.0.2.2:8000/api/v1',
  );

  static const webDemoMode = bool.fromEnvironment(
    'WEB_DEMO_MODE',
    defaultValue: false,
  );
}
