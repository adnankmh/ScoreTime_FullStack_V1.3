# بناء ScoreTime APK باستخدام GitHub Desktop — بدون Android Studio

1. افتح GitHub Desktop واختر مستودع ScoreTime.
2. راجع Changes وتأكد أن `.env` أو secrets غير ظاهرة.
3. Commit ثم **Push origin**.
4. افتح المستودع على GitHub > **Actions**.
5. لديك Workflowان فقط:
   - `ScoreTime • Quality Gate` للفحص بعد push/PR.
   - `ScoreTime • Android Release` لبناء APK + AAB يدويًا.
6. افتح `ScoreTime • Android Release` > **Run workflow**.
7. أدخل رابط Laravel API وينتهي بـ `/api/v1`، مثال: `https://api.example.com/api/v1`.
8. GitHub ينشئ Android platform تلقائيًا إن لم يكن موجودًا، يطبق أيقونة ScoreTime، ثم يشغل `flutter pub get` و`flutter analyze` و`flutter test` قبل البناء.
9. بعد النجاح نزّل Artifact باسم `ScoreTime-V1.4-Android`; بداخله APK للتثبيت وAAB لـGoogle Play.

> لا تستخدم `127.0.0.1` كرابط API داخل APK على الهاتف.
