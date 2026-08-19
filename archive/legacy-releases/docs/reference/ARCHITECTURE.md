# Architecture

Web: Laravel 12 + Blade first, upgradeable to Inertia/Vue without changing API domain.
Mobile: Flutter + Riverpod + Dio.
API: versioned REST `/api/v1`, with planned WebSocket/SSE live channel.
Data: MySQL/PostgreSQL; Redis recommended for caching, queues, live fan-out.
Jobs: provider sync -> normalization -> DB/cache -> broadcast -> push notification.
Media: object storage + CDN.
Search: Meilisearch/OpenSearch optional.
Observability: structured logs, Sentry/OpenTelemetry optional.
Security: Sanctum/OAuth, RBAC, rate limits, audit log, signed uploads, secret rotation.
