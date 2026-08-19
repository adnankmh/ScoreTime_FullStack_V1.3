<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\GlobalFootballCatalogService;
class SyncGlobalFootball extends Command {
 protected $signature='football:sync-global {scope=catalog : countries|catalog|league|players} {--country=} {--season=} {--league=} {--pages=10} {--no-players}';
 protected $description='Synchronize licensed global football catalog, teams and players from the configured provider.';
 public function handle(GlobalFootballCatalogService $sync): int {try{$scope=$this->argument('scope');$season=(int)($this->option('season')?:date('Y'));$result=match($scope){'countries'=>$sync->syncCountries(),'catalog'=>array_merge($sync->syncCountries(),$sync->syncCompetitions($this->option('country'),$this->option('season')?(int)$this->option('season'):null)),'league'=>$sync->syncLeague($this->requiredLeague(),$season,!$this->option('no-players')),'players'=>$sync->syncPlayerStats($this->requiredLeague(),$season,(int)$this->option('pages')),default=>throw new \InvalidArgumentException('Unknown scope')};$this->info(json_encode($result,JSON_PRETTY_PRINT));return self::SUCCESS;}catch(\Throwable $e){$this->error($e->getMessage());return self::FAILURE;}}
 private function requiredLeague(): string {if(!$this->option('league'))throw new \InvalidArgumentException('--league is required');return (string)$this->option('league');}
}
