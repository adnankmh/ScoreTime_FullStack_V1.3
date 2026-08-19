<?php
namespace App\Services\Providers;
use App\Contracts\FootballDataProvider; use GuzzleHttp\Client;
class ApiFootballProvider implements FootballDataProvider {
 private Client $http; public function __construct(){ $this->http=new Client(['base_uri'=>rtrim((string)config('football.base_url','https://v3.football.api-sports.io'),'/').'/', 'timeout'=>10,'headers'=>['x-apisports-key'=>(string)config('football.key'),'Accept'=>'application/json']]); }
 public function name(): string{return 'api-football';}
 public function countries():array{return $this->get('countries');}
 public function leagues(array $filters=[]):array{return $this->get('leagues',$filters);}
 public function teams(array $filters=[]):array{return $this->get('teams',$filters);}
 public function squads(int|string $teamId):array{return $this->get('players/squads',['team'=>$teamId]);}
 public function transfers(array $filters=[]):array{return $this->get('transfers',$filters);}
 public function injuries(array $filters=[]):array{return $this->get('injuries',$filters);}
 public function topScorers(int|string $league,int|string $season):array{return $this->get('players/topscorers',['league'=>$league,'season'=>$season]);}
 public function topAssists(int|string $league,int|string $season):array{return $this->get('players/topassists',['league'=>$league,'season'=>$season]);}
 private function get(string $endpoint,array $query=[]):array{if(!config('football.key'))throw new \RuntimeException('FOOTBALL_DATA_API_KEY is missing.');$r=$this->http->get($endpoint,['query'=>$query]);$j=json_decode((string)$r->getBody(),true,512,JSON_THROW_ON_ERROR);if(!empty($j['errors']))throw new \RuntimeException('Provider error: '.json_encode($j['errors']));return $j['response']??[];}
 public function fixtures(array $filters=[]):array{return $this->get('fixtures',$filters);} public function standings(int|string $competitionId,?string $season=null):array{return $this->get('standings',array_filter(['league'=>$competitionId,'season'=>$season]));} public function lineups(int|string $fixtureId):array{return $this->get('fixtures/lineups',['fixture'=>$fixtureId]);} public function events(int|string $fixtureId):array{return $this->get('fixtures/events',['fixture'=>$fixtureId]);} public function statistics(int|string $fixtureId):array{return $this->get('fixtures/statistics',['fixture'=>$fixtureId]);} public function players(array $filters=[]):array{return $this->get('players',$filters);} public function health():array{try{$this->get('status');return ['ok'=>true,'provider'=>$this->name(),'configured'=>(bool)config('football.key')];}catch(\Throwable $e){return ['ok'=>false,'provider'=>$this->name(),'configured'=>(bool)config('football.key'),'message'=>$e->getMessage()];}}
}
