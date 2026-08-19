# ScoreTime V1.4.4 — GitHub Actions

يوجد 3 Workflows فعالة فقط:

1. ScoreTime • Laravel
2. ScoreTime • Android APK + AAB
3. ScoreTime • iOS

## إصلاح خطأ setup-java
الخطأ السابق حدث لأن setup-java كان يستخدم:
`cache: gradle`
قبل وجود مجلد Android/Gradle.

في V1.4.4:
- يتم إعداد Flutter أولاً.
- يتم توليد `android/` عند الحاجة.
- ثم يتم تشغيل `actions/setup-java@v5`.
- لا نستخدم Gradle cache داخل setup-java، لذلك لا يبحث عن ملفات Gradle غير الموجودة.

## Node 24
تم تحديث GitHub Actions الأساسية إلى نسخ Node 24:
- actions/checkout@v6
- actions/setup-java@v5
- actions/upload-artifact@v6
- Flutter action مثبت على v2.23.0 ويستخدم cache action الحديثة.

## Android
Workflow واحد يبني:
- APK
- AAB

ويتم رفعهما معاً في Artifact واحد:
`ScoreTime-V1.4.4-Android`

## iOS
يبني iOS Release بدون Code Signing.
التثبيت الحقيقي على iPhone أو App Store يحتاج Apple Developer signing.
