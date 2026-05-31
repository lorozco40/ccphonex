#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
VENV_DIR="${FW_VENV_DIR:-$ROOT_DIR/.venv_transcripcion}"
LIMIT="${1:-${FW_LIMIT:-5}}"
MODEL="${2:-${FW_MODEL:-tiny}}"

if [[ ! -d "$VENV_DIR" ]]; then
    echo "No existe el entorno virtual en $VENV_DIR" >&2
    exit 1
fi

export AI_DB_HOST="${AI_DB_HOST:-${ASS_DB_HOST:-localhost}}"
export AI_DB_USER="${AI_DB_USER:-aldo}"
export AI_DB_PASS="${AI_DB_PASS:-4ss3rt1v3}"
export AI_DB_NAME="${AI_DB_NAME:-assertive}"
export FW_MODEL="$MODEL"

. "$VENV_DIR/bin/activate"
cd "$ROOT_DIR"

python scripts/worker_transcripcion.py --limit "$LIMIT" --model "$MODEL"