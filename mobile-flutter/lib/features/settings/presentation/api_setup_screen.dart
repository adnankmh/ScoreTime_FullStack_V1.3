import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:scoretime/core/config/app_config.dart';

class ApiSetupScreen extends StatefulWidget {
  const ApiSetupScreen({super.key, this.onConfigured});

  final VoidCallback? onConfigured;

  @override
  State<ApiSetupScreen> createState() => _ApiSetupScreenState();
}

class _ApiSetupScreenState extends State<ApiSetupScreen> {
  late final TextEditingController controller;
  bool busy = false;
  String? message;
  bool success = false;

  @override
  void initState() {
    super.initState();
    controller = TextEditingController(text: AppConfig.apiBaseUrl);
  }

  @override
  void dispose() {
    controller.dispose();
    super.dispose();
  }

  Future<void> save({required bool verify}) async {
    if (busy) {
      return;
    }
    final normalized = AppConfig.normalizeApiUrl(controller.text);
    if (!AppConfig.isValidPublicApiUrl(normalized)) {
      setState(() {
        success = false;
        message = 'أدخل رابط HTTPS حقيقياً ينتهي بـ /api/v1\n'
            'Enter a real HTTPS URL ending in /api/v1';
      });
      return;
    }

    setState(() {
      busy = true;
      success = false;
      message = verify
          ? 'جارٍ التحقق من خادم ScoreTime… / Checking ScoreTime server…'
          : null;
    });

    try {
      if (verify) {
        final response = await Dio(
          BaseOptions(
            baseUrl: normalized,
            connectTimeout: const Duration(seconds: 10),
            receiveTimeout: const Duration(seconds: 10),
            headers: const {'Accept': 'application/json'},
          ),
        ).get('/data-status');
        if (response.statusCode != 200 || response.data is! Map) {
          throw StateError('Unexpected API response');
        }
      }
      await AppConfig.saveApiBaseUrl(normalized);
      if (!mounted) {
        return;
      }
      setState(() {
        busy = false;
        success = true;
        message = 'تم حفظ الاتصال بنجاح. / Connection saved successfully.';
      });
      widget.onConfigured?.call();
    } on DioException catch (error) {
      if (!mounted) {
        return;
      }
      setState(() {
        busy = false;
        success = false;
        message = 'تعذر الوصول إلى الخادم الآن '
            '(${error.response?.statusCode ?? error.type.name}).\n'
            'تأكد من الرابط وHTTPS وإعداد CORS، أو احفظه دون اختبار.';
      });
    } on FormatException catch (error) {
      if (!mounted) {
        return;
      }
      setState(() {
        busy = false;
        success = false;
        message = error.message;
      });
    } catch (_) {
      if (!mounted) {
        return;
      }
      setState(() {
        busy = false;
        success = false;
        message = 'لم يرجع الخادم استجابة ScoreTime صالحة. '
            '/ Invalid server response.';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 560),
              child: Card(
                child: Padding(
                  padding: const EdgeInsets.all(24),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      Align(
                        child: Image.asset(
                          'assets/icons/scoretime_icon.png',
                          width: 82,
                          height: 82,
                        ),
                      ),
                      const SizedBox(height: 16),
                      Text(
                        'ربط ScoreTime بالخادم',
                        textAlign: TextAlign.center,
                        style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                              fontWeight: FontWeight.w900,
                            ),
                      ),
                      const SizedBox(height: 6),
                      const Text(
                        'Connect to your Laravel server',
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 22),
                      TextField(
                        controller: controller,
                        keyboardType: TextInputType.url,
                        autocorrect: false,
                        enableSuggestions: false,
                        decoration: const InputDecoration(
                          labelText: 'Laravel API URL',
                          hintText: 'https://api.your-domain.com/api/v1',
                          prefixIcon: Icon(Icons.cloud_done_rounded),
                          helperText: 'يمكنك لصق الرابط العادي أو رابط Markdown.',
                        ),
                      ),
                      const SizedBox(height: 18),
                      FilledButton.icon(
                        onPressed: busy ? null : () => save(verify: true),
                        icon: busy
                            ? const SizedBox.square(
                                dimension: 18,
                                child: CircularProgressIndicator(strokeWidth: 2),
                              )
                            : const Icon(Icons.verified_rounded),
                        label: const Text('اختبار وحفظ / Test & save'),
                      ),
                      TextButton(
                        onPressed: busy ? null : () => save(verify: false),
                        child: const Text('حفظ دون اختبار / Save without test'),
                      ),
                      if (message != null) ...[
                        const SizedBox(height: 12),
                        Semantics(
                          liveRegion: true,
                          child: Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: (success ? Colors.green : Colors.orange)
                                  .withValues(alpha: .12),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Text(message!, textAlign: TextAlign.center),
                          ),
                        ),
                      ],
                      const SizedBox(height: 14),
                      const Text(
                        'مفتاح API‑Football يبقى داخل Laravel ولا يوضع هنا أو داخل APK.\n'
                        'The provider key stays on Laravel and never inside the APK.',
                        textAlign: TextAlign.center,
                        style: TextStyle(fontSize: 12),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
