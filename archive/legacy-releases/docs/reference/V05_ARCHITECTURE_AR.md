# معمارية V0.5

- **Data Plane:** FootballDataProvider -> FootballProviderManager -> FootballDataService.
- **Intelligence Plane:** MatchIntelligenceService لتجميع H2H/Form/lineups/derived probability.
- **Experience Plane:** Laravel Web + API V1 + Flutter.
- **Identity Plane:** Sanctum + MFA للمدير + Device Sessions + audit logs.
- **Engagement Plane:** Favorites, follows, match subscriptions, predictions, mini leagues, friends, achievements.
- **Commercial Plane:** sponsors, ad slots/campaigns, premium-ready.

لا يتم تخزين مفاتيح مزود البيانات أو Firebase داخل المستودع. استخدم متغيرات البيئة وGitHub Secrets.
