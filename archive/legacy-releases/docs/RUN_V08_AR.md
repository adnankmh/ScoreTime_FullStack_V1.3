# تشغيل ScoreTime V0.8 — بدون Android Studio

## 1) تشغيل موقع Laravel
1. فك الضغط.
2. افتح Terminal داخل `backend-laravel`.
3. نفّذ `composer install`.
4. انسخ `.env.example` إلى `.env`.
5. نفّذ `php artisan key:generate`.
6. اضبط قاعدة البيانات داخل `.env`، أو استخدم SQLite للتجربة.
7. نفّذ `php artisan migrate --seed`.
8. نفّذ `php artisan storage:link`.
9. نفّذ `php artisan serve`.
10. افتح `http://127.0.0.1:8000`.

حساب Bootstrap: `Adnan / Adnan123`. غيّر كلمة المرور فورًا قبل النشر العام وفعّل 2FA.

## 2) فتح Visual Design Studio
`/admin/design-studio`

من هنا يمكنك بدون تعديل الكود:
- اسم المنصة وLogo text وTagline.
- الألوان الأساسية والخلفيات والنصوص.
- Radius وكثافة الواجهة ونمط Header/Cards.
- تشغيل/إيقاف Breaking News وPredictions وFan Room وTransfers وPremium والإعلانات وSocial.
- ترتيب بلوكات Home للموقع والتطبيق.
- إضافة/حذف/إخفاء بلوكات الصفحة.
- ترتيب عناصر Navigation وتفعيلها/إخفاؤها.
- الرجوع إلى نسخة تصميم سابقة Rollback.

## 3) بناء APK بدون Android Studio
1. ارفع المشروع إلى GitHub.
2. افتح `Actions`.
3. اختر `V0.8 Full Validation & APK`.
4. اضغط `Run workflow`.
5. أدخل رابط API مثل `https://yourdomain.com/api/v1`.
6. بعد النجاح حمّل Artifact باسم `ScoreTime-V08-APK`.
7. بداخله `app-release.apk`.

لا تحتاج Android Studio. البناء يتم على GitHub Actions.

## 4) ملاحظة مهمة عن Design Studio
اللوحة لا تكتب PHP أو Dart ولا تسمح بإدخال JavaScript من المدير. التخصيص مبني على Design Tokens وLayouts وFeature Flags مخزنة في قاعدة البيانات، وهذا أكثر أمانًا وأسهل في Rollback.
