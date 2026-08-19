from pathlib import Path
import re,sys
root=Path(__file__).resolve().parents[1]
required=[
 'backend-laravel/database/migrations/2026_08_19_000014_create_v09_no_code_experience.php',
 'backend-laravel/app/Http/Controllers/Admin/NoCodeStudioController.php',
 'backend-laravel/app/Services/NoCodeExperienceService.php',
 'backend-laravel/app/Services/DynamicBlockService.php',
 'backend-laravel/resources/views/admin/no-code-studio/index.blade.php',
 'mobile-flutter/lib/features/custom_pages/presentation/dynamic_page_screen.dart',
 '.github/workflows/v09-full-ci.yml','RUN_V09_AR.md']
missing=[x for x in required if not (root/x).exists()]
if missing: print('FAIL missing:',*missing,sep='\n- ');sys.exit(1)
web=(root/'backend-laravel/routes/web.php').read_text();api=(root/'backend-laravel/routes/api.php').read_text()
checks=['no-code-studio','NoCodeStudioController','custom-page.show']
for x in checks:
 if x not in web: print('FAIL web route reference',x);sys.exit(1)
for x in ['ExperienceController','/pages/{customPage:slug}','/experience/bootstrap']:
 if x not in api: print('FAIL api route reference',x);sys.exit(1)
mig=(root/required[0]).read_text()
for table in ['custom_pages','design_schedules','design_experiments','notification_campaigns','white_label_profiles','menu_nodes']:
 if table not in mig: print('FAIL migration table',table);sys.exit(1)
print('PASS: V0.9 structural checks')
