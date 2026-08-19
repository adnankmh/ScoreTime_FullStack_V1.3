# ScoreTime V1.4.3 — Workflows

يوجد بالضبط 4 Workflows فعالة:

1. **ScoreTime • Laravel**
   - يعمل تلقائياً عند تغييرات Laravel، ويمكن تشغيله يدوياً.
   - Composer + MySQL 8 + migrations + seed + PHPUnit.
   - تم إصلاح `Tests\TestCase` بإضافة `autoload-dev` إلى Composer.

2. **ScoreTime • Android APK**
   - تشغيل يدوي.
   - flutter pub get + analyze + test + APK Release.

3. **ScoreTime • Android AAB**
   - تشغيل يدوي.
   - flutter pub get + analyze + test + AAB Release لـGoogle Play.

4. **ScoreTime • iOS**
   - تشغيل يدوي على macOS Runner.
   - يبني iOS Release بدون Code Signing ويخرج ZIP للتطبيق.
   - تثبيت التطبيق على iPhone أو إخراج IPA/App Store يحتاج Apple Developer signing certificate وprovisioning profile. لم يتم وضع أي أسرار Apple داخل المشروع.

## Workflows القديمة
لا توجد ملفات workflow قديمة بصيغة yml/yaml داخل archive.

إذا بقيت أسماء قديمة ظاهرة في صفحة GitHub Actions فهذا سجل تاريخي على GitHub وليس ملفات فعالة.
لحذفها من الواجهة: Actions > workflow القديم > ... بجانب الـrun > Delete workflow run.
