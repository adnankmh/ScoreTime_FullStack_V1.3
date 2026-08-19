from pathlib import Path
import re,sys
root=Path(__file__).resolve().parents[1]
required=['backend-laravel/app/Services/MatchVisualService.php','backend-laravel/app/Http/Controllers/Api/V1/VisualMatchController.php','backend-laravel/database/migrations/2026_08_19_000011_create_v06_realtime_visual_features.php','mobile-flutter/lib/features/matches/presentation/visual_match_widgets.dart','mobile-flutter/lib/features/discovery/presentation/discovery_screen.dart','RUN_V06_AR.md','.github/workflows/v06-full-ci.yml']
missing=[x for x in required if not (root/x).exists()]
if missing: print('FAIL missing',missing);sys.exit(1)
api=(root/'backend-laravel/routes/api.php').read_text()
for needle in ['/visual','/search/trending','/challenges','/premium/status']:
 if needle not in api: print('FAIL missing route',needle);sys.exit(1)
mig=(root/'backend-laravel/database/migrations/2026_08_19_000011_create_v06_realtime_visual_features.php').read_text()
for table in ['match_shots','match_momentum_points','user_challenges','user_levels','search_trends','premium_entitlements']:
 if f"Schema::create('{table}'" not in mig: print('FAIL table',table);sys.exit(1)
print('PASS: V0.6 structural checks')
