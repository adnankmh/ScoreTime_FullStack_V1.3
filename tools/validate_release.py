from pathlib import Path
import json, re, sys, yaml

root=Path(__file__).resolve().parents[1]
errors=[]

# 4 workflows
wfs=list((root/'.github/workflows').glob('*.yml'))
if len(wfs)!=4:
    errors.append(f"expected 4 workflows, found {len(wfs)}")
for p in wfs:
    try: yaml.safe_load(p.read_text(encoding='utf-8'))
    except Exception as e: errors.append(f"workflow YAML {p.name}: {e}")

# provider implementation
provider=(root/'backend-laravel/app/Services/Providers/ApiFootballProvider.php').read_text(encoding='utf-8')
for token in ['fixtures/players','predictions','trophies','sidelined','coachs']:
    if token not in provider: errors.append(f"API-Football provider missing {token}")

if not (root/'backend-laravel/app/Services/Providers/FootballDataOrgProvider.php').exists():
    errors.append('football-data.org provider missing')
if not (root/'backend-laravel/app/Services/RealtimeFootballSyncService.php').exists():
    errors.append('realtime global sync service missing')
if not (root/'backend-laravel/app/Console/Commands/SyncGlobalNews.php').exists():
    errors.append('news sync command missing')
if not (root/'backend-laravel/app/Console/Commands/SyncTodayFootball.php').exists():
    errors.append('today sync command missing')

# Security: do not ship keys
if (root/'backend-laravel/.env').exists():
    errors.append('.env must not be shipped')
env=(root/'backend-laravel/.env.example').read_text(encoding='utf-8')
for line in env.splitlines():
    if line.startswith('FOOTBALL_DATA_API_KEY=') and line.split('=',1)[1].strip():
        errors.append('real football API key shipped')
    if line.startswith('NEWS_API_KEY=') and line.split('=',1)[1].strip():
        errors.append('real NewsAPI key shipped')

# Languages
strings=(root/'mobile-flutter/lib/core/i18n/app_strings.dart').read_text(encoding='utf-8')
for lang in ["'en'","'ar'","'fr'","'es'","'de'","'tr'"]:
    if lang not in strings: errors.append(f'language missing {lang}')
for key in ['tv_guide','standings','lineups','tactical_analysis','data_status','transfer_radar']:
    if strings.count(f"'{key}'") < 6:
        errors.append(f'translation key incomplete: {key}')

# Top controls
main=(root/'mobile-flutter/lib/main.dart').read_text(encoding='utf-8')
if 'GlobalTopControls' not in main:
    errors.append('global top theme/language controls missing')
if "t('global_search')" not in main:
    errors.append('desktop search is not localized')

# Version
pub=(root/'mobile-flutter/pubspec.yaml').read_text(encoding='utf-8')
if 'version: 1.6.0+30' not in pub:
    errors.append('wrong Flutter version')

# no old MyApp
all_dart='\\n'.join(p.read_text(encoding='utf-8',errors='ignore') for p in (root/'mobile-flutter').rglob('*.dart'))
if re.search(r'\\bMyApp\\b', all_dart):
    errors.append('legacy MyApp reference')

print('ScoreTime V1.6.0 validator')
if errors:
    print('FAIL')
    for e in errors: print('-',e)
    sys.exit(1)
print('PASS')
print('Live football providers: API-Football + football-data.org')
print('Live news provider: NewsAPI')
print('UI languages: 6')
print('Theme/language controls: global top bar')
