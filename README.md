# ScoreTime Global V1.7.3 — EASY CONNECT

منصة كرة قدم Full‑Stack أصلية باسم وشعار **ScoreTime**: موقع وREST API ولوحة إدارة بـLaravel 12، وتطبيق Android/iOS/Web بـFlutter 3.47 وRiverpod، مع ست لغات ودعم RTL.

ابدأ من [دليل التشغيل العربي](docs/START_HERE_V173_AR.md). لا تحتاج Android Studio؛ تبني GitHub Actions ملفات APK وAAB سواء أدخلت رابط Laravel الحقيقي أثناء البناء أو تركته فارغاً لاختيار المعاينة أو الربط المحلي أو HTTPS عند أول تشغيل.

## المكونات

- `backend-laravel/`: الموقع وAPI ولوحة الإدارة ومزامنة مزودي البيانات.
- `mobile-flutter/`: تطبيق Flutter للموبايل والويب.
- `.github/workflows/`: فحص Laravel وبناء Android وiOS وFlutter Web.
- `branding/`: شعار وأصول ScoreTime المعتمدة.
- `docs/`: التشغيل والمعمارية والأمان وملاحظات الإصدار.

يحفظ Laravel بيانات المزود في MySQL/Cache ويقدمها لكل المستخدمين من خادم ScoreTime. لا تدخل مفاتيح مزود كرة القدم إلى APK أو JavaScript، ولا تستبدل أخطاء الشبكة بنتائج تجريبية صامتة.

راجع أيضاً [معمارية البيانات والرصيد](docs/ARCHITECTURE_V172_AR.md) و[ملاحظات V1.7.3](docs/RELEASE_V173_AR.md).
