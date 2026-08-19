# ScoreTime V0.9 — Experience OS

V0.9 تنقل المشروع من Visual Design Studio إلى No-Code Experience OS. تم إضافة Custom Pages Builder ببلوكات آمنة، Dynamic Block Hydration، Preview متعدد المقاسات، Media picker، Scheduled Design Publishing، A/B Experiments، Multi-level Menu/App Tabs، Notification Campaign Builder، White-Label Profiles، Custom Page API وFlutter Dynamic Pages.

## حدود أمان مقصودة
لا يسمح النظام بكتابة PHP أو JavaScript أو Dart من لوحة الإدارة. التعديل يتم عبر Schema/Design Tokens/Blocks مع Laravel validation. هذا يمنع تحويل لوحة التصميم إلى منفذ لتنفيذ كود على الخادم.

## فحوصات محلية
PHP syntax lint لكل ملفات PHP. فحص imports المحلية وتوازن الأقواس في Dart. CI مرفق لتشغيل Composer/Laravel tests وFlutter analyze/test/build على GitHub.
