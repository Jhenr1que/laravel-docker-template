#!/bin/sh
set -eu

ROOT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")/../.." && pwd)
ENV_FILE="$ROOT_DIR/.env"

if [ ! -f "$ENV_FILE" ]; then
    echo "Arquivo .env nao encontrado em $ROOT_DIR. Copie .env.example para .env antes de gerar a chave." >&2
    exit 1
fi

cd "$ROOT_DIR"

APP_KEY=$(docker compose run --rm --build app php artisan key:generate --show --no-interaction | tail -n 1 | tr -d '\r')

if [ -z "$APP_KEY" ]; then
    echo "Nao foi possivel gerar APP_KEY." >&2
    exit 1
fi

TMP_FILE=$(mktemp)

cleanup() {
    rm -f "$TMP_FILE"
}

trap cleanup EXIT

awk -v app_key="$APP_KEY" '
BEGIN { updated = 0 }
/^APP_KEY=/ {
    print "APP_KEY=" app_key
    updated = 1
    next
}
{
    print
}
END {
    if (!updated) {
        print "APP_KEY=" app_key
    }
}
' "$ENV_FILE" > "$TMP_FILE"

mv -f "$TMP_FILE" "$ENV_FILE"

echo "APP_KEY atualizada em $ENV_FILE"
