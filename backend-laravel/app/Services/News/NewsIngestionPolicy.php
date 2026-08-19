<?php
namespace App\Services\News;

final class NewsIngestionPolicy
{
    public function canIngest(array $item): bool
    {
        return filled($item['source_url'] ?? null)
            && filled($item['source_name'] ?? null)
            && in_array($item['license'] ?? 'unknown', ['licensed','rss-permitted','partner'], true);
    }

    public function editorialRecord(array $item): array
    {
        return [
            'source_name' => $item['source_name'],
            'source_url' => $item['source_url'],
            'source_published_at' => $item['published_at'] ?? null,
            'editorial_status' => config('scoretime.news.auto_publish') ? 'ready' : 'review',
        ];
    }
}
