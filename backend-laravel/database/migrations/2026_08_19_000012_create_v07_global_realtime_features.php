<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_commentaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('football_match_id')
                ->constrained('football_matches')
                ->cascadeOnDelete();

            $table->foreignId('team_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('player_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('provider_event_id', 120)->nullable();
            $table->unsignedSmallInteger('minute')->nullable()->index();
            $table->unsignedSmallInteger('stoppage')->default(0);
            $table->string('type', 40)->default('commentary')->index();
            $table->text('text');
            $table->unsignedTinyInteger('importance')->default(1)->index();
            $table->json('payload')->nullable();

            $table->timestamps();

            $table->index(['football_match_id', 'id']);

            $table->unique(
                ['football_match_id', 'provider_event_id'],
                'match_provider_comment_unique'
            );
        });

        Schema::create('player_heatmap_points', function (Blueprint $table) {
            $table->id();

            $table->foreignId('football_match_id')
                ->constrained('football_matches')
                ->cascadeOnDelete();

            $table->foreignId('team_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('player_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('x', 5, 2);
            $table->decimal('y', 5, 2);
            $table->decimal('weight', 6, 3)->default(1);

            $table->unsignedSmallInteger('minute_from')->nullable();
            $table->unsignedSmallInteger('minute_to')->nullable();

            $table->timestamps();

            $table->index(['football_match_id', 'player_id']);
        });

        Schema::create('prediction_seasons', function (Blueprint $table) {
            $table->id();

            $table->string('name', 120);
            $table->string('slug', 140)->unique();

            // dateTime is used for better compatibility with XAMPP/MySQL/MariaDB.
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at')->index();

            $table->boolean('is_active')->default(true)->index();
            $table->json('scoring_rules')->nullable();

            $table->timestamps();
        });

        Schema::create('prediction_season_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('prediction_season_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('points')->default(0)->index();
            $table->unsignedInteger('exact_scores')->default(0);
            $table->unsignedInteger('correct_outcomes')->default(0);
            $table->unsignedInteger('current_streak')->default(0);
            $table->unsignedInteger('best_streak')->default(0);

            $table->timestamps();

            $table->unique([
                'prediction_season_id',
                'user_id',
            ]);
        });

        Schema::create('friend_activities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('type', 60)->index();

            $table->string('subject_type', 120)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'created_at',
            ]);
        });

        Schema::create('article_engagement_signals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('article_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('event', 30)->index();
            $table->decimal('weight', 6, 2)->default(1);

            $table->timestamps();

            $table->index([
                'user_id',
                'created_at',
            ]);
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->unsignedTinyInteger('confidence')->nullable()->index();
            $table->string('source_name', 160)->nullable();
            $table->string('headline', 220)->nullable();

            $table->dateTime('last_verified_at')
                ->nullable()
                ->index();
        });

        Schema::table('football_matches', function (Blueprint $table) {
            $table->string('realtime_state', 30)
                ->default('idle')
                ->index();

            $table->dateTime('realtime_heartbeat_at')
                ->nullable()
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('football_matches', function (Blueprint $table) {
            $table->dropColumn([
                'realtime_state',
                'realtime_heartbeat_at',
            ]);
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn([
                'confidence',
                'source_name',
                'headline',
                'last_verified_at',
            ]);
        });

        Schema::dropIfExists('article_engagement_signals');
        Schema::dropIfExists('friend_activities');
        Schema::dropIfExists('prediction_season_scores');
        Schema::dropIfExists('prediction_seasons');
        Schema::dropIfExists('player_heatmap_points');
        Schema::dropIfExists('live_commentaries');
    }
};