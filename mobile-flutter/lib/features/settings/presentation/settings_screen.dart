import '../../worldclass/presentation/v07_hub_screen.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:scoretime/core/i18n/app_strings.dart';
import 'package:scoretime/core/theme/theme_controller.dart';
import 'package:scoretime/features/auth/presentation/login_screen.dart';
import 'package:scoretime/features/worldclass/presentation/world_class_screen.dart';
import 'package:scoretime/features/social/presentation/social_hub_screen.dart';
import 'package:scoretime/features/world/presentation/global_football_screen.dart';

class SettingsScreen extends ConsumerWidget {
  const SettingsScreen({super.key});
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final s = ref.watch(themeControllerProvider);
    final controller = ref.read(themeControllerProvider.notifier);
    final t = AppStrings.of(context);
    return Scaffold(
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.fromLTRB(16, 14, 16, 32),
          children: [
            Row(children: [ClipRRect(borderRadius: BorderRadius.circular(14), child: Image.asset('assets/icons/scoretime_icon.png', width: 46, height: 46)), const SizedBox(width: 11), Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(t('more'), style: Theme.of(context).textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.w900)), Text('ScoreTime • Every moment counts', style: Theme.of(context).textTheme.bodySmall)]))]),
            const SizedBox(height: 22),
            _SectionLabel(t('account')),
            _HubTile(icon: Icons.person_rounded, title: t('login'), subtitle: 'Account, security and synced preferences', onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const LoginScreen()))),
            const SizedBox(height: 20),
            _SectionLabel('Football Hub'),
            _HubTile(icon: Icons.public_rounded, title: t('world'), subtitle: 'Countries • competitions • clubs • players', onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const GlobalFootballScreen()))),
            _HubTile(icon: Icons.insights_rounded, title: 'Football Intelligence', subtitle: 'Leaders • notifications • mini leagues • data provider', onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const WorldClassScreen()))),
            _HubTile(icon: Icons.groups_rounded, title: 'My Football', subtitle: 'Friends • achievements • devices • sessions', onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const SocialHubScreen()))),
            _HubTile(icon: Icons.public_rounded, title: 'Realtime Operations Hub', subtitle: 'Transfer intelligence • prediction seasons • global modules', onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const V07HubScreen()))),
            const SizedBox(height: 22),
            _SectionLabel(t('theme')),
            SegmentedButton<AppThemeName>(
              segments: [
                ButtonSegment(value: AppThemeName.stadium, label: Text(t('stadium')), icon: const Icon(Icons.stadium_rounded)),
                ButtonSegment(value: AppThemeName.midnight, label: Text(t('midnight')), icon: const Icon(Icons.dark_mode_rounded)),
                ButtonSegment(value: AppThemeName.light, label: Text(t('light')), icon: const Icon(Icons.light_mode_rounded)),
              ],
              selected: {s.theme},
              onSelectionChanged: (v) => controller.setTheme(v.first),
            ),
            const SizedBox(height: 18),
            Text('${t('font')}: ${(s.fontScale * 100).round()}%', style: const TextStyle(fontWeight: FontWeight.w800)),
            Slider(value: s.fontScale, min: .85, max: 1.30, divisions: 9, onChanged: controller.setFont),
            const SizedBox(height: 12),
            DropdownButtonFormField<String>(
              value: s.locale,
              decoration: InputDecoration(labelText: t('language'), prefixIcon: const Icon(Icons.language_rounded)),
              items: const [
                DropdownMenuItem(value: 'en', child: Text('English')),
                DropdownMenuItem(value: 'ar', child: Text('العربية')),
                DropdownMenuItem(value: 'fr', child: Text('Français')),
                DropdownMenuItem(value: 'es', child: Text('Español')),
                DropdownMenuItem(value: 'de', child: Text('Deutsch')),
                DropdownMenuItem(value: 'tr', child: Text('Türkçe')),
              ],
              onChanged: (v) { if (v != null) controller.setLocale(v); },
            ),
            const SizedBox(height: 18),
            Card(child: SwitchListTile(value: true, onChanged: (_) {}, secondary: const Icon(Icons.notifications_active_rounded), title: Text(t('alerts')), subtitle: const Text('Goals • lineups • VAR • red cards • full time • transfers'))),
          ],
        ),
      ),
    );
  }
}

class _SectionLabel extends StatelessWidget {
  final String text;
  const _SectionLabel(this.text);
  @override
  Widget build(BuildContext context) => Padding(padding: const EdgeInsets.only(bottom: 9), child: Text(text.toUpperCase(), style: TextStyle(color: Theme.of(context).colorScheme.secondary, fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1.15)));
}

class _HubTile extends StatelessWidget {
  final IconData icon;
  final String title, subtitle;
  final VoidCallback onTap;
  const _HubTile({required this.icon, required this.title, required this.subtitle, required this.onTap});
  @override
  Widget build(BuildContext context) => Card(
        margin: const EdgeInsets.only(bottom: 9),
        child: ListTile(
          contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
          leading: Container(width: 44, height: 44, decoration: BoxDecoration(borderRadius: BorderRadius.circular(14), color: Theme.of(context).colorScheme.primary.withOpacity(.12)), child: Icon(icon, color: Theme.of(context).colorScheme.secondary)),
          title: Text(title, style: const TextStyle(fontWeight: FontWeight.w900)),
          subtitle: Text(subtitle),
          trailing: const Icon(Icons.chevron_right_rounded),
          onTap: onTap,
        ),
      );
}
