# ScoreTime V1.4 — Security

لا يوجد تطبيق يمكن ضمان أنه غير قابل للاختراق 100%. ScoreTime يستخدم Defense-in-Depth ويمنع لوحة الإدارة من كتابة PHP/Dart/JavaScript عشوائيًا.

- Hashing عبر Laravel للمPasswords.
- Web CSRF + encrypted cookies.
- Sanctum tokens لتطبيق Flutter مع flutter_secure_storage.
- Rate limits لتسجيل الدخول والعمليات الحساسة.
- Admin middleware + optional IP allowlist + TOTP 2FA + recovery codes.
- Audit logs وdevice/session controls.
- CSP/HSTS/anti-sniff/frame/security headers في production.
- مفاتيح Football Provider وFirebase تبقى على Laravel server ولا تدخل التطبيق.
- News ingestion يسمح فقط بمصادر licensed / rss-permitted / partner مع attribution ومراجعة تحريرية افتراضية.

قبل النشر: غيّر كلمة مرور Adnan bootstrap، فعّل HTTPS و2FA، استخدم production secrets، Redis/queues عند الحاجة، backups واختبارات staging.
