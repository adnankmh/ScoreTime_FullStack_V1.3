from pathlib import Path
import json, re, sys, yaml

root=Path(__file__).resolve().parents[1]
errors=[]

expected={
 "01-laravel.yml":"ScoreTime • Laravel",
 "02-android-apk-aab.yml":"ScoreTime • Android APK + AAB",
 "03-ios.yml":"ScoreTime • iOS",
}

active=list((root/'.github/workflows').glob('*.y*ml'))
if {p.name for p in active} != set(expected):
    errors.append(f"active workflows mismatch: {[p.name for p in active]}")

for p in active:
    try:
        data=yaml.safe_load(p.read_text(encoding='utf-8'))
        if data.get('name') != expected[p.name]:
            errors.append(f"wrong workflow display name in {p.name}")
    except Exception as e:
        errors.append(f"invalid YAML {p.name}: {e}")

legacy=list((root/'archive').rglob('*.yml'))+list((root/'archive').rglob('*.yaml'))
if legacy:
    errors.append("legacy executable workflow YAML remains")

android=(root/'.github/workflows/02-android-apk-aab.yml').read_text(encoding='utf-8')
for token in [
    'actions/checkout@v6',
    'actions/setup-java@v5',
    'actions/upload-artifact@v6',
    'flutter build apk --release',
    'flutter build appbundle --release',
]:
    if token not in android:
        errors.append(f"Android workflow missing {token}")

if 'cache: gradle' in android or "cache: 'gradle'" in android:
    errors.append("Gradle cache must not be enabled in setup-java before stable Android scaffold")

if android.find('Generate Android platform when missing') > android.find('Java 17'):
    errors.append("Android scaffold must be generated before setup-java")

ios=(root/'.github/workflows/03-ios.yml').read_text(encoding='utf-8')
if 'actions/checkout@v6' not in ios or 'actions/upload-artifact@v6' not in ios:
    errors.append("iOS actions are not Node-24 generation")

laravel=(root/'.github/workflows/01-laravel.yml').read_text(encoding='utf-8')
if 'actions/checkout@v6' not in laravel:
    errors.append("Laravel checkout is outdated")

pub=(root/'mobile-flutter/pubspec.yaml').read_text(encoding='utf-8')
if 'version: 1.4.4+18' not in pub:
    errors.append("wrong Flutter version")
if not re.search(r'(?m)^\s*intl:\s*\^0\.20\.3\s*$', pub):
    errors.append("intl must remain ^0.20.3")

composer=json.loads((root/'backend-laravel/composer.json').read_text(encoding='utf-8'))
if composer.get('autoload-dev',{}).get('psr-4',{}).get('Tests\\') != 'tests/':
    errors.append("Tests autoload-dev missing")

if (root/'backend-laravel/.env').exists():
    errors.append(".env must not be shipped")

print("ScoreTime V1.4.4 validator")
if errors:
    print("FAIL")
    for e in errors: print("-", e)
    sys.exit(1)

print("PASS")
print("Active workflows: 3")
print("Android workflow: APK + AAB merged")
print("setup-java Gradle cache: disabled")
print("Node-24 generation actions: enabled")
