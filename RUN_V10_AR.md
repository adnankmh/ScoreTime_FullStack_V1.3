# تشغيل ScoreTime V1.0 — بدون Android Studio

## أولا: الموقع Laravel
1. فك الضغط.
2. افتح الطرفية داخل `backend-laravel`.
3. نفذ `composer install`.
4. انسخ `.env.example` إلى `.env`.
5. اضبط قاعدة البيانات ثم نفذ:
   - `php artisan key:generate`
   - `php artisan migrate --seed`
   - `php artisan storage:link`
   - `php artisan serve`
6. افتح `http://127.0.0.1:8000`.

حساب bootstrap للإدارة: `Adnan / Adnan123`. غيّر كلمة المرور فورًا قبل النشر، وفعّل 2FA. في الإنتاج اجعل `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, `ADMIN_REQUIRE_MFA=true`.

## البيانات الكروية الحقيقية
المشروع لا ينسخ بيانات مواقع أخرى. ضع مفتاح مزود مرخص في `.env`:

```env
FOOTBALL_PROVIDER=api-football
FOOTBALL_DATA_BASE_URL=https://v3.football.api-sports.io
FOOTBALL_DATA_API_KEY=YOUR_KEY
FOOTBALL_SYNC_ENABLED=true
```

ثم:

```bash
php artisan football:sync-global catalog --season=2026
php artisan football:sync-global league --league=PROVIDER_LEAGUE_ID --season=2026
php artisan football:sync-global players --league=PROVIDER_LEAGUE_ID --season=2026 --pages=20
php artisan football:sync-live --events
```

أو من لوحة المدير: `/admin/world-data`.

> كل دوري/موسم له Coverage مختلفة لدى المزود؛ النظام يخزن Coverage ويجب عدم افتراض أن كل إحصائية موجودة لكل بطولة.

## بناء APK بدون Android Studio
ارفع المشروع إلى GitHub ثم:
`Actions → V1.0 Production Validation & APK → Run workflow`.
أدخل `api_base_url` مثل `https://example.com/api/v1`.
بعد نجاح البناء نزّل Artifact باسم `ScoreTime-V10-APK` وستجد `app-release.apk`.

## التشغيل على الهاتف بدون Android Studio
يكفي APK الناتج من GitHub Actions. انقله للهاتف وثبته. لا تحتاج Android Studio.

## قبل النشر العام
- استخدم HTTPS فقط.
- غيّر كلمة مرور Adnan.
- فعّل 2FA ويفضل IP allowlist للإدارة.
- لا تضع API keys أو Firebase service account داخل Flutter أو GitHub.
- استخدم MySQL/PostgreSQL وRedis/queue worker في الإنتاج.
- خذ Backup دوري لقاعدة البيانات والوسائط.
