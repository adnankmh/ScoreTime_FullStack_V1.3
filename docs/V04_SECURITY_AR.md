# V0.4 Security Notes

- لا يوجد نظام يمكن ضمان أنه غير قابل للاختراق 100%. التصميم يعتمد Defense in Depth.
- صلاحيات المدير Server-side Middleware، وليست مجرد UI hiding.
- تفعيل 2FA TOTP للمدير مع نافذة زمنية صغيرة وRecovery Codes أحادية الاستخدام.
- Recovery Codes تخزن باستخدام password hashing.
- سر TOTP مشفر بواسطة Laravel Crypt ويعتمد أمانه على APP_KEY، لذلك يجب حماية APP_KEY وعدم رفع `.env` إلى Git.
- Auth API عبر Sanctum، والتوكن في Flutter داخل flutter_secure_storage.
- Rate limits على login/predictions/fan-room/mini-league creation.
- CORS يجب أن يكون allow-list في الإنتاج.
- استخدم HTTPS فقط، secure cookies، HSTS وreverse proxy مضبوط بشكل صحيح.
- لا تستخدم ADMIN_PASSWORD الافتراضي في الإنتاج.
- مفاتيح Football API وFirebase server credentials تبقى Server-side أو في GitHub Secrets.
- شغّل تحديثات Composer/Flutter وفحوص dependency vulnerabilities دوريًا.
