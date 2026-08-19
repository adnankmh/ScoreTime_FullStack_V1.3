# ScoreTime V1.4 Architecture

ScoreTime is a unified football platform with a Laravel 12 web/API/admin backend and a Flutter mobile client.

## Runtime layers
- **Laravel Web**: public web experience, SEO/PWA, live matches, news, teams, players, competitions and transfers.
- **REST API v1**: mobile/API contract for auth, scores, intelligence, search, personalization, social, notifications and design bootstrap.
- **Adnan Control Room**: secured administration, editorial, football data, media, monetization, security and no-code Experience Studio.
- **Flutter**: Android-first client with dynamic navigation, remote design tokens, secure token storage, six languages, three appearance themes and adjustable text size.
- **Football Provider Adapter**: provider-neutral ingest/sync layer. No competitor scraping is required by the core platform.

## Major functional domains retained
Live score + match timeline, lineups, stats, H2H/form, xG/shot-map/momentum-ready structures, player heatmaps/comparison, injuries/suspensions, transfer intelligence, global catalog, news/editorial, TV guide, predictions, mini leagues, achievements, friends, fan room, smart alerts, premium/ads/sponsors, PWA/SEO, media library, A/B experiments, white-label profiles, custom pages, design scheduler and dynamic onboarding.

## Data safety
Secrets live in server environment variables only. Flutter receives public API configuration, never football-provider or Firebase service-account secrets.
