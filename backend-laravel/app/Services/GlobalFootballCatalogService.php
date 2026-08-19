<?php
namespace App\Services;
use App\Models\{Competition,CompetitionSeason,FootballCountry,FootballMatch,Player,PlayerSeasonStat,Standing,Team};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GlobalFootballCatalogService {
 public function __construct(private FootballProviderManager $manager){}
 private function provider(){return $this->manager->driver();}

 public function syncCountries():array{
  $rows=$this->provider()->countries();$count=0;
  DB::transaction(function()use($rows,&$count){foreach($rows as $row){$name=trim((string)($row['name']??''));if($name==='')continue;FootballCountry::updateOrCreate(['name'=>$name,'code'=>$row['code']??null],['flag_url'=>$row['flag']??null,'is_active'=>true,'meta'=>$row]);$count++;}});
  return ['countries'=>$count];
 }

 public function syncCompetitions(?string $country=null,?int $season=null):array{
  $rows=$this->provider()->leagues(array_filter(['country'=>$country,'season'=>$season]));$leagues=0;$seasons=0;
  DB::transaction(function()use($rows,&$leagues,&$seasons){foreach($rows as $row){$l=$row['league']??[];$c=$row['country']??[];if(empty($l['id'])||empty($l['name']))continue;$seasonRows=$row['seasons']??[];$last=$seasonRows?end($seasonRows):[];$countryModel=FootballCountry::firstOrCreate(['name'=>$c['name']??'International','code'=>$c['code']??null],['flag_url'=>$c['flag']??null]);$competition=Competition::updateOrCreate(['provider_id'=>(string)$l['id']],['football_country_id'=>$countryModel->id,'name_ar'=>$l['name'],'name_en'=>$l['name'],'slug'=>Str::slug($l['name'].'-'.$l['id']),'country'=>$c['name']??null,'logo_url'=>$l['logo']??null,'type'=>strtolower((string)($l['type']??'league')),'is_international'=>($c['name']??'World')==='World','coverage'=>$last['coverage']??null,'last_synced_at'=>now()]);$leagues++;foreach($seasonRows as $s){if(empty($s['year']))continue;CompetitionSeason::updateOrCreate(['competition_id'=>$competition->id,'season'=>(int)$s['year']],['starts_on'=>$s['start']??null,'ends_on'=>$s['end']??null,'is_current'=>(bool)($s['current']??false),'coverage'=>$s['coverage']??null,'last_synced_at'=>now()]);$seasons++;}}});
  return ['competitions'=>$leagues,'seasons'=>$seasons];
 }

 public function syncLeague(int|string $providerLeagueId,int $season,bool $includePlayers=true):array{
  $competition=Competition::where('provider_id',(string)$providerLeagueId)->firstOrFail();$teamRows=$this->provider()->teams(['league'=>$providerLeagueId,'season'=>$season]);$teams=0;$players=0;$fixtures=0;$standings=0;
  foreach($teamRows as $row){$t=$row['team']??[];$v=$row['venue']??[];if(empty($t['id'])||empty($t['name']))continue;$team=Team::updateOrCreate(['provider_id'=>(string)$t['id']],['name_ar'=>$t['name'],'name_en'=>$t['name'],'slug'=>Str::slug($t['name'].'-'.$t['id']),'short_name'=>$t['code']??null,'country'=>$t['country']??null,'logo_url'=>$t['logo']??null,'founded_year'=>$t['founded']??null,'team_type'=>($t['national']??false)?'national':'club','venue_name'=>$v['name']??null,'venue_city'=>$v['city']??null,'venue_capacity'=>$v['capacity']??null,'venue_image_url'=>$v['image']??null,'last_synced_at'=>now()]);DB::table('competition_team')->updateOrInsert(['competition_id'=>$competition->id,'team_id'=>$team->id,'season'=>$season],['updated_at'=>now()]);$teams++;
   if($includePlayers){foreach($this->provider()->squads((string)$t['id']) as $squad){foreach(($squad['players']??[]) as $p){if(empty($p['id'])||empty($p['name']))continue;Player::updateOrCreate(['provider_id'=>(string)$p['id']],['team_id'=>$team->id,'name'=>$p['name'],'slug'=>Str::slug($p['name'].'-'.$p['id']),'position'=>$p['position']??null,'number'=>$p['number']??null,'photo_url'=>$p['photo']??null,'last_synced_at'=>now()]);$players++;}}}
  }
  foreach($this->provider()->fixtures(['league'=>$providerLeagueId,'season'=>$season]) as $raw){$fid=data_get($raw,'fixture.id');$home=Team::where('provider_id',(string)data_get($raw,'teams.home.id'))->first();$away=Team::where('provider_id',(string)data_get($raw,'teams.away.id'))->first();if(!$fid||!$home||!$away)continue;FootballMatch::updateOrCreate(['provider_id'=>(string)$fid],['competition_id'=>$competition->id,'home_team_id'=>$home->id,'away_team_id'=>$away->id,'kickoff_at'=>data_get($raw,'fixture.date'),'status'=>strtolower((string)data_get($raw,'fixture.status.short','scheduled')),'minute'=>data_get($raw,'fixture.status.elapsed'),'home_score'=>(int)(data_get($raw,'goals.home')??0),'away_score'=>(int)(data_get($raw,'goals.away')??0),'venue'=>data_get($raw,'fixture.venue.name'),'round'=>data_get($raw,'league.round'),'last_synced_at'=>now()]);$fixtures++;}
  $standingResponse=$this->provider()->standings($providerLeagueId,(string)$season);$groups=data_get($standingResponse,'0.league.standings',[]);Standing::where('competition_id',$competition->id)->delete();foreach($groups as $group){foreach($group as $row){$team=Team::where('provider_id',(string)data_get($row,'team.id'))->first();if(!$team)continue;Standing::create(['competition_id'=>$competition->id,'team_id'=>$team->id,'position'=>(int)($row['rank']??0),'played'=>(int)data_get($row,'all.played',0),'won'=>(int)data_get($row,'all.win',0),'drawn'=>(int)data_get($row,'all.draw',0),'lost'=>(int)data_get($row,'all.lose',0),'goals_for'=>(int)data_get($row,'all.goals.for',0),'goals_against'=>(int)data_get($row,'all.goals.against',0),'goal_difference'=>(int)($row['goalsDiff']??0),'points'=>(int)($row['points']??0),'form'=>$row['form']??null]);$standings++;}}
  $competition->update(['season'=>(string)$season,'last_synced_at'=>now()]);
  return ['teams'=>$teams,'squad_players'=>$players,'fixtures'=>$fixtures,'standings'=>$standings];
 }

 public function syncPlayerStats(int|string $providerLeagueId,int $season,int $maxPages=20):array{
  $competition=Competition::where('provider_id',(string)$providerLeagueId)->firstOrFail();$count=0;
  for($page=1;$page<=$maxPages;$page++){$response=$this->provider()->players(['league'=>$providerLeagueId,'season'=>$season,'page'=>$page]);if(!$response)break;foreach($response as $row){$p=$row['player']??[];$stat=$row['statistics'][0]??[];if(empty($p['id'])||empty($p['name']))continue;$teamProvider=(string)($stat['team']['id']??'');$team=$teamProvider?Team::where('provider_id',$teamProvider)->first():null;$player=Player::updateOrCreate(['provider_id'=>(string)$p['id']],['team_id'=>$team?->id,'name'=>$p['name'],'first_name'=>$p['firstname']??null,'last_name'=>$p['lastname']??null,'slug'=>Str::slug($p['name'].'-'.$p['id']),'nationality'=>$p['nationality']??null,'birth_date'=>$p['birth']['date']??null,'height'=>$p['height']??null,'weight'=>$p['weight']??null,'is_injured'=>(bool)($p['injured']??false),'photo_url'=>$p['photo']??null,'position'=>$stat['games']['position']??null,'number'=>$stat['games']['number']??null,'appearances'=>(int)($stat['games']['appearences']??0),'rating'=>isset($stat['games']['rating'])?(float)$stat['games']['rating']:null,'goals'=>(int)($stat['goals']['total']??0),'assists'=>(int)($stat['goals']['assists']??0),'last_synced_at'=>now()]);PlayerSeasonStat::updateOrCreate(['player_id'=>$player->id,'competition_id'=>$competition->id,'season'=>(string)$season],['appearances'=>(int)($stat['games']['appearences']??0),'starts'=>(int)($stat['games']['lineups']??0),'minutes'=>(int)($stat['games']['minutes']??0),'goals'=>(int)($stat['goals']['total']??0),'assists'=>(int)($stat['goals']['assists']??0),'yellow_cards'=>(int)($stat['cards']['yellow']??0),'red_cards'=>(int)(($stat['cards']['red']??0)+($stat['cards']['yellowred']??0)),'rating'=>isset($stat['games']['rating'])?(float)$stat['games']['rating']:null,'extra'=>$stat]);$count++;}if(count($response)<20)break;}
  return ['player_stats'=>$count];
 }
}
