<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
 public function up(): void {
  Schema::create('news_sources', function (Blueprint $t) {
   $t->id(); $t->string('name'); $t->string('type')->default('rss');
   $t->text('feed_url'); $t->string('homepage_url')->nullable();
   $t->string('license_status')->default('review');
   $t->boolean('enabled')->default(false); $t->unsignedSmallInteger('trust_score')->default(50);
   $t->timestamps();
  });
  Schema::create('editorial_items', function (Blueprint $t) {
   $t->id(); $t->foreignId('news_source_id')->nullable()->constrained()->nullOnDelete();
   $t->string('source_url')->unique(); $t->string('source_title')->nullable();
   $t->text('original_excerpt')->nullable(); $t->text('editorial_summary')->nullable();
   $t->string('language',5)->default('en'); $t->string('status')->default('review');
   $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
   $t->timestamp('source_published_at')->nullable(); $t->timestamp('reviewed_at')->nullable();
   $t->timestamps();
  });
  Schema::create('tv_broadcasts', function (Blueprint $t) {
   $t->id(); $t->foreignId('football_match_id')->constrained()->cascadeOnDelete();
   $t->string('country_code',3)->nullable(); $t->string('channel_name');
   $t->string('platform')->nullable(); $t->string('language',5)->nullable();
   $t->boolean('is_official')->default(true); $t->timestamps();
  });
  Schema::create('smart_alert_rules', function (Blueprint $t) {
   $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
   $t->string('event_type'); $t->string('subject_type')->nullable(); $t->unsignedBigInteger('subject_id')->nullable();
   $t->boolean('push')->default(true); $t->boolean('in_app')->default(true);
   $t->json('quiet_hours')->nullable(); $t->timestamps();
  });
  Schema::create('experience_presets', function (Blueprint $t) {
   $t->id(); $t->string('surface'); $t->string('screen'); $t->string('name');
   $t->json('schema'); $t->boolean('published')->default(false);
   $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); $t->timestamps();
  });
 }
 public function down(): void {
  Schema::dropIfExists('experience_presets'); Schema::dropIfExists('smart_alert_rules');
  Schema::dropIfExists('tv_broadcasts'); Schema::dropIfExists('editorial_items'); Schema::dropIfExists('news_sources');
 }
};
