#!/bin/sh
set -e

TARGET_DIR="/var/www/html"
SOURCE_DIR="/usr/src/wordpress"
WP_PATH="--allow-root --path=$TARGET_DIR"

if [ ! -f "$TARGET_DIR/index.php" ] || [ ! -d "$TARGET_DIR/wp-admin" ] || [ ! -d "$TARGET_DIR/wp-includes" ]; then
    echo "[wordpress-init] Populating local WordPress directory..."
    tar cf - -C "$SOURCE_DIR" . | tar xf - -C "$TARGET_DIR"
    chown -R www-data:www-data "$TARGET_DIR"
else
    echo "[wordpress-init] Local WordPress directory already populated."
fi

if [ ! -f "$TARGET_DIR/wp-config.php" ] && [ -f "$TARGET_DIR/wp-config-docker.php" ]; then
    echo "[wordpress-init] Creating wp-config.php from wp-config-docker.php..."
    cp "$TARGET_DIR/wp-config-docker.php" "$TARGET_DIR/wp-config.php"
    chown www-data:www-data "$TARGET_DIR/wp-config.php"
fi

if [ "${WP_AUTO_INSTALL:-true}" = "true" ] && [ -f "$TARGET_DIR/wp-config.php" ]; then
    ATTEMPTS=0
    MAX_ATTEMPTS="${WP_DB_WAIT_MAX_ATTEMPTS:-60}"

    echo "[wordpress-init] Waiting for MariaDB to accept connections..."

    until wp $WP_PATH db check >/dev/null 2>&1; do
        ATTEMPTS=$((ATTEMPTS + 1))

        if [ "$ATTEMPTS" -ge "$MAX_ATTEMPTS" ]; then
            echo "[wordpress-init] MariaDB did not become ready in time."
            exit 1
        fi

        sleep 2
    done

    if wp $WP_PATH core is-installed >/dev/null 2>&1; then
        echo "[wordpress-init] WordPress is already installed."
    else
        echo "[wordpress-init] Running first-time WordPress installation..."
        wp $WP_PATH core install \
            --url="${WP_HOME_URL:-http://localhost:8081}" \
            --title="${WP_SITE_TITLE:-Institucional Base}" \
            --admin_user="${WP_ADMIN_USER:-admin}" \
            --admin_password="${WP_ADMIN_PASSWORD:-admin123456}" \
            --admin_email="${WP_ADMIN_EMAIL:-admin@example.com}" \
            --skip-email
    fi
fi

exec docker-entrypoint.sh "$@"