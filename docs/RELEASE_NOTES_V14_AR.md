# ScoreTime Global V1.4 — إعادة بناء واجهة وتنظيف الإصدار

## الهوية والتصميم
- اعتماد شعار ScoreTime الذي اختاره المستخدم، مع أيقونة مستقلة مشتقة من رمز ST + الساعة + كرة القدم فقط.
- إعادة تصميم Laravel Web بالكامل بنظام بصري Navy / Electric Blue / Cyan / Gold، مع Home وMatches وMatch Center وAdmin Control Room جديدة.
- إعادة تصميم أهم شاشات Flutter: Home، Matches، News، Login، Settings، وإصلاح Dynamic Pages.
- استمرار 3 Themes وتكبير/تصغير الخط وRemote Design/Experience OS.

## GitHub Actions
- يوجد Workflowان فعالان فقط:
  1. `ScoreTime • Quality Gate`
  2. `ScoreTime • Android Release`
- Workflows القديمة نُقلت إلى `archive/legacy-releases/workflows/` ولا تعمل كـGitHub Actions.
- إصلاح تعارض `intl` إلى `^0.20.3`.
- عدم استخدام الأمر غير المتوفر `php artisan test`؛ Quality Gate يستخدم `vendor/bin/phpunit`.
- Android workflow ينشئ مجلد Android تلقائيًا إذا كان غير موجود ويطبق أيقونة ScoreTime ثم يبني APK + AAB.
- Flutter مثبت على 3.47.0 في CI لتقليل اختلافات البناء.

## Laravel/XAMPP reliability fixes
- إضافة `public/index.php` و`.htaccess` المطلوبين.
- تضمين `bootstrap/cache` وstorage runtime dirs عبر `.gitkeep`.
- `.env.example` أسهل للتشغيل المحلي: English، file session/cache، sync queue.
- إصلاح timestamps الحساسة لمشكلة MariaDB/XAMPP السابقة.
- Seeders تستخدم updateOrCreate/firstOrCreate لتقليل أخطاء duplicate data.

## اللغات
English هي الافتراضية. اللغات المدعومة في الموقع والتطبيق:
- English
- العربية
- Français
- Español
- Deutsch
- Türkçe

## الميزات المحفوظة
تم الحفاظ على كامل طبقات النتائج، المباريات، الأخبار، الإحصائيات، اللاعبين، الفرق، البطولات، الانتقالات، World Football، Match Intelligence، Visual Analytics، Fan/Social، Predictions، Notifications، Premium/Ads، Media، PWA/SEO، وNo-Code Experience Studio التي تم بناؤها في الإصدارات السابقة.
