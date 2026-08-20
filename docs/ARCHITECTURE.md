# ScoreTime V1.7.2 Architecture

ScoreTime is a unified football platform with a Laravel 12 web/API/admin backend and Flutter clients for Android, iOS and Web.

## Runtime layers

- **Laravel Web**: public responsive experience, SEO/PWA, matches, news, teams, players, competitions and transfers.
- **REST API v1**: versioned contract for Flutter, authentication, live data, search, personalization, alerts and remote design tokens.
- **Control Room**: secured administration, editorial tools, provider telemetry, media, monetization, security and no-code Experience Studio.
- **Flutter**: Riverpod client with secure token storage, six languages, Arabic RTL, themes and adjustable text size.
- **Provider adapters**: Laravel-only ingest for API‑Football and football-data.org. The core product does not scrape competitors.

## Data path

Provider → quota guard → scheduled sync → MySQL/Cache → Laravel API → Flutter/Web.

Provider secrets stay in server environment variables. Public health endpoints are passive and do not spend provider quota. The public Laravel URL may be compiled in or configured securely on first launch. The Web workflow repairs platform metadata before building. Details are in [the V1.7.2 data architecture](ARCHITECTURE_V172_AR.md).
