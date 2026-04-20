#!/usr/bin/env bash

# Start all 3 projects from repo root
# Usage: bash javier/start-all.sh   (run from Git Bash, not PowerShell)

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
LOGS="$ROOT/javier/logs"
mkdir -p "$LOGS"

echo "[1/3] php-only → http://localhost:8001"
php -S localhost:8001 -t "$ROOT/javier/php-only" &> "$LOGS/php-only.log" &
PID_PHPONLY=$!

echo "[2/3] php-react → http://localhost:8002"
php -S localhost:8002 -t "$ROOT/javier/php-react" &> "$LOGS/php-react.log" &
PID_PHPREACT=$!

echo "[3/3] react-component PHP backend → http://localhost:8003"
php -S localhost:8003 -t "$ROOT/javier/react-component" &> "$LOGS/react-php.log" &
PID_REACTPHP=$!

echo "[3/3] react-component Vite dev → http://localhost:5173"
(cd "$ROOT/javier/react-component" && npm run dev) &> "$LOGS/react-vite.log" &
PID_VITE=$!

echo ""
echo "All started. PIDs: php-only=$PID_PHPONLY php-react=$PID_PHPREACT react-php=$PID_REACTPHP vite=$PID_VITE"
echo "Logs in: $LOGS"
echo "  tail -f $LOGS/react-vite.log"
echo ""
echo "Press Ctrl+C to stop all."

trap "echo 'Stopping...'; kill $PID_PHPONLY $PID_PHPREACT $PID_REACTPHP $PID_VITE 2>/dev/null; exit" INT TERM

wait
