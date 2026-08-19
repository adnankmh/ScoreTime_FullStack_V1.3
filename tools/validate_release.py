from pathlib import Path
import json, re, sys, yaml

root=Path(__file__).resolve().parents[1]
errors=[]

expected={
 "01-laravel.yml":"ScoreTime • Laravel",
 "02-android-apk.yml":"ScoreTime • Android APK",
 "03-android-aab.yml":"ScoreTime • Android AAB",
 "04-ios.yml":"ScoreTime • iOS",
}
active=list((root/'.github/workflows').glob('*.y*ml'))
if {p.name for p in active} != set(expected):
    errors.append(f"active workflows mismatch: {[p.name for p in active]}")
for p in active:
    try:
        data=yaml.safe_load(p.read_text(encoding='utf-8'))
        if data.get('name') != expected[p.name]:
            errors.append(f"wrong display name in {p.name}")
    except Exception as e:
        errors.append(f"invalid YAML {p.name}: {e}")

legacy=list((root/'archive').rglob('*.yml'))+list((root/'archive').rglob('*.yaml'))
if legacy:
    errors.append(f"legacy workflow YAML remains: {[str(p.relative_to(root)) for p in legacy]}")

composer=json.loads((root/'backend-laravel/composer.json').read_text(encoding='utf-8'))
autoload_dev=composer.get('autoload-dev',{}).get('psr-4',{})
if autoload_dev.get('Tests\\') != 'tests/':
    errors.append("Composer autoload-dev Tests namespace missing")

testcase=(root/'backend-laravel/tests/TestCase.php').read_text(encoding='utf-8')
if 'namespace Tests;' not in testcase or 'abstract class TestCase extends BaseTestCase' not in testcase:
    errors.append("tests/TestCase.php invalid")

pub=(root/'mobile-flutter/pubspec.yaml').read_text(encoding='utf-8')
if 'version: 1.4.3+17' not in pub:
    errors.append('wrong Flutter version')
if not re.search(r'(?m)^\s*intl:\s*\^0\.20\.3\s*$',pub):
    errors.append('intl must be ^0.20.3')

for p in (root/'mobile-flutter/lib').rglob('*.dart'):
    text=p.read_text(encoding='utf-8',errors='ignore')
    if '.withOpacity(' in text:
        errors.append(f"deprecated withOpacity: {p.relative_to(root)}")

for rel in [
 'tooling/ios-appicon/Contents.json',
 'tooling/android-icons/mdpi/ic_launcher.png',
 'branding/scoretime-icon-master.png',
 'mobile-flutter/assets/lottie/scoretime_pulse.json',
 'backend-laravel/public/index.php',
]:
    if not (root/rel).exists():
        errors.append(f"missing {rel}")

if (root/'backend-laravel/.env').exists():
    errors.append('.env must not ship')

print("ScoreTime V1.4.3 validator")
if errors:
    print("FAIL")
    for e in errors: print("-",e)
    sys.exit(1)
print("PASS")
print("Active workflows: 4")
print("Laravel Tests autoload: configured")
