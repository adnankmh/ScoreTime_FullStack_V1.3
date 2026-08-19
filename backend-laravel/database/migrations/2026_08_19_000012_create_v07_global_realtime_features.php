<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('live_commentaries', function(Blueprint $t){$t->id();$t->foreignId('football_match_id')->constrained('football_matches')->cascadeOnDelete();$t->foreignId('team_id')->nullable()->constrained()->nullOnDelete();$t->foreignId('player_id')->nullable()->constrained()->nullOnDelete();$t->string('provider_event_id',120)->nullable();$t->unsignedSmallInteger('minute')->nullable()->index();$t->unsignedSmallInteger('stoppage')->default(0);$t->string('type',40)->default('commentary')->index();$t->text('text');$t->unsignedTinyInteger('importance')->default(1)->index();$t->json('payload')->nullable();$t->timestamps();$t->index(['football_match_id','id']);$t->unique(['football_match_id','provider_event_id'],'match_provider_comment_unique');});
  Schema::create('player_heatmap_points', function(Blueprint $t){$t->id();$t->foreignId('football_match_id')->constrained('football_matches')->cascadeOnDelete();$t->foreignId('team_id')->constrained()->cascadeOnDelete();$t->foreignId('player_id')->constrained()->cascadeOnDelete();$t->decimal('x',5,2);$t->decimal('y',5,2);$t->decimal('weight',6,3)->default(1);$t->unsignedSmallInteger('minute_from')->nullable();$t->unsignedSmallInteger('minute_to')->nullable();$t->timestamps();$t->index(['football_match_id','player_id']);});
  Schema::create('prediction_seasons', function(Blueprint $t){$t->id();$t->string('name',120);$t->string('slug',140)->unique();$t->timestamp('starts_at')->index();$t->timestamp('ends_at')->index();$t->boolean('is_active')->default(true)->index();$t->json('scoring_rules')->nullable();$t->timestamps();});
  Schema::create('prediction_season_scores', function(Blueprint $t){$t->id();$t->foreignId('prediction_season_id')->constrained()->cascadeOnDelete();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->integer('points')->default(0)->index();$t->unsignedInteger('exact_scores')->default(0);$t->unsignedInteger('correct_outcomes')->default(0);$t->unsignedInteger('current_streak')->default(0);$t->unsignedInteger('best_streak')->default(0);$t->timestamps();$t->unique(['prediction_season_id','user_id']);});
  Schema::create('friend_activities', function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->string('type',60)->index();$t->string('subject_type',120)->nullable();$t->unsignedBigInteger('subject_id')->nullable();$t->json('meta')->nullable();$t->timestamps();$t->index(['user_id','created_at']);});
  Schema::create('article_engagement_signals', function(Blueprint $t){$t->id();$t->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();$t->foreignId('article_id')->constrained()->cascadeOnDelete();$t->string('event',30)->index();$t->decimal('weight',6,2)->default(1);$t->timestamps();$t->index(['user_id','created_at']);});
  Schema::table('transfers', function(Blueprint $t){$t->unsignedTinyInteger('confidence')->nullable()->index();$t->string('source_name',160)->nullable();$t->string('headline',220)->nullable();$t->timestamp('last_verified_at')->nullable()->index();});
  Schema::table('football_matches', function(Blueprint $t){$t->string('realtime_state',30)->default('idle')->index();$t->timestamp('realtime_heartbeat_at')->nullable()->index();});
 }
 public function down(): void {
  Schema::table('football_matches',fn(Blueprint $t)=>$t->dropColumn(['realtime_state','realtime_heartbeat_at']));
  Schema::table('transfers',fn(Blueprint $t)=>$t->dropColumn(['confidence','source_name','headline','last_verified_at']));
  foreach(['article_engagement_signals','friend_activities','prediction_season_scores','prediction_seasons','player_heatmap_points','live_commentaries'] as $table) Schema::dropIfExists($table);
 }
};
