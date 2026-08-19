# ScoreTime V1.1 — التشغيل السهل
1. شغّل Laravel كما في V1.0: composer install، أنشئ .env، ثم key:generate و migrate --seed.
2. اللغة الافتراضية هي English. اللغات: English, العربية, Français, Español, Deutsch, Türkçe.
3. تسجيل الدخول يقبل البريد الإلكتروني أو اسم المستخدم مع كلمة المرور.
4. البيانات الرياضية الحقيقية تأتي من مزود مرخّص عبر Laravel ولا توضع مفاتيحه داخل Flutter.
5. الأخبار: اربط فقط RSS/API/partner feeds التي تسمح بإعادة الاستخدام. كل خبر يحتفظ بالمصدر والرابط ويذهب للمراجعة افتراضيًا. لا تستخدم scraping للمواقع التي تمنعه.
6. APK/AAB: استخدم GitHub Actions الموجودة في المشروع؛ لا تحتاج Android Studio.
