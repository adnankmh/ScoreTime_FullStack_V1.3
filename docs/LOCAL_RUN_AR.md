# تشغيل ScoreTime محليًا على Windows/XAMPP

داخل `backend-laravel`:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

ثم افتح `http://127.0.0.1:8000`.

الإعداد المحلي الافتراضي في `.env.example` يستخدم MySQL `football_global`، root بدون password، وFile session/cache وSync queue لتقليل متطلبات التشغيل.

بيانات bootstrap للإدارة: `Adnan / Adnan123` ويجب تغيير كلمة المرور فورًا قبل أي نشر حقيقي.
