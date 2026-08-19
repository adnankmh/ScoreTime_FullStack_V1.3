from pathlib import Path
import re, sys
root=Path(__file__).resolve().parents[1]
required=[
'backend-laravel/database/migrations/2026_08_19_000013_create_v08_visual_builder.php',
'backend-laravel/app/Services/DesignStudioService.php',
'backend-laravel/app/Http/Controllers/Admin/DesignStudioController.php',
'backend-laravel/app/Http/Controllers/Api/V1/DesignController.php',
'backend-laravel/resources/views/admin/design-studio/index.blade.php',
'backend-laravel/public/js/design-studio.js',
'mobile-flutter/lib/core/design/remote_design.dart',
'.github/workflows/v08-full-ci.yml',
]
missing=[x for x in required if not (root/x).exists()]
if missing:
 print('FAIL missing:',*missing,sep='\n- ');sys.exit(1)
web=(root/'backend-laravel/routes/web.php').read_text()
api=(root/'backend-laravel/routes/api.php').read_text()
assert 'design-studio' in web and '/design/bootstrap' in api
mig=(root/required[0]).read_text()
for table in ['design_profiles','page_layouts','design_versions','navigation_items']:
 assert table in mig
print('PASS V0.8 structural validation')
