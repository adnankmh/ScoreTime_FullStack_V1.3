# إصلاحات V1.4.5

- إصلاح Flutter `MyApp isn't a class`:
  - `widget_test.dart` أصبح يستخدم `ScoreTimeApp`.
  - Android/iOS workflows يحتويان حماية تلقائية من ملف widget_test قديم يحمل MyApp.
- إصلاح Laravel المحلي:
  - السبب السابق: `vendor/autoload.php` غير موجود.
  - START_SCORETIME_WINDOWS.bat يشغل Composer تلقائياً إذا كان vendor مفقوداً.
  - ينشئ `.env` و`APP_KEY` آلياً.
- 3 Workflows فقط:
  - ScoreTime • Laravel
  - ScoreTime • Android APK + AAB
  - ScoreTime • iOS
- لا توجد Workflow YAML قديمة في archive.
- Android يستخدم setup-java@v5 بدون Gradle cache المبكر.
- checkout@v6 وupload-artifact@v6.
