# V1.0 — Global Football Data Architecture

V1.0 يعتمد مبدأ Provider-backed data. لا يتم نسخ محتوى Kooora أو 365Scores أو FilGoal أو غيرها، ولا يتم تخزين dataset غير مرخص داخل المصدر.

التسلسل:
`Licensed Provider → Provider Adapter → GlobalFootballCatalogService → Laravel DB → REST API → Web + Flutter`.

المزامنة تدعم الدول، المسابقات والكؤوس، المواسم وCoverage، الأندية والمنتخبات، الملاعب، القوائم، اللاعبين، بيانات اللاعب الموسمية، المباريات الحية، الأحداث، التشكيلات والإحصائيات عبر الـProvider abstraction.

الجداول الجديدة: `football_countries`, `competition_seasons`, `competition_team`, `coaches`، إضافة Provider IDs وLast Sync وCoverage إلى المسابقات والفرق واللاعبين.

API العامة الجديدة:
- `/api/v1/world/summary`
- `/api/v1/world/countries`
- `/api/v1/world/competitions`
- `/api/v1/world/teams`
- `/api/v1/world/players`
- `/api/v1/world/coaches`

الموقع: `/world` و`/world/players`.
لوحة المدير: `/admin/world-data`.
