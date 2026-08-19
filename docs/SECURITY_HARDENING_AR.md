# KoraOne Next — الأمن والتقوية

## ما تم تطبيقه
- فصل صلاحية المدير عن المستخدم العادي عبر `AdminOnly` middleware على الخادم.
- مصادقة Web بالجلسات مع تجديد Session ID بعد الدخول وإبطال الجلسة عند الخروج.
- مصادقة Mobile API بواسطة Laravel Sanctum tokens.
- تحديد محاولات تسجيل الدخول (`5/minute` لكل login+IP) وRate Limit عام للـAPI.
- CSRF على جميع نماذج الويب، وقواعد Validation على التسجيل والدخول ولوحة الأخبار.
- Password hashing بواسطة Laravel casts / bcrypt.
- Audit Log للعمليات الإدارية الحساسة.
- المستخدم المحظور لا يستطيع تسجيل الدخول.
- تخزين Token تطبيق Flutter في `flutter_secure_storage` وليس Shared Preferences.
- CORS allow-list قابلة للضبط من `.env`.

## بيانات المدير الأولية
- Username: `Adnan`
- Password: `Adnan123`
- Email الافتراضي: `adnan@local.test`

> هذه البيانات مطلوبة بناءً على مواصفات المشروع، لكنها **ليست مناسبة للإنتاج** لأنها معروفة الآن. قبل النشر غيّر `ADMIN_PASSWORD` ثم نفّذ seeding آمن أو غيّر كلمة المرور من قاعدة البيانات/لوحة إدارة الحساب التي ستضاف في الإصدار التالي.

## Production Checklist
1. `APP_ENV=production` و `APP_DEBUG=false`.
2. HTTPS فقط + HSTS من Nginx/Cloudflare.
3. `SESSION_SECURE_COOKIE=true`.
4. مفتاح `APP_KEY` جديد وغير مشترك.
5. مستخدم MySQL بصلاحيات قاعدة المشروع فقط؛ لا تستخدم root.
6. Firewall: افتح 80/443 فقط للعامة، وقاعدة البيانات داخليًا فقط.
7. نسخ احتياطي مشفر واختبار الاستعادة دوريًا.
8. تدوير أسرار API ومفاتيح Firebase وعدم رفعها إلى Git.
9. تحديث Laravel/Flutter dependencies بعد اختبار CI.
10. إضافة WAF/Cloudflare Rate Limiting للإنتاج ذي الحمل العالي.
11. تفعيل MFA لحساب المدير في مرحلة hardening التالية.
12. لا يوجد نظام “غير قابل للاختراق 100%”؛ الهدف هو Defense in Depth وتقليل سطح الهجوم والاستجابة السريعة للحوادث.
