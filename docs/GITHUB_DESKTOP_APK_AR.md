# بناء ScoreTime V1.7 APK باستخدام GitHub Desktop — بدون Android Studio

1. افتح GitHub Desktop واختر مستودع ScoreTime.
2. راجع Changes وتأكد أن `.env` أو secrets غير ظاهرة.
3. Commit ثم **Push origin**.
4. افتح المستودع على GitHub > **Actions**.
5. ستجد أربعة Workflows: Laravel، Flutter Web، Android APK + AAB، وiOS.
6. افتح `ScoreTime • Android APK + AAB` ثم **Run workflow**.
7. أدخل رابط Laravel API الحقيقي عبر HTTPS وينتهي بـ `/api/v1`، مثال: `https://api.your-domain.com/api/v1`.
8. GitHub ينشئ Android platform تلقائيًا إن لم يكن موجودًا، يطبق أيقونة ScoreTime، ثم يشغل `flutter pub get` و`flutter analyze` و`flutter test` قبل البناء.
9. بعد النجاح نزّل Artifact باسم `ScoreTime-V1.7.0-Android`؛ بداخله APK للاختبار وAAB. وقّع إصدار المتجر بمفتاحك الخاص قبل النشر العام.

> لا تستخدم `127.0.0.1` كرابط API داخل APK على الهاتف.
