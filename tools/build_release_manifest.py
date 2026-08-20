from datetime import datetime, timezone
from hashlib import sha256
import json
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
OUTPUT = ROOT / "RELEASE_MANIFEST_V172.json"
EXCLUDED_PARTS = {
    ".git",
    "vendor",
    "build",
    ".dart_tool",
    "__pycache__",
    "runtime-logs",
}


def included(path: Path) -> bool:
    relative = path.relative_to(ROOT)
    if any(part in EXCLUDED_PARTS for part in relative.parts):
        return False
    if path.name == ".env" or path.suffix.lower() in {".zip", ".pyc", ".pyo"}:
        return False
    if path.name.startswith("RELEASE_MANIFEST_") and path.suffix == ".json":
        return False
    return True


files = []
for path in sorted(item for item in ROOT.rglob("*") if item.is_file() and included(item)):
    payload = path.read_bytes()
    files.append({
        "path": path.relative_to(ROOT).as_posix(),
        "size": len(payload),
        "sha256": sha256(payload).hexdigest(),
    })

manifest = {
    "product": "ScoreTime Global",
    "version": "1.7.2",
    "flutter_build": "42",
    "generated_at_utc": datetime.now(timezone.utc).isoformat(),
    "truthfulness_mode": "real-data-by-default; preview-explicit-only",
    "counts": {
        "files": len(files),
        "php": sum(item["path"].endswith(".php") for item in files),
        "dart": sum(item["path"].endswith(".dart") for item in files),
        "workflows": sum(item["path"].startswith(".github/workflows/") for item in files),
        "web_locales": 6,
        "flutter_locales": 6,
    },
    "files": files,
}

OUTPUT.write_text(json.dumps(manifest, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
print(f"Wrote {OUTPUT.name} with {len(files)} files")
