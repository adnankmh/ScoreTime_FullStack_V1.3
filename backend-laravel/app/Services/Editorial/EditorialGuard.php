<?php
namespace App\Services\Editorial;
use App\Models\NewsSource;
final class EditorialGuard {
 public function sourceMayIngest(NewsSource $source): bool {
  return $source->enabled && in_array($source->license_status,['licensed','rss-permitted','partner'],true);
 }
 public function validateSummary(string $summary): array {
  $words=preg_split('/\s+/u',trim($summary),-1,PREG_SPLIT_NO_EMPTY);
  return ['ok'=>count($words)<=config('scoretime_v12.editorial.max_summary_words',180),'word_count'=>count($words)];
 }
}
