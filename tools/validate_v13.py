from pathlib import Path
root=Path(__file__).resolve().parents[1]
required=[
 'backend-laravel/config/scoretime_v13.php',
 'backend-laravel/app/Http/Controllers/Api/V1/ReleaseCandidateController.php',
 'backend-laravel/app/Http/Controllers/Admin/EditorialWorkflowController.php',
 'backend-laravel/database/migrations/2026_08_19_130000_scoretime_v13_release_candidate.php',
 'mobile-flutter/lib/features/release_candidate/presentation/scoretime_v13_hub.dart',
 '.github/workflows/v13-release-candidate.yml',
 'RUN_V13_AR.md'
]
missing=[p for p in required if not (root/p).exists()]
if missing:
 print('FAIL'); [print('missing:',p) for p in missing]; raise SystemExit(1)
print('PASS: ScoreTime V1.3 required release files')
