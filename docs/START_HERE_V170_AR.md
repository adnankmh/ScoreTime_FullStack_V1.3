# تشغيل ScoreTime V1.7.0 من الصفر

## ما تحتاجه

- Windows 10/11.
- XAMPP مع PHP 8.2 أو أحدث وMySQL، أو PHP/Composer/MySQL مثبتة بصورة مستقلة.
- Composer مرة واحدة لتنزيل حزم Laravel.
- حساب GitHub فقط إذا أردت APK/AAB أو GitHub Pages.
- لا تحتاج Android Studio.

## التشغيل المحلي بضغطة واحدة

1. فك الحزمة في `C:\ScoreTime` وتجنب المسارات الطويلة جداً.
2. افتح XAMPP مرة واحدة وتأكد أن MySQL يمكن تشغيله. السكربت سيحاول تشغيله تلقائياً لاحقاً.
3. شغّل `START_SCORETIME_WINDOWS.bat`.

السكربت يقوم تلقائياً بما يلي:

- إنشاء مجلدات `bootstrap/cache` و`storage` المفقودة.
- تشغيل Composer عند غياب `vendor`، مع دعم حالة عدم وجود `composer.lock`.
- إنشاء `.env` و`APP_KEY` وقاعدة `football_global` وتشغيل migrations غير المدمرة.
- إزالة سجلات العرض القديمة المعروفة من نسخ V1.6 وما قبلها.
- إنشاء كلمة مرور قوية للمستخدم الإداري `Adnan` وعرضها مرة واحدة فقط.
- تشغيل الموقع على `http://127.0.0.1:8000` وتشغيل `artisan schedule:work` تلقائياً.

لا يستخدم السكربت `migrate:fresh` ولا يمسح قاعدة بياناتك الحالية.

## ربط البيانات الحقيقية

1. احصل على مفتاح API‑Football.
2. شغّل `SETUP_FREE_API_WINDOWS.bat`.
3. الصق المفتاح؛ مفتاح NewsAPI اختياري وملائم للتطوير وفق شروط المزود.

التحقق الأول يستهلك طلب مباريات واحد فقط. بعده يعمل الجدول المحمي:

- مزامنة جدول اليوم مرة واحدة عند `00:05 UTC`.
- فحص حي كل عشر دقائق، لكنه لا يتصل بالمزود ما لم توجد مباراة مخزنة داخل نافذة اللعب.
- تفاصيل مباراة أولوية كل 30 دقيقة أثناء وجود مباراة حية فقط.
- تدوير دوري مميز واحد يومياً لجلب الفرق والموسم والترتيب وصفحتين من اللاعبين وانتقالات فريق وإصابات الدوري.

يمكن متابعة الرصيد وحالة آخر مزامنة من:

`GET /api/v1/data-status`

هذه الصفحة لا تنفذ أي طلب خارجي.

## إنشاء APK من GitHub من دون Android Studio

1. أنشئ مستودع GitHub وارفع محتويات الحزمة إلى جذره.
2. انشر Laravel على نطاق HTTPS مثل `https://api.example.org`.
3. من GitHub افتح **Actions → ScoreTime • Android APK + AAB → Run workflow**.
4. أدخل رابط API كاملاً وينتهي بـ`/api/v1`، مثال: `https://api.example.org/api/v1`.
5. بعد نجاح الـworkflow نزّل Artifact باسم `ScoreTime-V1.7.0-Android`؛ ستجد APK وAAB داخله.

الـworkflow يرفض `example.com` أو رابط HTTP أو رابطاً لا ينتهي بـ`/api/v1` حتى لا ينتج تطبيقاً يبدو ناجحاً لكنه غير متصل.

## نشر Flutter Web

أنشئ Repository variable باسم `SCORETIME_API_BASE_URL` وضع فيه رابط Laravel HTTPS المنتهي بـ`/api/v1`. عندها يبني Workflow الويب المسار الصحيح تلقائياً وفق اسم المستودع.

لا ينشر Workflow بيانات عرض بصمت. يمكن السماح بها يدوياً فقط من خيار `allow_demo_mode` عند التشغيل اليدوي.

## إعدادات الإنتاج الضرورية

عدّل `.env` على الخادم:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
ADMIN_REQUIRE_MFA=true
CACHE_STORE=redis
QUEUE_CONNECTION=redis
CORS_ALLOWED_ORIGINS=https://your-web-domain.example
```

استخدم HTTPS وRedis ونسخاً احتياطية وقاعدة بيانات بحساب محدود الصلاحيات. لا ترفع `.env` أو مفاتيح API أو ملف Firebase إلى GitHub.

## أوامر مفيدة

```bash
php artisan football:sync-today
php artisan football:sync-live --force
php artisan football:sync-priority-details 123
php artisan football:sync-featured --league=39 --season=2026
php artisan optimize:clear
```

خيار `--force` للتشخيص اليدوي فقط لأنه يتجاوز بوابة نافذة المباراة، لكنه لا يتجاوز سقف الرصيد اليومي.
