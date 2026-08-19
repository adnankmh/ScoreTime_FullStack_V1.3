# تشغيل ScoreTime V0.6 بسهولة — بدون Android Studio

## أسهل طريقة لبناء التطبيق
1. ارفع مجلد المشروع كاملًا إلى GitHub.
2. افتح تبويب **Actions**.
3. اختر **V0.6 Full Validation and APK**.
4. اضغط **Run workflow**.
5. ضع رابط API الخاص بموقع Laravel، مثل: `https://example.com/api/v1`.
6. انتظر نجاح مهمتي Laravel وFlutter.
7. افتح نتيجة الـWorkflow ثم **Artifacts**.
8. حمّل **ScoreTime-V06-APK**.
9. ستجد داخله `app-release.apk`، انقله إلى هاتف Android وثبته.

> لا تحتاج Android Studio على جهازك بهذه الطريقة. GitHub يقوم ببناء APK في خوادمه.

## تشغيل الموقع محليًا
تحتاج PHP 8.2+ وComposer فقط:

```bash
cd backend-laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

ثم افتح `http://127.0.0.1:8000`.

## حساب المدير الأولي
- Username: `Adnan`
- Password: `Adnan123`

غيّر كلمة المرور فورًا قبل نشر الموقع، وفعّل 2FA من لوحة المدير.

## Live Data
الوضع الافتراضي Demo/Provider-ready. اربط مزود بيانات مرخص ثم اضبط متغيرات `.env`. ميزات Shot Map وxG وMomentum وLineups تستخدم بيانات المزود عند توافرها.
