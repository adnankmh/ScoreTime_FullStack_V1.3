<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('football_countries', function(Blueprint $t){
   $t->id(); $t->string('name')->index(); $t->string('code',8)->nullable()->index(); $t->string('flag_url',1000)->nullable();
   $t->boolean('is_active')->default(true)->index(); $t->json('meta')->nullable(); $t->timestamps(); $t->unique(['name','code']);
  });
  Schema::create('competition_seasons', function(Blueprint $t){
   $t->id(); $t->foreignId('competition_id')->constrained()->cascadeOnDelete(); $t->unsignedSmallInteger('season')->index();
   $t->date('starts_on')->nullable(); $t->date('ends_on')->nullable(); $t->boolean('is_current')->default(false)->index();
   $t->json('coverage')->nullable(); $t->timestamp('last_synced_at')->nullable()->index(); $t->timestamps();
   $t->unique(['competition_id','season']);
  });
  Schema::create('competition_team', function(Blueprint $t){
   $t->foreignId('competition_id')->constrained()->cascadeOnDelete(); $t->foreignId('team_id')->constrained()->cascadeOnDelete();
   $t->unsignedSmallInteger('season')->index(); $t->timestamps(); $t->primary(['competition_id','team_id','season']);
  });
  Schema::create('coaches', function(Blueprint $t){
   $t->id(); $t->string('provider_id')->nullable()->unique(); $t->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
   $t->string('name')->index(); $t->string('nationality',100)->nullable(); $t->date('birth_date')->nullable(); $t->string('photo_url',1000)->nullable();
   $t->timestamp('last_synced_at')->nullable(); $t->timestamps();
  });
  Schema::table('competitions', function(Blueprint $t){
   $t->string('provider_id')->nullable()->unique()->after('id'); $t->foreignId('football_country_id')->nullable()->after('provider_id')->constrained('football_countries')->nullOnDelete();
   $t->string('type',30)->default('league')->index(); $t->boolean('is_international')->default(false)->index(); $t->json('coverage')->nullable(); $t->timestamp('last_synced_at')->nullable()->index();
  });
  Schema::table('teams', function(Blueprint $t){
   $t->string('provider_id')->nullable()->unique()->after('id'); $t->string('team_type',30)->default('club')->index(); $t->string('national_code',8)->nullable();
   $t->string('venue_name')->nullable(); $t->string('venue_city')->nullable(); $t->unsignedInteger('venue_capacity')->nullable(); $t->string('venue_image_url',1000)->nullable();
   $t->timestamp('last_synced_at')->nullable()->index();
  });
  Schema::table('players', function(Blueprint $t){
   $t->string('provider_id')->nullable()->unique()->after('id'); $t->string('first_name')->nullable(); $t->string('last_name')->nullable();
   $t->string('height',20)->nullable(); $t->string('weight',20)->nullable(); $t->boolean('is_injured')->default(false)->index();
   $t->timestamp('last_synced_at')->nullable()->index();
  });
 }
 public function down(): void {
  Schema::table('players',fn(Blueprint $t)=>$t->dropColumn(['provider_id','first_name','last_name','height','weight','is_injured','last_synced_at']));
  Schema::table('teams',fn(Blueprint $t)=>$t->dropColumn(['provider_id','team_type','national_code','venue_name','venue_city','venue_capacity','venue_image_url','last_synced_at']));
  Schema::table('competitions',function(Blueprint $t){$t->dropConstrainedForeignId('football_country_id');$t->dropColumn(['provider_id','type','is_international','coverage','last_synced_at']);});
  Schema::dropIfExists('coaches'); Schema::dropIfExists('competition_team'); Schema::dropIfExists('competition_seasons'); Schema::dropIfExists('football_countries');
 }
};
