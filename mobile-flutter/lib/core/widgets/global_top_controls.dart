import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../i18n/app_strings.dart';
import '../theme/theme_controller.dart';

class GlobalTopControls extends ConsumerWidget {
  final bool compact;
  const GlobalTopControls({super.key, this.compact = false});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final settings = ref.watch(themeControllerProvider);
    final controller = ref.read(themeControllerProvider.notifier);
    final t = AppStrings.of(context);

    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        PopupMenuButton<AppThemeName>(
          tooltip: t('quick_theme'),
          initialValue: settings.theme,
          onSelected: controller.setTheme,
          itemBuilder: (_) => [
            PopupMenuItem(value: AppThemeName.stadium, child: Text(t('stadium'))),
            PopupMenuItem(value: AppThemeName.midnight, child: Text(t('midnight'))),
            PopupMenuItem(value: AppThemeName.light, child: Text(t('light'))),
          ],
          child: _TopPill(
            icon: settings.theme == AppThemeName.light
                ? Icons.light_mode_rounded
                : settings.theme == AppThemeName.midnight
                    ? Icons.nightlight_round
                    : Icons.stadium_rounded,
            label: compact ? null : t('theme'),
          ),
        ),
        const SizedBox(width: 8),
        PopupMenuButton<String>(
          tooltip: t('language'),
          initialValue: settings.locale,
          onSelected: controller.setLocale,
          itemBuilder: (_) => const [
            PopupMenuItem(value: 'en', child: Text('English')),
            PopupMenuItem(value: 'ar', child: Text('العربية')),
            PopupMenuItem(value: 'fr', child: Text('Français')),
            PopupMenuItem(value: 'es', child: Text('Español')),
            PopupMenuItem(value: 'de', child: Text('Deutsch')),
            PopupMenuItem(value: 'tr', child: Text('Türkçe')),
          ],
          child: _TopPill(
            icon: Icons.language_rounded,
            label: compact ? settings.locale.toUpperCase() : settings.locale.toUpperCase(),
          ),
        ),
      ],
    );
  }
}

class _TopPill extends StatelessWidget {
  final IconData icon;
  final String? label;
  const _TopPill({required this.icon, this.label});

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 38,
      padding: const EdgeInsets.symmetric(horizontal: 11),
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.surface.withValues(alpha: .82),
        borderRadius: BorderRadius.circular(13),
        border: Border.all(color: Theme.of(context).dividerColor),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 18),
          if (label != null) ...[
            const SizedBox(width: 7),
            Text(label!, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 12)),
          ],
        ],
      ),
    );
  }
}
