# بناء ScoreTime V1.7.3 APK باستخدام GitHub Desktop — بدون Android Studio

1. افتح GitHub Desktop واختر مستودع ScoreTime.
2. راجع Changes وتأكد أن `.env` أو secrets غير ظاهرة.
3. Commit ثم **Push origin**.
4. افتح المستودع على GitHub > **Actions**.
5. ستجد أربعة Workflows: Laravel، Flutter Web، Android APK + AAB، وiOS.
6. افتح `ScoreTime • Android APK + AAB` ثم **Run workflow**.
7. أدخل رابط Laravel الحقيقي المنتهي بـ`/api/v1`، أو اتركه فارغاً ليطلبه التطبيق عند أول تشغيل. لا تكتب رابط `example.com` على أنه رابط حقيقي.
8. GitHub ينشئ Android platform تلقائيًا إن لم يكن موجودًا، يطبق أيقونة ScoreTime، ثم يشغل `flutter pub get` و`flutter analyze` و`flutter test` قبل البناء.
9. بعد النجاح نزّل Artifact باسم `ScoreTime-V1.7.3-Android`؛ بداخله APK للاختبار وAAB. وقّع إصدار المتجر بمفتاحك الخاص قبل النشر العام.

> لا تستخدم `127.0.0.1` كرابط API داخل APK على الهاتف.
