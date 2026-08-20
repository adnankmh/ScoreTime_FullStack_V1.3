#!/usr/bin/env bash
set -euo pipefail

value="${1:-}"
value="${value#"${value%%[![:space:]]*}"}"
value="${value%"${value##*[![:space:]]}"}"

if [[ "$value" == \[*\]\(https://*\) ]]; then
  value="${value#*](}"
  value="${value%)}"
fi

if [[ "$value" == \"*\" && "$value" == *\" ]]; then
  value="${value:1:${#value}-2}"
elif [[ "$value" == \'*\' && "$value" == *\' ]]; then
  value="${value:1:${#value}-2}"
fi

while [[ "$value" == */ ]]; do
  value="${value%/}"
done

lower="${value,,}"
if [[ ! "$value" =~ ^https://[^/?#[:space:]]+(/[^?#[:space:]]*)?/api/v1$ ]]; then
  exit 2
fi
authority="${value#https://}"
authority="${authority%%/*}"
if [[ "$authority" == *"@"* ]]; then
  exit 2
fi
if [[ "$lower" == *"example.com"* ||
      "$lower" == *".invalid/"* ||
      "$lower" == *".test/"* ||
      "$lower" == *".example/"* ||
      "$lower" == *"localhost/"* ]]; then
  exit 2
fi

printf '%s' "$value"
