# معمارية V0.3

`Laravel Web + Admin` و`Laravel REST API /api/v1` يشتركان في Domain Models، بينما Flutter يستهلك الـAPI فقط. طبقة `FootballDataService` هي Anti-Corruption Layer لمزود البيانات المرخص، لذلك لا يجب أن تتسرب أسماء حقول المزود إلى الواجهات.

للتشغيل الحي على نطاق كبير: Provider webhook/polling → Queue Worker → normalized football tables → Cache/Redis → API → Flutter/Web. عند الحاجة للحظية الحقيقية يضاف Laravel Reverb/WebSockets؛ لم نربطه بمزود وهمي في V0.3 حتى لا نعطي إحساسًا كاذبًا بالـLive.

الأمان مبني على least privilege. المدير ليس مجرد زر مخفي: مسارات `/admin` خلف `auth + admin`. في الإنتاج يجب إجبار HTTPS، تغيير بيانات المدير الأولية، تعطيل APP_DEBUG، ضبط CORS على النطاقات الفعلية، وإضافة MFA حقيقي قبل فتح لوحة الإدارة للعامة.
