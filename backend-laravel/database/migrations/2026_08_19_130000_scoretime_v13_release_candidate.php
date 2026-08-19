<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
 public function up(): void {
  Schema::create('competition_bracket_nodes', function(Blueprint $t){
   $t->id(); $t->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();
   $t->string('stage'); $t->unsignedSmallInteger('round_order')->default(1);
   $t->unsignedSmallInteger('slot_order')->default(1); $t->foreignId('football_match_id')->nullable()->constrained()->nullOnDelete();
   $t->unsignedBigInteger('next_node_id')->nullable(); $t->timestamps();
   $t->index(['competition_id','stage','round_order']);
  });
  Schema::create('team_of_week_entries', function(Blueprint $t){
   $t->id(); $t->foreignId('competition_id')->nullable()->constrained('competitions')->nullOnDelete();
   $t->string('season')->nullable(); $t->unsignedSmallInteger('week')->nullable();
   $t->foreignId('player_id')->constrained()->cascadeOnDelete(); $t->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
   $t->string('position',12); $t->decimal('rating',4,2)->nullable(); $t->unsignedSmallInteger('x')->default(50); $t->unsignedSmallInteger('y')->default(50);
   $t->timestamps(); $t->index(['competition_id','season','week']);
  });
  Schema::create('player_radar_snapshots', function(Blueprint $t){
   $t->id(); $t->foreignId('player_id')->constrained()->cascadeOnDelete();
   $t->foreignId('competition_id')->nullable()->constrained('competitions')->nullOnDelete();
   $t->string('season')->nullable(); $t->json('metrics'); $t->timestamps();
  });
  Schema::create('match_story_items', function(Blueprint $t){
   $t->id(); $t->foreignId('football_match_id')->constrained()->cascadeOnDelete();
   $t->unsignedInteger('sequence')->default(0); $t->string('type',30); $t->unsignedSmallInteger('minute')->nullable();
   $t->string('title')->nullable(); $t->text('body')->nullable(); $t->json('payload')->nullable(); $t->timestamps();
   $t->index(['football_match_id','sequence']);
  });
  Schema::create('onboarding_flows', function(Blueprint $t){
   $t->id(); $t->string('locale',5)->default('en'); $t->string('name'); $t->boolean('published')->default(false);
   $t->json('steps'); $t->unsignedInteger('version')->default(1);
   $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); $t->timestamps();
  });
  Schema::create('editorial_revisions', function(Blueprint $t){
   $t->id(); $t->foreignId('editorial_item_id')->constrained()->cascadeOnDelete();
   $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $t->text('summary')->nullable();
   $t->string('action',30); $t->json('meta')->nullable(); $t->timestamps();
  });
 }
 public function down(): void {
  Schema::dropIfExists('editorial_revisions'); Schema::dropIfExists('onboarding_flows');
  Schema::dropIfExists('match_story_items'); Schema::dropIfExists('player_radar_snapshots');
  Schema::dropIfExists('team_of_week_entries'); Schema::dropIfExists('competition_bracket_nodes');
 }
};
