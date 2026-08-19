<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;use App\Models\{AdSlot,AppSetting,Competition,Player,PlayerSeasonStat,Team};
class V04Seeder extends Seeder { public function run(): void {
 foreach([['home.hero','Homepage Hero','home'],['match.inline','Match Center Inline','match'],['news.inline','News Feed Inline','news']] as $slot){AdSlot::updateOrCreate(['key'=>$slot[0]],['name'=>$slot[1],'placement'=>$slot[2],'is_active'=>true]);}
 foreach(['maintenance'=>false,'premium_enabled'=>true,'fan_room_enabled'=>true,'predictions_enabled'=>true] as $key=>$value){AppSetting::updateOrCreate(['key'=>$key],['value'=>['value'=>$value],'is_public'=>true]);}
 if(config('scoretime.seed_demo_data',false)){$competition=Competition::first();$teams=Team::all();if($competition && $teams->isNotEmpty()){foreach($teams->take(4) as $i=>$team){$player=Player::firstOrCreate(['slug'=>'demo-player-'.($i+1)],['name'=>'Demo Player '.($i+1),'team_id'=>$team->id,'position'=>$i===0?'FW':'MF','nationality'=>'Demo','number'=>$i+7,'rating'=>7.2+($i/10),'goals'=>max(0,8-$i*2),'assists'=>2+$i,'appearances'=>8]);PlayerSeasonStat::updateOrCreate(['player_id'=>$player->id,'competition_id'=>$competition->id,'season'=>$competition->season??'2026/27'],['appearances'=>8,'starts'=>7,'minutes'=>650,'goals'=>max(0,8-$i*2),'assists'=>2+$i,'rating'=>7.2+($i/10),'xg'=>6.5-$i,'xa'=>2.1+$i]);}}}
 }}
