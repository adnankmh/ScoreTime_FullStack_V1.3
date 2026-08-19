# ScoreTime V1.4.7 — GitHub Pages

## الرابط النهائي المطلوب
`https://adnankmh.github.io/ScoreTime/`

## مرة واحدة فقط على GitHub
1. اسم الـRepository يجب أن يكون بالضبط: `ScoreTime`
2. افتح: Settings → Pages
3. تحت Build and deployment اختر: `GitHub Actions`
4. اعمل Push للنسخة V1.4.7.

بعدها Workflow:
`ScoreTime • Web / GitHub Pages`
سيبني Flutter Web وينشره تلقائياً.

## بدون Laravel API منشور
الموقع يعمل تلقائياً في Professional Demo Mode:
- واجهة ScoreTime كاملة.
- مباريات تجريبية احترافية.
- أخبار تحريرية تجريبية.
- World Football.
- Transfer Intelligence.
- PWA.
- تصميم Desktop + Mobile.

## عند نشر Laravel API لاحقاً
GitHub:
Settings → Secrets and variables → Actions → Variables → New repository variable

Name:
`SCORETIME_API_BASE_URL`

Value مثال:
`https://api.example.com/api/v1`

في الـPush التالي سيتحول Web تلقائياً من Demo إلى Live API.

## CORS
Laravel V1.4.7 يحتوي افتراضياً:
`https://adnankmh.github.io`
ضمن CORS_ALLOWED_ORIGINS.

## Workflows الفعالة فقط
1. ScoreTime • Laravel
2. ScoreTime • Web / GitHub Pages
3. ScoreTime • Android APK + AAB
4. ScoreTime • iOS
