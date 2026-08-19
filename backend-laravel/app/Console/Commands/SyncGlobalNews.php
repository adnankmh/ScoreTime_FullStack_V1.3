<?php
namespace App\Console\Commands;

use App\Models\Article;
use App\Models\DataProviderSyncLog;
use App\Services\NewsApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncGlobalNews extends Command
{
    protected $signature = 'news:sync-global {--language=}';
    protected $description = 'Synchronize current football headlines from the configured licensed news provider.';

    public function handle(NewsApiService $news): int
    {
        $languages = $this->option('language')
            ? [$this->option('language')]
            : config('news.languages', ['en']);

        $started = now();
        $clock = microtime(true);
        $count = 0;

        try {
            foreach ($languages as $language) {
                foreach ($news->football($language) as $raw) {
                    $url = trim((string) ($raw['url'] ?? ''));
                    $title = trim((string) ($raw['title'] ?? ''));
                    if ($url === '' || $title === '') continue;

                    $providerId = hash('sha256', $url);
                    $domain = parse_url($url, PHP_URL_HOST);

                    Article::updateOrCreate(
                        ['provider' => 'newsapi', 'provider_id' => $providerId],
                        [
                            'title' => $title,
                            'slug' => Str::slug(Str::limit($title, 110, '') . '-' . substr($providerId, 0, 8)),
                            'excerpt' => Str::limit(strip_tags((string) ($raw['description'] ?? '')), 500),
                            // Do not copy publisher articles into ScoreTime.
                            'body' => Str::limit(strip_tags((string) ($raw['description'] ?? '')), 1000),
                            'image_url' => $raw['urlToImage'] ?? null,
                            'category' => 'world-football',
                            'author_name' => $raw['author'] ?? data_get($raw, 'source.name'),
                            'published_at' => $raw['publishedAt'] ?? now(),
                            'source_published_at' => $raw['publishedAt'] ?? null,
                            'source_name' => data_get($raw, 'source.name'),
                            'source_url' => $url,
                            'source_domain' => $domain,
                            'locale' => $language,
                            'content_policy' => 'headline_excerpt_link',
                            'is_breaking' => false,
                            'is_featured' => false,
                        ]
                    );
                    $count++;
                }
            }

            DataProviderSyncLog::create([
                'provider' => 'newsapi',
                'resource' => 'news',
                'status' => 'success',
                'records' => $count,
                'duration_ms' => (int) ((microtime(true) - $clock) * 1000),
                'started_at' => $started,
                'finished_at' => now(),
            ]);

            $this->info("Synced {$count} football headlines.");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            DataProviderSyncLog::create([
                'provider' => 'newsapi',
                'resource' => 'news',
                'status' => 'failed',
                'records' => $count,
                'duration_ms' => (int) ((microtime(true) - $clock) * 1000),
                'message' => $e->getMessage(),
                'started_at' => $started,
                'finished_at' => now(),
            ]);
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
