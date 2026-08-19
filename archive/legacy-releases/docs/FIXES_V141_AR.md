# ScoreTime V1.4.1 — Clean Build

تم إصلاح المشاكل التي ظهرت في GitHub Actions:

## Flutter
- intl مثبت على ^0.20.3.
- استبدال جميع Color.withOpacity(...) بـ Color.withValues(alpha: ...).
- إصلاح MomentumChart: clamp يعاد كـ double صريح.
- إصلاح Featured News: استخدام BoxConstraints(minHeight: 270) بدل minHeight غير الموجود في Container.
- DropdownButtonFormField يستخدم initialValue بدل value المتوقف تدريجياً.
- إضافة Lottie صالح داخل assets/lottie حتى يبقى المجلد موجوداً في Git.
- Workflow يفحص هذه الانحدارات قبل flutter pub get/analyze.

## Laravel / Composer
- إضافة license = proprietary إلى composer.json.
- Quality Gate يستخدم composer validate --no-check-publish --no-check-lock.
- CI يستخدم composer update داخل Runner لحل حالة composer.lock القديم أو غير الموجود.
- لا يوجد أي php artisan test داخل Workflows الفعالة؛ الاختبارات تستخدم vendor/bin/phpunit.

## GitHub Actions
الموجود فقط:
1. ScoreTime • Quality Gate
2. ScoreTime • Android Release

## ملاحظة
الـarchive يحتوي ملفات الإصدارات القديمة للتوثيق فقط، ولا يتم تنفيذها كـGitHub Actions.
