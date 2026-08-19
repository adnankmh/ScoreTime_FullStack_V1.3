<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('provider')->nullable()->index();
            $table->string('provider_id')->nullable()->index();
            $table->string('source_name')->nullable();
            $table->text('source_url')->nullable();
            $table->string('source_domain')->nullable()->index();
            $table->string('locale', 10)->default('en')->index();
            $table->timestamp('source_published_at')->nullable()->index();
            $table->string('content_policy')->default('headline_excerpt_link');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'provider','provider_id','source_name','source_url','source_domain',
                'locale','source_published_at','content_policy'
            ]);
        });
    }
};
