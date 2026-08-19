<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Article,Competition,FootballMatch,Standing,Team};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminSeeder::class);

        $epl = Competition::updateOrCreate(
            ['slug' => 'premier-league'],
            ['name_ar' => 'الدوري الإنجليزي الممتاز', 'name_en' => 'Premier League', 'country' => 'England', 'season' => '2026/27', 'is_featured' => true, 'sort_order' => 1]
        );
        Competition::updateOrCreate(
            ['slug' => 'champions-league'],
            ['name_ar' => 'دوري أبطال أوروبا', 'name_en' => 'UEFA Champions League', 'country' => 'Europe', 'season' => '2026/27', 'is_featured' => true, 'sort_order' => 2]
        );

        $teams = [
            ['أرسنال','Arsenal','arsenal','ARS','#E91A2B'],
            ['ليفربول','Liverpool','liverpool','LIV','#C8102E'],
            ['مانشستر سيتي','Manchester City','manchester-city','MCI','#6CABDD'],
            ['تشيلسي','Chelsea','chelsea','CHE','#034694'],
        ];
        $made = [];
        foreach ($teams as $x) {
            $made[] = Team::updateOrCreate(
                ['slug' => $x[2]],
                ['name_ar' => $x[0], 'name_en' => $x[1], 'short_name' => $x[3], 'country' => 'England', 'primary_color' => $x[4]]
            );
        }

        $scheduled = FootballMatch::updateOrCreate(
            ['provider_id' => 'scoretime-demo-scheduled'],
            ['competition_id' => $epl->id, 'home_team_id' => $made[0]->id, 'away_team_id' => $made[1]->id, 'kickoff_at' => now()->startOfDay()->setTime(20,30), 'status' => 'scheduled', 'venue' => 'ScoreTime Arena', 'round' => 'Round 1', 'tv_channels' => ['Sports One']]
        );
        $live = FootballMatch::updateOrCreate(
            ['provider_id' => 'scoretime-demo-live'],
            ['competition_id' => $epl->id, 'home_team_id' => $made[2]->id, 'away_team_id' => $made[3]->id, 'kickoff_at' => now()->startOfDay()->setTime(18,30), 'status' => 'live', 'minute' => 34, 'home_score' => 1, 'away_score' => 0, 'venue' => 'ScoreTime City', 'round' => 'Round 1', 'stats' => ['possession'=>[61,39],'shots'=>[8,4],'shots_on_target'=>[4,1],'corners'=>[5,2]]]
        );

        foreach ($made as $i => $team) {
            Standing::updateOrCreate(
                ['competition_id' => $epl->id, 'team_id' => $team->id],
                ['position' => $i+1, 'played' => 1, 'won' => $i<2?1:0, 'drawn' => 0, 'lost' => $i<2?0:1, 'goals_for' => 2-$i%2, 'goals_against' => $i%2, 'goal_difference' => $i<2?1:-1, 'points' => $i<2?3:0, 'form' => $i<2?'W':'L']
            );
        }

        Article::updateOrCreate(
            ['slug' => 'scoretime-launch'],
            ['title' => 'ScoreTime is ready for the next football moment', 'excerpt' => 'Live scores, trusted editorial coverage and advanced match intelligence in one premium experience.', 'body' => 'This is local development content. Replace it with original or licensed editorial coverage before publishing.', 'category' => 'ScoreTime', 'author_name' => 'ScoreTime Editorial', 'published_at' => now(), 'is_breaking' => true, 'is_featured' => true]
        );

        $this->call(V04Seeder::class);
        $this->call(V05EliteSeeder::class);
        $this->call(V08VisualBuilderSeeder::class);
        $this->call(V09NoCodeSeeder::class);
        $this->call(V10GlobalSeeder::class);
    }
}
