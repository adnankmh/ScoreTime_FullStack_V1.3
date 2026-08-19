import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:scoretime/core/i18n/app_strings.dart';
import 'package:scoretime/core/network/auth_repository.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});
  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final login = TextEditingController();
  final password = TextEditingController();
  bool hide = true, busy = false;
  String? error;

  Future<void> submit() async {
    setState(() { busy = true; error = null; });
    try {
      await AuthRepository().login(login.text.trim(), password.text);
      if (mounted) Navigator.pop(context, true);
    } on DioException catch (e) {
      setState(() => error = e.response?.data?['message']?.toString() ?? 'Sign in failed');
    } catch (_) {
      setState(() => error = 'Sign in failed');
    } finally {
      if (mounted) setState(() => busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final t = AppStrings.of(context);
    return Scaffold(
      body: SafeArea(
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 480),
            child: ListView(
              padding: const EdgeInsets.fromLTRB(24, 32, 24, 24),
              children: [
                Align(alignment: Alignment.centerLeft, child: IconButton(onPressed: () => Navigator.maybePop(context), icon: const Icon(Icons.arrow_back_rounded))),
                const SizedBox(height: 8),
                Center(child: ClipRRect(borderRadius: BorderRadius.circular(28), child: Image.asset('assets/icons/scoretime_icon.png', width: 110, height: 110))),
                const SizedBox(height: 18),
                Text('Welcome to ScoreTime', textAlign: TextAlign.center, style: Theme.of(context).textTheme.headlineMedium?.copyWith(fontWeight: FontWeight.w900)),
                const SizedBox(height: 7),
                Text('Every moment counts.', textAlign: TextAlign.center, style: Theme.of(context).textTheme.bodyMedium?.copyWith(color: Theme.of(context).colorScheme.secondary)),
                const SizedBox(height: 30),
                TextField(controller: login, autofillHints: const [AutofillHints.username, AutofillHints.email], decoration: InputDecoration(labelText: t('email_user'), prefixIcon: const Icon(Icons.person_outline_rounded))),
                const SizedBox(height: 13),
                TextField(controller: password, obscureText: hide, autofillHints: const [AutofillHints.password], onSubmitted: (_) => submit(), decoration: InputDecoration(labelText: t('password'), prefixIcon: const Icon(Icons.lock_outline_rounded), suffixIcon: IconButton(onPressed: () => setState(() => hide = !hide), icon: Icon(hide ? Icons.visibility_rounded : Icons.visibility_off_rounded)))),
                if (error != null) Padding(padding: const EdgeInsets.only(top: 12), child: Text(error!, textAlign: TextAlign.center, style: TextStyle(color: Theme.of(context).colorScheme.error))),
                const SizedBox(height: 18),
                FilledButton.icon(onPressed: busy ? null : submit, icon: busy ? const SizedBox.square(dimension: 18, child: CircularProgressIndicator(strokeWidth: 2)) : const Icon(Icons.login_rounded), label: Padding(padding: const EdgeInsets.symmetric(vertical: 3), child: Text(t('login')))),
                const SizedBox(height: 9),
                OutlinedButton(onPressed: () {}, child: Text(t('register'))),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
