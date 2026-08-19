from pathlib import Path
import re
import sys

import yaml


ROOT = Path(__file__).resolve().parents[1]
ERRORS: list[str] = []


def check(condition: bool, message: str) -> None:
    if not condition:
        ERRORS.append(message)


def code_only(source: str, hash_comments: bool = False) -> str:
    """Remove comments and quoted text while preserving structural characters."""
    output: list[str] = []
    i = 0
    size = len(source)
    state = "code"
    quote = ""
    triple = False
    while i < size:
        char = source[i]
        pair = source[i:i + 2]
        if state == "code":
            if pair == "//" or (hash_comments and char == "#"):
                state = "line"
                output.append(" ")
            elif pair == "/*":
                state = "block"
                output.append(" ")
                i += 1
            elif char in "'\"":
                quote = char
                triple = source[i:i + 3] == char * 3
                state = "string"
                output.append(" ")
                if triple:
                    i += 2
            else:
                output.append(char)
        elif state == "line":
            if char == "\n":
                state = "code"
                output.append("\n")
            else:
                output.append(" ")
        elif state == "block":
            if pair == "*/":
                state = "code"
                output.extend([" ", " "])
                i += 1
            else:
                output.append("\n" if char == "\n" else " ")
        else:
            if char == "\\":
                output.extend([" ", " "])
                i += 1
            elif triple and source[i:i + 3] == quote * 3:
                output.extend([" ", " ", " "])
                i += 2
                state = "code"
            elif not triple and char == quote:
                output.append(" ")
                state = "code"
            else:
                output.append("\n" if char == "\n" else " ")
        i += 1
    return "".join(output)


def balanced(path: Path, hash_comments: bool = False) -> None:
    source = code_only(path.read_text(encoding="utf-8-sig", errors="ignore"), hash_comments)
    stack: list[tuple[str, int]] = []
    pairs = {")": "(", "]": "[", "}": "{"}
    openings = set(pairs.values())
    line = 1
    for char in source:
        if char == "\n":
            line += 1
        elif char in openings:
            stack.append((char, line))
        elif char in pairs:
            if not stack or stack[-1][0] != pairs[char]:
                ERRORS.append(f"unbalanced {char} in {path.relative_to(ROOT)}:{line}")
                return
            stack.pop()
    if stack:
        ERRORS.append(f"unclosed {stack[-1][0]} in {path.relative_to(ROOT)}:{stack[-1][1]}")


env = (ROOT / "backend-laravel/.env.example").read_text(encoding="utf-8-sig")
check('NEWS_QUERY="football OR soccer"' in env, "NEWS_QUERY is not dotenv-safe")
check("FOOTBALL_FREE_DAILY_RESERVE=20" in env, "free daily reserve is not protected")
check('FOOTBALL_FREE_TODAY_CRON="5 0 * * *"' in env, "today sync is not one call per day")
check("FOOTBALL_FREE_LIVE_DAILY_CAP=40" in env, "live daily bucket cap missing")
check("FOOTBALL_FREE_DETAIL_DAILY_CAP=20" in env, "details daily bucket cap missing")
check("SCORETIME_SEED_DEMO_DATA=false" in env, "demo seed is not opt-in")
check(re.search(r"(?m)^ADMIN_PASSWORD=$", env) is not None, "a predictable administrator password is shipped")
check("SESSION_ENCRYPT=true" in env, "encrypted sessions are not the default")
check(not (ROOT / "backend-laravel/.env").exists(), "backend .env was shipped")
for line in env.splitlines():
    if line.startswith(("FOOTBALL_DATA_API_KEY=", "FOOTBALL_DATA_ORG_KEY=", "NEWS_API_KEY=")):
        check(line.split("=", 1)[1].strip() == "", f"secret value shipped in {line.split('=', 1)[0]}")


expected_workflows = {"01-laravel.yml", "02-web-pages.yml", "03-android-apk-aab.yml", "04-ios.yml"}
workflow_files = list((ROOT / ".github/workflows").glob("*.yml"))
check({path.name for path in workflow_files} == expected_workflows, "active workflow set differs from the four supported workflows")
for path in workflow_files:
    try:
        yaml.safe_load(path.read_text(encoding="utf-8"))
    except Exception as error:
        ERRORS.append(f"invalid workflow YAML {path.name}: {error}")

android_workflow = (ROOT / ".github/workflows/03-android-apk-aab.yml").read_text(encoding="utf-8")
web_workflow = (ROOT / ".github/workflows/02-web-pages.yml").read_text(encoding="utf-8")
check("ScoreTime-V1.7.0-Android" in android_workflow, "Android artifact version is stale")
check("android.permission.INTERNET" in android_workflow, "release Android INTERNET permission guard missing")
check("example.com" in android_workflow and "exit 1" in android_workflow, "Android real API URL gate missing")
check("will not silently publish demo scores" in web_workflow, "GitHub Pages silently enables preview scores")
for path in workflow_files:
    body = path.read_text(encoding="utf-8")
    check("actions/checkout@v6" in body, f"{path.name} does not use checkout v6")


php_paths = []
for directory in ["app", "bootstrap", "config", "database", "routes", "tests"]:
    php_paths.extend((ROOT / "backend-laravel" / directory).rglob("*.php"))
for path in php_paths:
    check(path.read_text(encoding="utf-8-sig", errors="ignore").lstrip().startswith("<?php"), f"missing PHP opener: {path.relative_to(ROOT)}")
    balanced(path, hash_comments=True)

service_text = "\n".join(path.read_text(encoding="utf-8", errors="ignore") for path in (ROOT / "backend-laravel/app/Services").rglob("*.php"))
check("Cache::flush()" not in service_text, "a service can still erase the entire cache and quota counters")
check("Adnan123" not in "\n".join(path.read_text(encoding="utf-8", errors="ignore") for path in ROOT.rglob("*.php")), "legacy administrator password remains")

api_provider = (ROOT / "backend-laravel/app/Services/Providers/ApiFootballProvider.php").read_text(encoding="utf-8")
secondary_provider = (ROOT / "backend-laravel/app/Services/Providers/FootballDataOrgProvider.php").read_text(encoding="utf-8")
sync_service = (ROOT / "backend-laravel/app/Services/RealtimeFootballSyncService.php").read_text(encoding="utf-8")
catalog_service = (ROOT / "backend-laravel/app/Services/GlobalFootballCatalogService.php").read_text(encoding="utf-8")
check("get('status'" not in api_provider, "public provider health can still spend API-Football quota")
check("mode' => 'passive'" in api_provider and "mode' => 'passive'" in secondary_provider, "passive provider health is missing")
check("FootballStatus::canonical" in sync_service and "FootballStatus::canonical" in catalog_service, "canonical match status mapping is incomplete")
check("'realtime_state' => FootballStatus::isLive" in sync_service, "database-safe realtime state mapping is missing")
check((ROOT / "backend-laravel/app/Console/Commands/SyncPriorityMatchDetails.php").exists(), "priority detail synchronization command missing")
check((ROOT / "backend-laravel/app/Console/Commands/SyncFeaturedFootball.php").exists(), "free featured-catalog rotation command missing")


locale_keys: dict[str, set[str]] = {}
for locale in ["en", "ar", "fr", "es", "de", "tr"]:
    path = ROOT / f"backend-laravel/lang/{locale}/ui.php"
    check(path.exists(), f"missing web locale {locale}")
    if path.exists():
        keys = re.findall(r"(?m)^\s*'([^']+)'\s*=>", path.read_text(encoding="utf-8"))
        check(len(keys) == len(set(keys)), f"duplicate translation key in {locale}")
        locale_keys[locale] = set(keys)
if locale_keys:
    expected = locale_keys.get("en", set())
    for locale, keys in locale_keys.items():
        check(keys == expected, f"web translation parity mismatch: {locale}")


dart_paths = list((ROOT / "mobile-flutter/lib").rglob("*.dart")) + list((ROOT / "mobile-flutter/test").rglob("*.dart"))
for path in dart_paths:
    balanced(path)
    source = path.read_text(encoding="utf-8", errors="ignore")
    for spec in re.findall(r"^import\s+['\"]([^'\"]+)['\"]", source, re.MULTILINE):
        target = None
        if spec.startswith("package:scoretime/"):
            target = ROOT / "mobile-flutter/lib" / spec.removeprefix("package:scoretime/")
        elif spec.startswith("."):
            target = (path.parent / spec).resolve()
        if target is not None:
            check(target.exists(), f"unresolved Dart import {spec} in {path.relative_to(ROOT)}")

home = (ROOT / "mobile-flutter/lib/features/home/presentation/home_screen.dart").read_text(encoding="utf-8")
detail = (ROOT / "mobile-flutter/lib/features/matches/presentation/match_detail_screen.dart").read_text(encoding="utf-8")
app_strings = (ROOT / "mobile-flutter/lib/core/i18n/app_strings.dart").read_text(encoding="utf-8")
check("DemoData" not in home, "home screen still substitutes demo scores after a live error")
check("58%" not in home and "24%" not in home and "18%" not in home, "fixed win probabilities remain on the live home screen")
check(".clamp(10, 120).toInt()" in detail, "match polling duration has the old num/int compile risk")
check("commentaries" in detail, "real synchronized commentary is not rendered")
check("static const supported = ['en', 'ar', 'fr', 'es', 'de', 'tr']" in app_strings, "Flutter six-language list changed")


version = (ROOT / "VERSION").read_text(encoding="utf-8").strip()
pubspec = (ROOT / "mobile-flutter/pubspec.yaml").read_text(encoding="utf-8")
check(version == "1.7.0", "root VERSION is not 1.7.0")
check("version: 1.7.0+40" in pubspec, "Flutter build version is not 1.7.0+40")


print("ScoreTime V1.7.0 release validator")
print(f"PHP structures scanned: {len(php_paths)}")
print(f"Dart structures scanned: {len(dart_paths)}")
print(f"Workflows parsed: {len(workflow_files)}")
if ERRORS:
    print("FAIL")
    for error in ERRORS:
        print("-", error)
    sys.exit(1)

print("PASS")
print("Real-data truthfulness, free quota gates, six locales, workflow safety and structural checks passed.")
