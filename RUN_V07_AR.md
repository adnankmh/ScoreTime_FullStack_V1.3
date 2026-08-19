# تشغيل ScoreTime V0.7 — بدون Android Studio

## أسهل طريقة لبناء التطبيق
1. ارفع المشروع كاملًا إلى GitHub.
2. افتح **Actions**.
3. اختر **V0.7 Full Validation & APK**.
4. اضغط **Run workflow**.
5. اكتب رابط Laravel API، مثال: `https://example.com/api/v1`.
6. انتظر نجاح Laravel checks وFlutter analyze/test/build.
7. نزّل Artifact باسم `ScoreTime-V07-APK` وثبّت `app-release.apk` على الهاتف.

> لا تحتاج Android Studio. GitHub يشغّل Android SDK وFlutter في السحابة.

## تشغيل الموقع محليًا
تحتاج PHP 8.2+ وComposer وMySQL/MariaDB أو SQLite.

```bash
cd backend-laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

افتح `http://127.0.0.1:8000`.

## المدير الأولي
- Username: `Adnan`
- Password: `Adnan123`

غيّر كلمة المرور فورًا قبل أي نشر عام، ثم فعّل 2FA من لوحة المدير.

## Realtime
V0.7 يقدّم `/api/v1/matches/{id}/realtime` وSSE عبر `/api/v1/matches/{id}/stream`. هذا يعمل كطبقة realtime مباشرة بدون Android Studio أو خدمات إضافية، ويمكن استبداله/دعمه بـWebSockets عند النشر واسع النطاق.

## Firebase Push
ضع ملف Service Account خارج public directory ثم أضف مساره في `.env`:
`FIREBASE_CREDENTIALS=/secure/path/firebase-service-account.json`
لا ترفع ملف المفاتيح إلى GitHub.

## مزود بيانات كرة القدم
الوضع الافتراضي Demo. للنشر يجب ربط مزود بيانات مرخص في طبقة `FootballDataProvider` وعدم Scraping مواقع المنافسين.


## تفعيل API-Football اختياريًا
في `.env`:
```env
FOOTBALL_DATA_PROVIDER=api-football
FOOTBALL_DATA_BASE_URL=https://v3.football.api-sports.io
FOOTBALL_DATA_API_KEY=YOUR_PRIVATE_KEY
```
ثم لمزامنة المباريات الحية الموجودة والمربوطة بـ`provider_id`:
```bash
php artisan football:sync-live --events
```
يمكن تشغيل هذا الأمر كل دقيقة عبر Laravel Scheduler في الإنتاج. لا تضع المفتاح داخل Flutter ولا داخل GitHub العام؛ المفتاح يبقى في Laravel فقط.
