<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up(){Schema::create('competitions',function(Blueprint $t){$t->id();$t->string('name_ar');$t->string('name_en');$t->string('slug')->unique();$t->string('country')->nullable();$t->string('logo_url')->nullable();$t->string('season')->nullable();$t->boolean('is_featured')->default(false);$t->unsignedInteger('sort_order')->default(0);$t->timestamps();});}public function down(){Schema::dropIfExists('competitions');}};
