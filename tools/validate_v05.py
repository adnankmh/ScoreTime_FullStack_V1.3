from pathlib import Path
import sys
root=Path(__file__).resolve().parents[1]
required=[
 'backend-laravel/app/Services/MatchIntelligenceService.php',
 'backend-laravel/app/Http/Controllers/Api/V1/MatchIntelligenceController.php',
 'backend-laravel/database/migrations/2026_08_19_000010_create_v05_elite_features.php',
 'mobile-flutter/lib/features/matches/presentation/match_detail_screen.dart',
 'mobile-flutter/lib/features/social/presentation/social_hub_screen.dart',
 '.github/workflows/flutter-v05-release.yml','RUN_V05_AR.md','RELEASE_NOTES_V05_AR.md'
]
missing=[x for x in required if not (root/x).exists()]
print(f'V0.5 required files: {len(required)-len(missing)}/{len(required)}')
if missing:
    print('Missing:',*missing,sep='\n- ');sys.exit(1)
# Catch a migration regression already found during V0.5 work.
m=(root/'backend-laravel/database/migrations/2026_08_19_000010_create_v05_elite_features.php').read_text()
if "Schema::table('device_sessions'" in m:
    print('FAIL: V0.5 migration must not re-add existing device_sessions columns');sys.exit(2)
print('PASS: V0.5 structural checks')
