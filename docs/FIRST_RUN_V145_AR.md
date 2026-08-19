# ScoreTime V1.4.5 — التشغيل لأول مرة

## الأسهل على Windows
شغّل فقط:

`START_SCORETIME_WINDOWS.bat`

الملف يقوم تلقائياً بـ:
- اكتشاف PHP/XAMPP.
- تشغيل `composer install` إذا كان `vendor/` مفقوداً.
- إنشاء `.env` إذا كان مفقوداً.
- إنشاء Laravel `APP_KEY` تلقائياً.
- إعداد Session/Cache للتشغيل المحلي.
- محاولة تشغيل MySQL من XAMPP إذا لم يكن يعمل.
- إنشاء قاعدة `football_global` إذا لم تكن موجودة.
- تشغيل migrations بدون `migrate:fresh`.
- تشغيل seeders.
- تنظيف Laravel cache.
- تشغيل `php artisan serve`.
- فتح `http://127.0.0.1:8000` تلقائياً.

## GitHub Desktop
إذا كنت تستعمل نفس Repository القديم ولا تريد إنشاء Repository جديد:
1. انسخ ملفات V1.4.5 فوق المشروع.
2. شغّل `CLEAN_OLD_GITHUB_FILES.bat` مرة واحدة.
3. افتح GitHub Desktop.
4. Commit ثم Push.

هذا يحذف Workflows القديمة النشطة والاختبارات القديمة التي قد تبقى من نسخ سابقة.
