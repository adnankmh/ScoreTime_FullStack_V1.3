# أسهل طريقة لإخراج APK بدون Android Studio

هذه هي الطريقة الموصى بها إذا كنت لا تريد تثبيت Android Studio أو Android SDK أو Java على جهازك.

1. افتح GitHub من المتصفح وأنشئ Repository جديدًا فارغًا.
2. ارفع محتويات هذا المجلد إلى المستودع باستخدام زر **Add file → Upload files**؛ لا تحتاج GitHub Desktop.
3. افتح تبويب **Actions** في GitHub.
4. اختر Workflow باسم **Flutter APK - No Android Studio** ثم اضغط **Run workflow**.
5. بعد نجاح التنفيذ افتح التشغيل نفسه وانزل إلى **Artifacts**.
6. حمّل الملف `scoretime-apk` ثم فك الضغط؛ ستجد `app-release.apk`.
7. أرسل APK إلى هاتف Android وثبته. قد يطلب الهاتف السماح بالتثبيت من المتصفح/مدير الملفات لمرة واحدة.

> لا تحتاج Android Studio في هذه الطريقة. البناء كله يتم على خوادم GitHub Actions.

## الموقع Laravel
إذا أردت فقط معاينة الموقع على جهاز فيه PHP + Composer:

```bat
cd backend-laravel
copy .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
ثم افتح `http://127.0.0.1:8000`.

بيانات المدير التجريبية بعد Seeder:
- Username: `Adnan`
- Password: `Adnan123`

**غيّر كلمة المرور فورًا قبل أي نشر عام.**
