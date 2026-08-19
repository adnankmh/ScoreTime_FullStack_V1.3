# تشغيل ScoreTime V0.9 بسهولة

## أسرع طريقة لبناء التطبيق بدون Android Studio
1. ارفع مجلد المشروع كاملًا إلى GitHub.
2. افتح تبويب **Actions**.
3. اختر **V0.9 Full Validation & APK**.
4. اضغط **Run workflow**.
5. اكتب رابط Laravel API المنتهي بـ `/api/v1`.
6. GitHub يشغل `flutter pub get` و`flutter analyze` و`flutter test` ثم يبني APK.
7. من نتيجة الـWorkflow حمّل Artifact باسم **ScoreTime-V09-APK**.
8. داخله ستجد `app-release.apk` ويمكن تثبيته على الهاتف مباشرة.

لا تحتاج Android Studio لبناء APK بهذه الطريقة.

## تشغيل موقع Laravel محليًا
المطلوب فقط PHP 8.2+ وComposer وقاعدة بيانات مناسبة. من مجلد `backend-laravel`:

```bash
composer install
```

انسخ `.env.example` إلى `.env` ثم:

```bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

افتح `http://127.0.0.1:8000`.

حساب Bootstrap الإداري هو `Adnan` / `Adnan123`. غيّر كلمة المرور فورًا قبل نشر الموقع للعامة وفعّل 2FA.

## تشغيل المهام المجدولة
في الإنتاج شغّل Laravel Scheduler كل دقيقة. هذا يشغل مزامنة المباريات إن كانت مفعلة، وكذلك حملات الإشعارات المجدولة في V0.9:

```bash
php artisan schedule:run
```

على Linux يضاف عادة إلى cron كل دقيقة. لا تضع مفاتيح Firebase أو مزود كرة القدم داخل Flutter؛ تبقى في `.env` أو ملفات أسرار السيرفر فقط.

## أين أعدل الموقع والتطبيق بدون كود؟
بعد الدخول كمدير:
- `/admin/design-studio` لتعديل الهوية والألوان والتخطيط الأساسي.
- `/admin/no-code-studio` لـV0.9: الصفحات بالسحب والترتيب، الجدولة، A/B، القوائم، Notification Campaigns وWhite-Label.

التغييرات البنيوية الجديدة التي تحتاج منطقًا برمجيًا جديدًا ما زالت تحتاج إصدارًا جديدًا؛ الـNo-Code Studio لا يسمح بتنفيذ PHP/JS/Dart من لوحة الإدارة لأسباب أمنية.
