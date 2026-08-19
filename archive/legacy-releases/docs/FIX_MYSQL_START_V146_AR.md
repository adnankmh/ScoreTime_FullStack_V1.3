# ScoreTime V1.4.6 — إصلاح تشغيل MySQL المحلي

المشكلة في V1.4.5:
- Composer نجح.
- vendor/autoload.php تم إنشاؤه.
- APP_KEY تم إنشاؤه.
- MySQL لم يكن يستمع على 127.0.0.1:3306، لذلك Laravel لم يستطع تشغيل migrations.

V1.4.6:
1. يفحص 3306 بفحص TCP سريع وهادئ.
2. يجرب `C:\xampp\mysql_start.bat`.
3. ينتظر حتى 15 ثانية.
4. إذا لم يعمل، يجرب `mysqld.exe` مباشرة مع `my.ini`.
5. ينتظر حتى 20 ثانية.
6. يتحقق بـ `mysql.exe ... SELECT 1`.
7. ينشئ `football_global` إن لم تكن موجودة.
8. يشغل migrations فقط بعد تأكيد أن MySQL جاهز.
9. لا يستخدم migrate:fresh ولا يحذف قواعد بيانات.
10. إذا تعذر تشغيل MySQL، يفتح XAMPP Control Panel ويوقف العملية بأمان.
