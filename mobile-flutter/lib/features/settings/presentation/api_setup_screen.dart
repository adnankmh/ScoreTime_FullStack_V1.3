import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:scoretime/core/config/app_config.dart';
import 'package:scoretime/core/i18n/app_strings.dart';

class ApiSetupScreen extends StatefulWidget {
  const ApiSetupScreen({super.key, this.onConfigured});

  final VoidCallback? onConfigured;

  @override
  State<ApiSetupScreen> createState() => _ApiSetupScreenState();
}

class _ApiSetupScreenState extends State<ApiSetupScreen> {
  late final TextEditingController controller;
  bool busy = false;
  bool success = false;
  String? message;

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

  Future<void> openPreview() async {
    if (busy) {
      return;
    }
    setState(() => busy = true);
    await AppConfig.enablePreviewMode();
    if (!mounted) {
      return;
    }
    widget.onConfigured?.call();
  }

  Future<void> save({required bool verify}) async {
    if (busy) {
      return;
    }
    final normalized = AppConfig.normalizeApiUrl(controller.text);
    final t = AppStrings.of(context);
    final valid = kIsWeb
        ? AppConfig.isValidPublicApiUrl(normalized)
        : AppConfig.isValidApiUrl(normalized);
    if (!valid) {
      setState(() {
        success = false;
        message = kIsWeb
            ? '${t('invalid_url_web')}\nhttps://api.your-domain.com/api/v1'
            : '${t('invalid_url')}\nhttp://192.168.1.25:8000/api/v1';
      });
      return;
    }

    setState(() {
      busy = true;
      success = false;
      message = verify ? t('checking_server') : null;
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
        message = t('connection_saved');
      });
      widget.onConfigured?.call();
    } on DioException catch (error) {
      if (!mounted) {
        return;
      }
      setState(() {
        busy = false;
        success = false;
        message = '${t('cannot_reach')} '
            '(${error.response?.statusCode ?? error.type.name})';
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
        message = t('invalid_response');
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final colors = Theme.of(context).colorScheme;
    final t = AppStrings.of(context);
    return Scaffold(
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 620),
              child: Card(
                child: Padding(
                  padding: const EdgeInsets.all(22),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      Align(
                        child: Image.asset(
                          'assets/icons/scoretime_icon.png',
                          width: 78,
                          height: 78,
                        ),
                      ),
                      const SizedBox(height: 14),
                      Text(
                        t('setup_title'),
                        textAlign: TextAlign.center,
                        style: Theme.of(context)
                            .textTheme
                            .headlineSmall
                            ?.copyWith(fontWeight: FontWeight.w900),
                      ),
                      const SizedBox(height: 6),
                      Text(
                        t('setup_intro'),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 20),
                      _OptionCard(
                        icon: Icons.play_circle_fill_rounded,
                        title: t('preview_title'),
                        description: t('preview_desc'),
                        child: FilledButton.icon(
                          onPressed: busy ? null : openPreview,
                          icon: const Icon(Icons.rocket_launch_rounded),
                          label: Text(t('preview_button')),
                        ),
                      ),
                      const SizedBox(height: 12),
                      _OptionCard(
                        icon: Icons.lan_rounded,
                        title: kIsWeb
                            ? t('connect_title_web')
                            : t('connect_title'),
                        description: kIsWeb
                            ? t('connect_desc_web')
                            : t('connect_desc'),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            TextField(
                              controller: controller,
                              keyboardType: TextInputType.url,
                              autocorrect: false,
                              enableSuggestions: false,
                              decoration: InputDecoration(
                                labelText: 'Laravel API URL',
                                hintText: kIsWeb
                                    ? 'https://api.your-domain.com/api/v1'
                                    : 'http://192.168.1.25:8000/api/v1',
                                prefixIcon:
                                    const Icon(Icons.cloud_done_rounded),
                              ),
                            ),
                            const SizedBox(height: 12),
                            FilledButton.tonalIcon(
                              onPressed:
                                  busy ? null : () => save(verify: true),
                              icon: busy
                                  ? const SizedBox.square(
                                      dimension: 18,
                                      child: CircularProgressIndicator(
                                        strokeWidth: 2,
                                      ),
                                    )
                                  : const Icon(Icons.verified_rounded),
                              label: Text(t('test_save')),
                            ),
                          ],
                        ),
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
                      Text(
                        t('security_note'),
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          color: colors.onSurfaceVariant,
                          fontSize: 12,
                        ),
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

class _OptionCard extends StatelessWidget {
  const _OptionCard({
    required this.icon,
    required this.title,
    required this.description,
    required this.child,
  });

  final IconData icon;
  final String title;
  final String description;
  final Widget child;

  @override
  Widget build(BuildContext context) {
    final colors = Theme.of(context).colorScheme;
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: colors.surfaceContainerHighest.withValues(alpha: .45),
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: colors.outlineVariant),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Row(
            children: [
              Icon(icon, color: colors.primary),
              const SizedBox(width: 9),
              Expanded(
                child: Text(
                  title,
                  style: const TextStyle(fontWeight: FontWeight.w900),
                ),
              ),
            ],
          ),
          const SizedBox(height: 6),
          Text(description, style: Theme.of(context).textTheme.bodySmall),
          const SizedBox(height: 12),
          child,
        ],
      ),
    );
  }
}
