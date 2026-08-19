# معمارية V0.8 Visual Builder

المصدر الوحيد للحقيقة هو Laravel Database.

`design_profiles` يخزن Design Tokens + Branding + Feature Flags.
`page_layouts` يخزن ترتيب مكونات الصفحة لكل surface (`web` أو `app`).
`navigation_items` يخزن القوائم وترتيبها وتفعيلها.
`design_versions` يخزن Snapshots للرجوع للخلف.

Laravel Web يقرأ `DesignStudioService::bootstrap('web')`، وFlutter يقرأ `/api/v1/design/bootstrap?surface=app`.

هذا يعني أن تغيير Accent أو اسم المنصة أو ترتيب Home أو Bottom Navigation لا يحتاج Release جديدًا للتطبيق طالما أن المكوّن المطلوب موجود أصلًا داخل الإصدار.

الحد المقصود: إضافة Widget برمجي جديد كليًا أو منطق جديد ما زال يحتاج تطوير إصدار، أما ترتيب/إظهار/إخفاء/Branding/Design Tokens فلا يحتاج تعديل كود.
