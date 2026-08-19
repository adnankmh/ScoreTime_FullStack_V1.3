# معمارية V0.6

## Match Visual Intelligence
`FootballDataProvider -> FootballProviderManager -> sync/database -> MatchIntelligenceService + MatchVisualService -> Web/Flutter`

الجداول الجديدة `match_shots` و`match_momentum_points` تسمح بتخزين البيانات المرئية بصورة مستقلة وقابلة للتحديث. `revision` في المباراة يساعد الواجهات على اكتشاف التغييرات.

## Fan Growth
`user_levels`, `user_challenges`, achievements, friendships, predictions وmini leagues تشكل طبقة Gamification قابلة للتوسع.

## Discovery
`search_histories` + `search_trends` يدعمان الاقتراحات والبحث الرائج، بدون الاعتماد على خدمة خارجية.

## Premium
`premium_entitlements` يسمح بتفعيل ميزات محددة لكل مستخدم بدل ربط كل شيء بحقل plan فقط.
