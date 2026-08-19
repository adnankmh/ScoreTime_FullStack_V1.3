# تشغيل V0.2

## Laravel
```bash
cd backend-laravel
cp .env.example .env
composer install
php artisan key:generate
# عدّل DB_* داخل .env
php artisan migrate --seed
php artisan serve
```

المدير الأولي: `Adnan` / `Adnan123`.

## Flutter
```bash
cd mobile-flutter
flutter pub get
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1
```
لجهاز حقيقي استبدل `10.0.2.2` بعنوان IP الكمبيوتر داخل الشبكة.

## فحص الإنتاج
```bash
php artisan optimize
php artisan route:list
php artisan test
flutter analyze
flutter test
```
