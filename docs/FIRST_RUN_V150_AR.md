# ScoreTime V1.5.0 — التشغيل المحلي على Windows

الطريقة الأسهل:
1. فك الضغط داخل `C:\xampp\htdocs\ScoreTime`.
2. شغّل `START_SCORETIME_WINDOWS.bat`.
3. السكربت يثبت Composer dependencies إذا لزم، ينشئ `.env` و`APP_KEY`، يشغّل MySQL إن أمكن، ينفذ migrations غير المدمرة، ثم يفتح `http://127.0.0.1:8000`.

لا يستخدم السكربت `migrate:fresh` ولا يمسح قواعد البيانات.
