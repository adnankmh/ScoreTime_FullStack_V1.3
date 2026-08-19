# ScoreTime Global V1.4

نسخة موحدة جديدة للموقع والتطبيق بهوية **ScoreTime** المعتمدة وشعار **Every Moment Counts**.

## المجلدات الرئيسية
- `backend-laravel/` — الموقع + REST API + Adnan Control Room.
- `mobile-flutter/` — تطبيق Flutter.
- `branding/` — الشعار والأيقونة المعتمدان.
- `docs/` — وثائق الإصدار الحالي فقط.
- `archive/legacy-releases/` — كل ملفات وWorkflows ووثائق الإصدارات القديمة في مكان واحد.
- `.github/workflows/` — Workflowان فقط للإصدار الحالي.

## أهم الإصلاحات في هذا الإصدار
- `intl ^0.20.3` متوافق مع Flutter localization الحالي.
- إلغاء الاعتماد على `php artisan test` في CI واستخدام `vendor/bin/phpunit`.
- Workflow واحد للجودة وWorkflow واحد لبناء Android APK/AAB.
- إذا كان `android/` غير موجود، GitHub Actions ينشئه تلقائيًا ثم يطبق أيقونة ScoreTime.
- إضافة `public/index.php` وملفات Laravel runtime المطلوبة.
- Local `.env.example`: English افتراضيًا + file sessions/cache + sync queue لتشغيل أسهل.
- Migrations الزمنية الحساسة معدلة لتوافق MariaDB/XAMPP.
- Seeders أصبحت idempotent لتقليل أخطاء Duplicate key.
- ترجمة الواجهة للموقع والتطبيق: English / العربية / Français / Español / Deutsch / Türkçe.

## التشغيل
راجع:
- `docs/LOCAL_RUN_AR.md`
- `docs/GITHUB_DESKTOP_APK_AR.md`

## ملاحظة الأمان
حساب Bootstrap للإدارة `Adnan / Adnan123` مخصص لأول تشغيل فقط. غيّر كلمة المرور وفعّل 2FA قبل النشر الحقيقي.
