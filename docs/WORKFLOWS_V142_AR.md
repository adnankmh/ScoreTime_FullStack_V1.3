# ScoreTime V1.4.2 — GitHub Actions

## الموجود في المشروع
يوجد Workflow فعال واحد فقط:

`.github/workflows/scoretime.yml`

ويظهر في GitHub باسم:

**ScoreTime • Verify & Android**

### عند Push / Pull Request
يشغّل:
- Laravel / Composer validation
- MySQL 8 service
- migrations + seed
- route boot
- PHPUnit
- flutter pub get
- flutter analyze
- flutter test

### عند Run workflow يدويًا
بعد نجاح Laravel وFlutter، يبني:
- app-release.apk
- app-release.aab

## لماذا قد ترى أسماء Workflows قديمة في GitHub؟
حذف ملفات `.github/workflows/*.yml` يمنع تشغيلها مستقبلاً، لكن GitHub يحتفظ بسجل Workflow Runs القديمة.
إذا أردت تنظيف واجهة Actions أيضًا:
1. افتح GitHub > Actions.
2. اختر Workflow قديم.
3. من `...` بجانب كل Run اختر `Delete workflow run`.
4. كرر للـRuns القديمة التي لا تحتاجها.

## عند ترقية Repository قديم
لا تنسخ الملفات الجديدة فقط فوق القديمة.
احذف محتويات `.github/workflows` القديمة أولاً، ثم اترك `scoretime.yml` وحده.
