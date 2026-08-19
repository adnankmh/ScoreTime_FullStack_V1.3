# إصلاحات ScoreTime V1.6.1

1. Laravel dotenv:
   - تم تغيير NEWS_QUERY إلى قيمة مقتبسة:
     `NEWS_QUERY="football OR soccer"`
   - تم حذف مفاتيح البيئة المكررة.

2. Flutter:
   - حذف import القديم غير المستخدم الخاص بـTransferIntelligenceScreen.
   - Workflow Web وAndroid وiOS يشغل flutter analyze قبل البناء.

3. Composer:
   - لا يوجد أمر quiet يخفي الخطأ.
   - إذا composer.lock موجود: composer install.
   - إذا غير موجود: composer update مرة واحدة ثم build.
   - Composer يعمل --no-scripts أولاً.
   - يتم إنشاء .env الصحيح قبل تشغيل Laravel.
   - يتم إنشاء bootstrap/cache وstorage runtime folders.
   - package discovery وAPP_KEY بعد اكتمال vendor.

4. GitHub:
   - 4 Workflows فقط.
   - CLEAN_GITHUB_BEFORE_PUSH.bat يحذف أي Workflow قديم بقي من V1.3/V1.4/V1.5.

5. Free API:
   - Quota guard.
   - Cache.
   - Scheduler مناسب لـ100 request/day.
   - Data Status يعرض الحصة المستخدمة والمتبقية.
