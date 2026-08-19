from pathlib import Path
import re, sys, yaml, json

root = Path(__file__).resolve().parents[1]
errors=[]

wfs=list((root/'.github/workflows').glob('*.yml'))
if len(wfs)!=3:
    errors.append(f"expected 3 workflows, found {len(wfs)}")

for p in wfs:
    try:
        yaml.safe_load(p.read_text(encoding='utf-8'))
    except Exception as e:
        errors.append(f"invalid YAML {p.name}: {e}")

legacy=list((root/'archive').rglob('*.yml'))+list((root/'archive').rglob('*.yaml'))
if legacy:
    errors.append("legacy executable workflow YAML remains")

launcher=(root/'START_SCORETIME_WINDOWS.ps1').read_text(encoding='utf-8-sig')
for token in [
    'function Test-TcpPort',
    'function Wait-ForMySQL',
    'function Start-MySQLAutomatic',
    'mysql_start.bat',
    'mysqld.exe',
    'SELECT 1;',
    'CREATE DATABASE IF NOT EXISTS football_global',
    'php artisan migrate --force',
]:
    if token not in launcher:
        errors.append(f"launcher missing {token}")

if 'Test-NetConnection' in launcher:
    errors.append("launcher still uses noisy Test-NetConnection")

if 'migrate:fresh' in launcher:
    errors.append("launcher must not wipe database")

pub=(root/'mobile-flutter/pubspec.yaml').read_text(encoding='utf-8')
if 'version: 1.4.6+20' not in pub:
    errors.append("wrong Flutter version")

all_dart="\n".join(p.read_text(encoding='utf-8',errors='ignore') for p in (root/'mobile-flutter').rglob('*.dart'))
if re.search(r'\bMyApp\b', all_dart):
    errors.append("legacy MyApp reference remains")

if (root/'backend-laravel/.env').exists():
    errors.append(".env must not be shipped")

print("ScoreTime V1.4.6 validator")
if errors:
    print("FAIL")
    for e in errors: print("-", e)
    sys.exit(1)

print("PASS")
print("Workflows: 3")
print("MySQL auto-start fallback methods: 2")
print("Database destructive reset: disabled")
