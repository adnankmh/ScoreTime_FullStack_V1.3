# ScoreTime V0.4 — World Class Platform

V0.4 توسع V0.3 إلى منصة تشغيل كروية متعددة الطبقات: Provider Adapter، مركز عمليات، إحصائيات موسمية للاعبين، Top Scorers/Assists/Ratings/xG-ready، Notification Center، Push Devices، Mini Leagues، Media Library schema، Sponsors/Ads schema، Premium-ready accounts، MFA/TOTP للمدير مع Recovery Codes، وواجهات Flutter متصلة بالـAPI لهذه الوظائف.

## أهم إصلاحات الجودة
- إصلاح البحث ليستخدم حقول أسماء الفرق والبطولات الصحيحة.
- توحيد حقول Seeder الخاصة باللاعبين مع schema الفعلي.
- إبقاء Football Provider في وضع Demo افتراضي آمن وعدم ادعاء Live Data بدون مزود مرخص.
- 2FA يطبق Server-side وليس بإخفاء عناصر الواجهة.
- Recovery Codes مخزنة كـhashes وليس كنص مكشوف.

## حدود هذه النسخة
Firebase packages موجودة والبنية Push-ready، لكن إرسال FCM الحقيقي يحتاج Firebase project credentials الخاصة بصاحب المشروع. xG/Shot Map والبيانات الحية لا يمكن توليدها بشكل موثوق دون مزود Football Data مرخص يوفرها.
