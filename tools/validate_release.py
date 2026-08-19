from pathlib import Path
import re, sys, yaml
root=Path(__file__).resolve().parents[1]
errors=[]
required=[
    'backend-laravel/public/index.php','backend-laravel/.env.example',
    'mobile-flutter/pubspec.yaml','mobile-flutter/assets/icons/scoretime_icon.png',
    'branding/scoretime-logo-approved.png','branding/scoretime-icon-master.png',
    '.github/workflows/01-quality-gate.yml','.github/workflows/02-android-release.yml',
]
for rel in required:
    if not (root/rel).exists():errors.append(f'missing {rel}')
# workflows: exactly 2 active
workflows=list((root/'.github/workflows').glob('*.y*ml'))
if len(workflows)!=2:errors.append(f'expected 2 active workflows, found {len(workflows)}')
for p in workflows:
    try: yaml.safe_load(p.read_text(encoding='utf-8'))
    except Exception as e: errors.append(f'invalid yaml {p.name}: {e}')
# Known CI regressions from prior release
pub=(root/'mobile-flutter/pubspec.yaml').read_text(encoding='utf-8')
if not re.search(r'^\s*intl:\s*\^0\.20\.3\s*$',pub,re.M):errors.append('intl must be ^0.20.3')
for p in workflows:
    s=p.read_text(encoding='utf-8')
    if 'php artisan test' in s:errors.append(f'{p.name} still uses unavailable php artisan test')
# secrets not tracked in source tree root
if (root/'backend-laravel/.env').exists():errors.append('backend-laravel/.env must not be shipped')
# old active branding names in runtime source
runtime_dirs=[root/'backend-laravel/app',root/'backend-laravel/config',root/'backend-laravel/resources/views',root/'mobile-flutter/lib']
pat=re.compile(r'KoraOne|Football Global|Global V0\.|V0\.[0-9]')
for d in runtime_dirs:
    for p in d.rglob('*'):
        if p.is_file() and p.suffix in {'.php','.dart'} or p.name.endswith('.blade.php'):
            try:s=p.read_text(encoding='utf-8')
            except:continue
            if pat.search(s):errors.append(f'legacy visible brand/version in {p.relative_to(root)}')
print('ScoreTime V1.4 release validator')
if errors:
    print('FAIL')
    for e in errors:print('-',e)
    sys.exit(1)
print('PASS')
print('Active workflows:',', '.join(p.name for p in workflows))
