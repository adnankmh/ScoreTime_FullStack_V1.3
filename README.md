# ScoreTime Global V1.7.0 — REAL DATA SAFE

منصة كرة قدم Full‑Stack أصلية باسم وشعار **ScoreTime**: موقع وREST API ولوحة إدارة بـLaravel 12، وتطبيق Android/iOS/Web بـFlutter 3.47 وRiverpod، مع ست لغات ودعم RTL.

ابدأ من [دليل التشغيل العربي](docs/START_HERE_V170_AR.md). لا تحتاج Android Studio؛ تبني GitHub Actions ملفات APK وAAB بعد إدخال رابط Laravel API حقيقي وآمن.

## المكونات

- `backend-laravel/`: الموقع وAPI ولوحة الإدارة ومزامنة مزودي البيانات.
- `mobile-flutter/`: تطبيق Flutter للموبايل والويب.
- `.github/workflows/`: فحص Laravel وبناء Android وiOS وFlutter Web.
- `branding/`: شعار وأصول ScoreTime المعتمدة.
- `docs/`: التشغيل والمعمارية والأمان وملاحظات الإصدار.

يحفظ Laravel بيانات المزود في MySQL/Cache ويقدمها لكل المستخدمين من خادم ScoreTime. لا تدخل مفاتيح مزود كرة القدم إلى APK أو JavaScript، ولا تستبدل أخطاء الشبكة بنتائج تجريبية صامتة.

راجع أيضاً [معمارية البيانات والرصيد](docs/ARCHITECTURE_V170_AR.md) و[ملاحظات V1.7.0](docs/RELEASE_V170_AR.md).
