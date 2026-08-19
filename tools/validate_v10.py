from pathlib import Path
import re, sys
root=Path(__file__).resolve().parents[1]
required=[
'backend-laravel/database/migrations/2026_08_19_000015_create_v10_global_catalog.php',
'backend-laravel/app/Services/GlobalFootballCatalogService.php',
'backend-laravel/app/Console/Commands/SyncGlobalFootball.php',
'backend-laravel/app/Http/Controllers/Api/V1/WorldController.php',
'backend-laravel/app/Http/Controllers/Admin/WorldDataController.php',
'backend-laravel/app/Http/Middleware/ProductionSecurityHeaders.php',
'mobile-flutter/lib/features/world/presentation/global_football_screen.dart',
'.github/workflows/v10-production-ci.yml','RUN_V10_AR.md']
missing=[x for x in required if not (root/x).exists()]
if missing:
 print('FAIL missing:',*missing,sep='\n - ');sys.exit(1)
api=(root/'backend-laravel/routes/api.php').read_text()
for route in ['/world/summary','/world/countries','/world/competitions','/world/teams','/world/players']:
 if route not in api: print('FAIL route',route);sys.exit(1)
contract=(root/'backend-laravel/app/Contracts/FootballDataProvider.php').read_text()
for method in ['countries','leagues','teams','squads','fixtures','standings','players','transfers','injuries']:
 if f'function {method}' not in contract: print('FAIL provider method',method);sys.exit(1)
service=(root/'backend-laravel/app/Services/GlobalFootballCatalogService.php').read_text()
for token in ['PlayerSeasonStat::updateOrCreate','FootballMatch::updateOrCreate','Standing::create','competition_team']:
 if token not in service: print('FAIL sync token',token);sys.exit(1)
security=(root/'backend-laravel/bootstrap/app.php').read_text()
for token in ['ProductionSecurityHeaders','AdminIpAllowlist']:
 if token not in security: print('FAIL security',token);sys.exit(1)
print('PASS V1.0 structural validation')
