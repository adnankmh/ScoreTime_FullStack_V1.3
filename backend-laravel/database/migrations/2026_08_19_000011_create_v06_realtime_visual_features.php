<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration{
 public function up():void{
  Schema::create('match_shots',function(Blueprint $t){$t->id();$t->foreignId('football_match_id')->constrained('football_matches')->cascadeOnDelete();$t->foreignId('team_id')->constrained()->cascadeOnDelete();$t->foreignId('player_id')->nullable()->constrained()->nullOnDelete();$t->unsignedSmallInteger('minute')->nullable();$t->decimal('x',5,2);$t->decimal('y',5,2);$t->decimal('xg',6,3)->nullable();$t->string('outcome',30)->default('off_target')->index();$t->string('body_part',30)->nullable();$t->boolean('is_big_chance')->default(false);$t->timestamps();$t->index(['football_match_id','minute']);});
  Schema::create('match_momentum_points',function(Blueprint $t){$t->id();$t->foreignId('football_match_id')->constrained('football_matches')->cascadeOnDelete();$t->unsignedSmallInteger('minute');$t->smallInteger('value');$t->timestamps();$t->unique(['football_match_id','minute']);});
  Schema::create('user_challenges',function(Blueprint $t){$t->id();$t->foreignId('creator_id')->constrained('users')->cascadeOnDelete();$t->foreignId('opponent_id')->constrained('users')->cascadeOnDelete();$t->string('title',140);$t->string('type',30)->default('predictions')->index();$t->string('status',20)->default('pending')->index();$t->unsignedInteger('creator_score')->default(0);$t->unsignedInteger('opponent_score')->default(0);$t->timestamp('starts_at')->nullable();$t->timestamp('ends_at')->nullable();$t->timestamps();});
  Schema::create('user_levels',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();$t->unsignedInteger('xp')->default(0)->index();$t->unsignedSmallInteger('level')->default(1);$t->string('title',80)->default('Rookie');$t->unsignedInteger('prediction_streak')->default(0);$t->timestamps();});
  Schema::create('search_trends',function(Blueprint $t){$t->id();$t->string('query',180)->unique();$t->unsignedBigInteger('score')->default(0)->index();$t->timestamp('last_searched_at')->nullable();$t->timestamps();});
  Schema::create('article_reactions',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->foreignId('article_id')->constrained()->cascadeOnDelete();$t->string('reaction',20)->default('like');$t->timestamps();$t->unique(['user_id','article_id']);});
  Schema::create('premium_entitlements',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->string('feature',80)->index();$t->timestamp('starts_at')->nullable();$t->timestamp('ends_at')->nullable();$t->json('meta')->nullable();$t->timestamps();$t->index(['user_id','feature']);});
  Schema::table('teams',function(Blueprint $t){$t->string('coach_name',120)->nullable();$t->string('website_url')->nullable();$t->string('city',120)->nullable();$t->json('social_links')->nullable();});
  Schema::table('football_matches',function(Blueprint $t){$t->unsignedBigInteger('revision')->default(0)->index();$t->timestamp('last_synced_at')->nullable()->index();$t->json('broadcast_meta')->nullable();});
 }
 public function down():void{
  Schema::table('football_matches',fn(Blueprint $t)=>$t->dropColumn(['revision','last_synced_at','broadcast_meta']));
  Schema::table('teams',fn(Blueprint $t)=>$t->dropColumn(['coach_name','website_url','city','social_links']));
  Schema::dropIfExists('premium_entitlements');Schema::dropIfExists('article_reactions');Schema::dropIfExists('search_trends');Schema::dropIfExists('user_levels');Schema::dropIfExists('user_challenges');Schema::dropIfExists('match_momentum_points');Schema::dropIfExists('match_shots');
 }
};
