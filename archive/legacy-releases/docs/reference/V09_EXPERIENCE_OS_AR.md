# V0.9 Experience OS

## ما الذي يمكن تعديله بدون كود؟
- إنشاء صفحات Web/App جديدة من Preset blocks.
- ترتيب البلوكات بالسحب والإفلات.
- Hero/Banner وRich Text وLive Matches وNews وTransfers وAd slots.
- اختيار صور من Media Library.
- Preview Desktop/Tablet/Mobile.
- نشر أو حفظ الصفحة Draft.
- قوائم متعددة المستويات وApp tabs، مع targets مثل `page:world-football-center`.
- جدولة هوية مؤقتة لبطولة أو حملة.
- A/B experiments بنسبة توزيع محددة.
- White-label حسب domain/host.
- Notification campaigns حسب All/Premium/Free ومواعيد إرسال.

## البنية
`NoCodeStudioController` يدير البيانات الإدارية، `NoCodeExperienceService` يحل الهوية الحالية والجدولة والتجارب والقوائم، `DynamicBlockService` يربط تعريف البلوك بالبيانات الفعلية، و`DesignStudioService` يوحد Bootstrap الذي يقرأه Laravel وFlutter.

## الأمان
- Admin middleware + Admin 2FA كما في الإصدارات السابقة.
- Validation لكل مدخلات Builder.
- لا arbitrary executable code.
- A/B assignment deterministic لتفادي تبديل التجربة في كل refresh.
- Firebase وFootball provider secrets Server-side فقط.
