# V1.0 — Production Security

لا يوجد نظام يمكن ضمان أنه غير قابل للاختراق 100%. V1.0 يستخدم Defense-in-Depth ويضيف طبقات تمنع فئات واسعة من الأخطاء الشائعة:

- Laravel authentication + Sanctum.
- Password hashing بواسطة Laravel.
- CSRF لواجهات الويب.
- Login/API rate limiting.
- Admin middleware server-side.
- تقييد Super Admin افتراضيًا إلى `Adnan` عبر `ADMIN_SUPERUSER_ONLY`.
- 2FA + Recovery Codes للإدارة، ويمكن فرضه في Production.
- Optional admin IP allowlist.
- Security headers: HSTS في Production, CSP, nosniff, SAMEORIGIN, Referrer Policy, Permissions Policy.
- Secure/SameSite session cookie configuration.
- Audit logs وDevice Sessions.
- API keys server-side فقط.
- FCM HTTP v1 service credentials server-side فقط.
- No-Code Studio لا ينفذ PHP/Dart/JavaScript arbitrary code.

قبل Production يجب تغيير Bootstrap password `Adnan123` لأنها ليست آمنة بعد مشاركتها/توثيقها.
