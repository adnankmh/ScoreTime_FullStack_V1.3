# تشغيل ScoreTime V0.4 — بدون Android Studio

## الخيار الأسهل للتطبيق: GitHub Actions فقط
لا تحتاج Android Studio لبناء APK. ارفع المشروع إلى GitHub ثم افتح Actions وشغّل workflow باسم **Flutter APK - No Android Studio**. بعد النجاح نزّل Artifact باسم `scoretime-apk` وستجد داخله `app-release.apk`.

### قبل البناء
1. افتح `mobile-flutter/lib/core/config/app_config.dart`.
2. غيّر `API_BASE_URL` إلى رابط Laravel الحقيقي، مثل `https://your-domain.com/api/v1`.
3. إذا أردت Firebase Push حقيقيًا، أضف ملفات إعداد Firebase الخاصة بمشروعك كـGitHub Secrets/ملفات build ولا تضع مفاتيح حساسة علنًا في المستودع.

## تشغيل الموقع Laravel محليًا
المطلوب فقط PHP 8.2+ وComposer وقاعدة MySQL/MariaDB أو SQLite. لا تحتاج Android Studio.

1. افتح Terminal داخل `backend-laravel`.
2. نفّذ `composer install`.
3. انسخ `.env.example` إلى `.env`.
4. عدّل إعدادات قاعدة البيانات في `.env`.
5. نفّذ `php artisan key:generate`.
6. نفّذ `php artisan migrate --seed`.
7. نفّذ `php artisan serve`.
8. افتح `http://127.0.0.1:8000`.

## حساب المدير الأولي
Username: `Adnan`
Password: `Adnan123`

**مهم جدًا:** هذه كلمة Bootstrap فقط. غيّرها فور أول تشغيل وقبل نشر الموقع للعامة، ثم فعّل 2FA من لوحة المدير.

## 2FA للمدير
بعد تسجيل الدخول افتح `/admin/2fa/setup`، أضف المفتاح إلى Google Authenticator أو Microsoft Authenticator أو أي تطبيق TOTP متوافق، ثم أدخل الرمز. ستظهر Recovery Codes لمرة واحدة؛ خزّنها خارج الموقع.

## مزود بيانات كرة القدم
افتراضيًا `FOOTBALL_PROVIDER=demo` حتى يعمل المشروع بدون مفاتيح مدفوعة. طبقة `FootballDataProvider` تفصل التطبيق عن المزود. عند شراء مزود مرخص أضف Adapter جديدًا بدل تعديل Controllers أو Flutter.

## بناء Flutter محليًا بدون Android Studio (اختياري)
يمكن استخدام Flutter SDK وحده + Android command-line tools، لكن هذا أصعب من GitHub Actions. لذلك المسار الموصى به لهذا المشروع هو GitHub Actions لإخراج APK/AAB دون تثبيت Android Studio.
