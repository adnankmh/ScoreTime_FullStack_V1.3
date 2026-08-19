from pathlib import Path
import json, re, sys, yaml

root = Path(__file__).resolve().parents[1]
errors = []

active = list((root / ".github/workflows").glob("*.y*ml"))
if len(active) != 1:
    errors.append(f"expected exactly 1 active workflow, found {len(active)}")
elif active[0].name != "scoretime.yml":
    errors.append(f"active workflow must be scoretime.yml, found {active[0].name}")

for p in active:
    try:
        data = yaml.safe_load(p.read_text(encoding="utf-8"))
        if data.get("name") != "ScoreTime • Verify & Android":
            errors.append("unexpected workflow display name")
    except Exception as e:
        errors.append(f"invalid workflow YAML: {e}")

# No executable legacy workflows anywhere in archive.
legacy_yamls = list((root / "archive").rglob("*.yml")) + list((root / "archive").rglob("*.yaml"))
if legacy_yamls:
    errors.append("legacy executable YAML workflows still exist in archive")

required = [
    "mobile-flutter/pubspec.yaml",
    "mobile-flutter/assets/lottie/scoretime_pulse.json",
    "mobile-flutter/assets/icons/scoretime_icon.png",
    "branding/scoretime-logo-approved.png",
    "branding/scoretime-icon-master.png",
    "tooling/android-icons/mdpi/ic_launcher.png",
    "tooling/android-icons/hdpi/ic_launcher.png",
    "tooling/android-icons/xhdpi/ic_launcher.png",
    "tooling/android-icons/xxhdpi/ic_launcher.png",
    "tooling/android-icons/xxxhdpi/ic_launcher.png",
    "backend-laravel/public/index.php",
    "backend-laravel/.env.example",
]
for rel in required:
    if not (root / rel).exists():
        errors.append(f"missing {rel}")

pub = (root / "mobile-flutter/pubspec.yaml").read_text(encoding="utf-8")
if "version: 1.4.2+16" not in pub:
    errors.append("Flutter version not 1.4.2+16")
if not re.search(r"(?m)^\s*intl:\s*\^0\.20\.3\s*$", pub):
    errors.append("intl is not ^0.20.3")

workflow_text = (root / ".github/workflows/scoretime.yml").read_text(encoding="utf-8")
if "php artisan test" in workflow_text:
    errors.append("workflow still uses php artisan test")
if "flutter analyze" not in workflow_text or "flutter test" not in workflow_text:
    errors.append("Flutter verification steps missing")
if "flutter build apk --release" not in workflow_text:
    errors.append("APK build step missing")
if "flutter build appbundle --release" not in workflow_text:
    errors.append("AAB build step missing")

dart_files = list((root / "mobile-flutter/lib").rglob("*.dart"))
for p in dart_files:
    text = p.read_text(encoding="utf-8", errors="ignore")
    if ".withOpacity(" in text:
        errors.append(f"deprecated withOpacity in {p.relative_to(root)}")

if (root / "backend-laravel/.env").exists():
    errors.append(".env must not be shipped")

print("ScoreTime V1.4.2 validator")
if errors:
    print("FAIL")
    for e in errors:
        print("-", e)
    sys.exit(1)

print("PASS")
print("Active workflows: 1")
print("Workflow: ScoreTime • Verify & Android")
print("Dart files:", len(dart_files))
