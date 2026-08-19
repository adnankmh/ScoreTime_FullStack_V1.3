from pathlib import Path
import json, re, sys, yaml

root=Path(__file__).resolve().parents[1]
errors=[]

expected={
 "01-laravel.yml":"ScoreTime • Laravel",
 "02-web-pages.yml":"ScoreTime • Web / GitHub Pages",
 "03-android-apk-aab.yml":"ScoreTime • Android APK + AAB",
 "04-ios.yml":"ScoreTime • iOS",
}
active=list((root/'.github/workflows').glob('*.yml'))
if {p.name for p in active} != set(expected):
    errors.append(f"workflow set mismatch: {[p.name for p in active]}")

for p in active:
    try:
        data=yaml.safe_load(p.read_text(encoding='utf-8'))
        if data.get('name') != expected[p.name]:
            errors.append(f"wrong workflow display name: {p.name}")
    except Exception as e:
        errors.append(f"invalid YAML {p.name}: {e}")

legacy=list((root/'archive').rglob('*.yml'))+list((root/'archive').rglob('*.yaml'))
if legacy:
    errors.append("legacy executable workflow YAML remains")

webwf=(root/'.github/workflows/02-web-pages.yml').read_text(encoding='utf-8')
for token in [
 'actions/configure-pages@v6',
 'actions/upload-pages-artifact@v5',
 'actions/deploy-pages@v5',
 '--base-href "/ScoreTime/"',
 'WEB_DEMO_MODE',
 'SCORETIME_API_BASE_URL',
]:
    if token not in webwf:
        errors.append(f"Web workflow missing {token}")

for rel in [
 'mobile-flutter/web/index.html',
 'mobile-flutter/web/manifest.json',
 'mobile-flutter/web/favicon.png',
 'mobile-flutter/web/icons/Icon-192.png',
 'mobile-flutter/web/icons/Icon-512.png',
 'mobile-flutter/lib/core/network/demo_data.dart',
 'mobile-flutter/lib/core/config/app_config.dart',
 'docs/GITHUB_PAGES_V147_AR.md',
]:
    if not (root/rel).exists():
        errors.append(f"missing {rel}")

config=(root/'mobile-flutter/lib/core/config/app_config.dart').read_text(encoding='utf-8')
if 'webDemoMode' not in config:
    errors.append('WEB_DEMO_MODE app config missing')

repo=(root/'mobile-flutter/lib/core/network/football_repository.dart').read_text(encoding='utf-8')
if 'DemoData.matches' not in repo or 'AppConfig.webDemoMode' not in repo:
    errors.append('demo-aware repository missing')

main=(root/'mobile-flutter/lib/main.dart').read_text(encoding='utf-8')
if 'NavigationRail' not in main or 'constraints.maxWidth >= 980' not in main:
    errors.append('responsive desktop shell missing')

home=(root/'mobile-flutter/lib/features/home/presentation/home_screen.dart').read_text(encoding='utf-8')
if 'AppConfig.webDemoMode' not in home or 'DemoData.news' not in home:
    errors.append('Home web demo fallback missing')

env=(root/'backend-laravel/.env.example').read_text(encoding='utf-8')
if 'https://adnankmh.github.io' not in env:
    errors.append('GitHub Pages origin missing from Laravel CORS defaults')

pub=(root/'mobile-flutter/pubspec.yaml').read_text(encoding='utf-8')
if 'version: 1.4.7+21' not in pub:
    errors.append('wrong Flutter version')

all_dart="\n".join(p.read_text(encoding='utf-8',errors='ignore') for p in (root/'mobile-flutter').rglob('*.dart'))
if re.search(r'\bMyApp\b', all_dart):
    errors.append('legacy MyApp reference remains')

if (root/'backend-laravel/.env').exists():
    errors.append('.env must not be shipped')

print('ScoreTime V1.4.7 validator')
if errors:
    print('FAIL')
    for e in errors: print('-', e)
    sys.exit(1)

print('PASS')
print('Active workflows: 4')
print('GitHub Pages workflow: ready')
print('Professional web demo fallback: ready')
print('Responsive desktop shell: ready')
