import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:scoretime/core/i18n/app_strings.dart';
import 'package:scoretime/core/network/auth_repository.dart';
import 'package:scoretime/core/theme/theme_controller.dart';

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
      body: Stack(
        children: [
          Positioned.fill(
            child: DecoratedBox(
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [Color(0xFF020817), Color(0xFF061B39), Color(0xFF020817)],
                ),
                boxShadow: [
                  BoxShadow(color: ScoreTimeColors.blue.withValues(alpha: .16), blurRadius: 120, spreadRadius: 20),
                ],
              ),
            ),
          ),
          SafeArea(
            child: Center(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(22),
                child: ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 470),
                  child: Container(
                    padding: const EdgeInsets.all(26),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(28),
                      color: const Color(0xFF07162D).withValues(alpha: .92),
                      border: Border.all(color: Colors.white.withValues(alpha: .09)),
                      boxShadow: const [BoxShadow(color: Colors.black45, blurRadius: 40, offset: Offset(0, 18))],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Align(
                          alignment: Alignment.centerLeft,
                          child: IconButton(onPressed: () => Navigator.maybePop(context), icon: const Icon(Icons.arrow_back_rounded)),
                        ),
                        Center(
                          child: Container(
                            padding: const EdgeInsets.all(3),
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(25),
                              gradient: const LinearGradient(colors: [ScoreTimeColors.blue, ScoreTimeColors.cyan]),
                            ),
                            child: ClipRRect(
                              borderRadius: BorderRadius.circular(22),
                              child: Image.asset('assets/icons/scoretime_icon.png', width: 82, height: 82),
                            ),
                          ),
                        ),
                        const SizedBox(height: 18),
                        Text('Welcome Back', textAlign: TextAlign.center, style: Theme.of(context).textTheme.headlineMedium?.copyWith(fontWeight: FontWeight.w900)),
                        const SizedBox(height: 6),
                        Text('Sign in to your ScoreTime world', textAlign: TextAlign.center, style: TextStyle(color: Theme.of(context).colorScheme.onSurfaceVariant)),
                        const SizedBox(height: 28),
                        TextField(controller: login, autofillHints: const [AutofillHints.username, AutofillHints.email], decoration: InputDecoration(labelText: t('email_user'), prefixIcon: const Icon(Icons.alternate_email_rounded))),
                        const SizedBox(height: 12),
                        TextField(controller: password, obscureText: hide, autofillHints: const [AutofillHints.password], onSubmitted: (_) => submit(), decoration: InputDecoration(labelText: t('password'), prefixIcon: const Icon(Icons.lock_outline_rounded), suffixIcon: IconButton(onPressed: () => setState(() => hide = !hide), icon: Icon(hide ? Icons.visibility_rounded : Icons.visibility_off_rounded)))),
                        if (error != null) Padding(padding: const EdgeInsets.only(top: 12), child: Text(error!, textAlign: TextAlign.center, style: TextStyle(color: Theme.of(context).colorScheme.error))),
                        const SizedBox(height: 18),
                        FilledButton.icon(onPressed: busy ? null : submit, icon: busy ? const SizedBox.square(dimension: 18, child: CircularProgressIndicator(strokeWidth: 2)) : const Icon(Icons.login_rounded), label: Padding(padding: const EdgeInsets.symmetric(vertical: 3), child: Text(t('login')))),
                        const SizedBox(height: 9),
                        OutlinedButton.icon(onPressed: () {}, icon: const Icon(Icons.person_add_alt_1_rounded), label: Text(t('register'))),
                        const SizedBox(height: 16),
                        const Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.verified_user_rounded, size: 15, color: ScoreTimeColors.green),
                            SizedBox(width: 6),
                            Text('Protected sign-in • secure token storage', style: TextStyle(fontSize: 9.5, color: Colors.white54)),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
