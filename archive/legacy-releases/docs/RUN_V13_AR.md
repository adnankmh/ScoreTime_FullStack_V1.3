# ScoreTime V1.3 — Release Candidate
## قبل النشر
- غيّر كلمة مرور المدير ولا تستخدم Adnan123 في الإنتاج.
- فعّل HTTPS و2FA للإدارة.
- ضع مفاتيح Football Provider وFirebase في أسرار السيرفر فقط.
- اربط مصادر أخبار مرخصة/مسموح بها فقط، وراجع الأخبار من /admin/editorial.
- شغّل queue worker وLaravel scheduler تحت Supervisor/systemd.
- استخدم Redis للإنتاج إن توفر.
- شغّل migrations مع نسخة احتياطية حديثة.
## Android بدون Android Studio
GitHub > Actions > ScoreTime V1.3 Release Candidate > Run workflow.
سيتم بناء APK وAAB بعد نجاح tests/analyze.
## ملاحظة
لا تعتبر النسخة Production-approved إلا بعد نجاح Workflow الكامل على GitHub واختبار staging على جهاز Android حقيقي وسيرفر Laravel حقيقي.
