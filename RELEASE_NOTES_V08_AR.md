# ScoreTime V0.8

V0.8 هي مرحلة No-Code Operations & Visual Builder.

## الجديد
- Visual Design Studio محمي بصلاحية Admin + 2FA.
- Design Profiles مركزية للموقع والتطبيق.
- Design Tokens للألوان والخلفيات والنصوص والـradius والكثافة وأنماط البطاقات والهيدر.
- Branding مركزي: product name / logo text / tagline.
- Feature Switches مركزية.
- Web/App Page Layouts ببلوكات قابلة لإعادة الترتيب والتفعيل والحذف.
- Navigation Manager للموقع وBottom Navigation للتطبيق.
- Version History + Rollback للتصميم.
- Public design bootstrap API مع caching.
- Laravel Web يقرأ التصميم والتخطيط المنشور من قاعدة البيانات.
- Flutter يقرأ التصميم والـbranding والـnavigation والـhome layout من Laravel API.
- Home Flutter أصبحت API-backed بدل Demo-only.
- V0.8 CI يبني APK بدون Android Studio.

## مبدأ الأمان
No arbitrary code execution: لا تقوم لوحة التصميم بكتابة أو تشغيل PHP/Dart/JS مخصص. التخصيص محصور في schema محدد ومتحقق منه server-side.
