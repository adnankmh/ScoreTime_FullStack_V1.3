# تشغيل V0.5 بسهولة — بدون Android Studio

## أسهل طريقة للتطبيق
1. ارفع المشروع إلى GitHub.
2. افتح **Actions**.
3. شغّل Workflow الخاص بـ Flutter APK.
4. GitHub سيشغّل `flutter pub get` ثم `flutter analyze` ثم الاختبارات ثم `flutter build apk --release`.
5. بعد النجاح حمّل Artifact باسم `scoretime-apk` وثبّت `app-release.apk` على هاتفك.

بهذه الطريقة لا تحتاج Android Studio على جهازك.

## الموقع محليًا
داخل `backend-laravel`:
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
ثم افتح `http://127.0.0.1:8000`.

## المدير
الحساب الأولي: `Adnan` / `Adnan123`. غيّر كلمة المرور فور أول دخول، ثم فعّل 2FA من لوحة الإدارة.

## بيانات الكرة الحقيقية
ابدأ بـ `FOOTBALL_PROVIDER=demo`. عند شراء مزود مرخّص أضف Adapter جديد ولا تضع API Key داخل الكود.
