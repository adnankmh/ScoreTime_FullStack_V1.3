<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('design_profiles', function(Blueprint $t){$t->id();$t->string('name',120);$t->string('scope',20)->default('global')->index();$t->boolean('is_active')->default(false)->index();$t->json('tokens');$t->json('branding')->nullable();$t->json('features')->nullable();$t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamps();});
  Schema::create('page_layouts', function(Blueprint $t){$t->id();$t->string('surface',20)->index();$t->string('page_key',80)->index();$t->string('locale',10)->default('*');$t->json('blocks');$t->boolean('is_published')->default(true)->index();$t->unsignedInteger('revision')->default(1);$t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamps();$t->unique(['surface','page_key','locale']);});
  Schema::create('design_versions', function(Blueprint $t){$t->id();$t->foreignId('design_profile_id')->constrained()->cascadeOnDelete();$t->unsignedInteger('version');$t->json('snapshot');$t->string('note',255)->nullable();$t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamps();$t->unique(['design_profile_id','version']);});
  Schema::create('navigation_items', function(Blueprint $t){$t->id();$t->string('surface',20)->index();$t->string('location',30)->default('primary')->index();$t->string('key',60);$t->json('label');$t->string('icon',60)->nullable();$t->string('target',255);$t->unsignedInteger('sort_order')->default(0);$t->boolean('enabled')->default(true)->index();$t->json('visibility')->nullable();$t->timestamps();});
 }
 public function down(): void { foreach(['navigation_items','design_versions','page_layouts','design_profiles'] as $x) Schema::dropIfExists($x); }
};
