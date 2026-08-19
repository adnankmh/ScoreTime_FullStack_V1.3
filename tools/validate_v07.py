from pathlib import Path
root=Path(__file__).resolve().parents[1]
required=[
'backend-laravel/app/Services/RealtimeMatchService.php','backend-laravel/app/Services/FcmPushService.php','backend-laravel/app/Models/LiveCommentary.php','backend-laravel/app/Models/PlayerHeatmapPoint.php','backend-laravel/app/Http/Controllers/Api/V1/RealtimeController.php','mobile-flutter/lib/features/realtime/presentation/realtime_match_screen.dart','.github/workflows/v07-full-ci.yml','RUN_V07_AR.md']
missing=[x for x in required if not (root/x).exists()]
if missing: raise SystemExit('Missing: '+', '.join(missing))
api=(root/'backend-laravel/routes/api.php').read_text()
for token in ['/realtime','/stream','transfer-intelligence','prediction-seasons','friend-activity']:
 if token not in api: raise SystemExit('Route token missing: '+token)
print(f'PASS: V0.7 structural checks ({len(required)}/{len(required)} required files)')
