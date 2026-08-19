# V0.3 — Feature Matrix

## Match Intelligence
- Live match REST payloads, timeline/events-ready schema, lineups/stats JSON, H2H-ready endpoints.
- Match Center architecture supports possession, shots, xG, cards, substitutions and live minute when the licensed provider supplies them.
- Provider abstraction remains isolated from UI and domain models.

## Discovery
- Global search across teams, players, competitions and published news.
- Dedicated Transfer Center and Player APIs.
- Web + Flutter Explore experience.

## Fan Layer
- Predictions before kickoff with unique per-user/per-match constraint.
- Global leaderboard.
- Match Fan Room with authenticated, rate-limited posting and admin moderation.
- Favorites endpoints for teams, players, competitions and matches.

## Security
- Admin middleware isolation, Sanctum API tokens, throttled authentication and fan posting.
- Device-session table prepared for session revocation UI.
- Database fields prepared for admin MFA/TOTP; production enablement still requires secret encryption + QR enrollment flow.
- Audit/security tools from V0.2 retained.

## Editorial/Operations
- Transfer model/feed, global-feature admin screen, fan moderation.
- Five locale packs retained; 3 appearance themes and font scale retained.

## Flutter
- API repository, Explore, Transfer Center, Fan League screens.
- GitHub Actions APK build requiring no Android Studio locally.
- Secure token storage from V0.2 retained.
