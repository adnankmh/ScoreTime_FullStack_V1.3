<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // V1.6 and earlier inserted these exact preview identities during every
        // normal seed. V1.7 removes only those known records so they can never be
        // mistaken for licensed, synchronized football data after an upgrade.
        if (Schema::hasTable('football_matches')) {
            DB::table('football_matches')
                ->whereIn('provider_id', ['scoretime-demo-scheduled', 'scoretime-demo-live'])
                ->delete();
        }

        if (Schema::hasTable('players')) {
            DB::table('players')->where('slug', 'like', 'demo-player-%')->delete();
        }

        if (Schema::hasTable('articles')) {
            DB::table('articles')->where('slug', 'scoretime-launch')->delete();
        }

        if (Schema::hasTable('teams')) {
            DB::table('teams')
                ->whereNull('provider_id')
                ->whereIn('slug', ['arsenal', 'liverpool', 'manchester-city', 'chelsea'])
                ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('football_matches')->whereColumn('home_team_id', 'teams.id')->orWhereColumn('away_team_id', 'teams.id'))
                ->delete();
        }

        if (Schema::hasTable('competitions')) {
            DB::table('competitions')
                ->whereNull('provider_id')
                ->whereIn('slug', ['premier-league', 'champions-league'])
                ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('football_matches')->whereColumn('competition_id', 'competitions.id'))
                ->delete();
        }
    }

    public function down(): void
    {
        // Intentionally empty: removed rows were explicitly labeled preview data.
    }
};
