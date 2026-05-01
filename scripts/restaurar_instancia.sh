#!/usr/bin/env bash

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
STAMP="$(date +%Y%m%d_%H%M%S)"

TARGET_WEB_ROOT="${TARGET_WEB_ROOT:-/var/www/html}"
TARGET_BAGO_ROOT="${TARGET_BAGO_ROOT:-/root/bago}"
TARGET_SIVNA_ROOT="${TARGET_SIVNA_ROOT:-/root/sivna}"
TARGET_KEYS_ROOT="${TARGET_KEYS_ROOT:-/root/keys}"
TARGET_ASTERISK_ROOT="${TARGET_ASTERISK_ROOT:-/etc/asterisk}"
TARGET_ETC_ROOT="${TARGET_ETC_ROOT:-/etc}"

MYSQL_BIN="${MYSQL_BIN:-mysql}"
GO_BIN="${GO_BIN:-go}"

RESTORE_DB_SCHEMA="${RESTORE_DB_SCHEMA:-1}"
BUILD_BAGO="${BUILD_BAGO:-1}"
PATCH_SOFTPHONE="${PATCH_SOFTPHONE:-0}"
ENABLE_CONTINGENCY="${ENABLE_CONTINGENCY:-0}"
RESTART_SERVICES="${RESTART_SERVICES:-0}"
PREFLIGHT_ONLY="${PREFLIGHT_ONLY:-0}"

say() {
    printf '%s\n' "$*"
}

step() {
    printf '\n>> %s\n' "$*"
}

fail() {
    say "ERROR: $*" >&2
    exit 1
}

require_root() {
    [[ "$(id -u)" -eq 0 ]] || fail "Este script debe ejecutarse como root"
}

require_cmd() {
    command -v "$1" >/dev/null 2>&1 || fail "No se encontro el comando requerido: $1"
}

require_cmd_if_requested() {
    local command_name="$1"
    local flag_value="$2"
    if [[ "$flag_value" == "1" ]]; then
        require_cmd "$command_name"
    fi
}

backup_file_if_exists() {
    local file_path="$1"
    if [[ -f "$file_path" ]]; then
        mkdir -p /root/restore_backups
        cp "$file_path" "/root/restore_backups/$(basename "$file_path").bak_${STAMP}"
    fi
}

sync_dir() {
    local source_dir="$1"
    local target_dir="$2"
    mkdir -p "$target_dir"
    rsync -a "$source_dir/" "$target_dir/"
}

copy_file_if_exists() {
    local source_file="$1"
    local target_file="$2"
    if [[ -f "$source_file" ]]; then
        mkdir -p "$(dirname "$target_file")"
        cp -a "$source_file" "$target_file"
    fi
}

check_prereqs() {
    step "Verificando prerequisitos"
    require_root
    require_cmd rsync
    require_cmd php
    require_cmd_if_requested "$MYSQL_BIN" "$RESTORE_DB_SCHEMA"
    require_cmd_if_requested "$GO_BIN" "$BUILD_BAGO"

    [[ -d "$REPO_ROOT/backend/bago" ]] || fail "No existe $REPO_ROOT/backend/bago"
    [[ -d "$REPO_ROOT/webroot/application" ]] || fail "No existe $REPO_ROOT/webroot/application"
    [[ -d "$REPO_ROOT/telephony/etc/asterisk" ]] || fail "No existe $REPO_ROOT/telephony/etc/asterisk"
    [[ -d "$REPO_ROOT/database/schema" ]] || fail "No existe $REPO_ROOT/database/schema"
}

report_path_state() {
    local path_label="$1"
    local path_value="$2"
    if [[ -e "$path_value" ]]; then
        say "OK  ${path_label}: ${path_value}"
    else
        say "WARN ${path_label}: ${path_value} no existe aun"
    fi
}

preflight_summary() {
    step "Resumen preflight"
    say "repo_root=$REPO_ROOT"
    say "target_web_root=$TARGET_WEB_ROOT"
    say "target_bago_root=$TARGET_BAGO_ROOT"
    say "target_sivna_root=$TARGET_SIVNA_ROOT"
    say "target_keys_root=$TARGET_KEYS_ROOT"
    say "target_asterisk_root=$TARGET_ASTERISK_ROOT"
    say "target_etc_root=$TARGET_ETC_ROOT"
    say "restore_db_schema=$RESTORE_DB_SCHEMA"
    say "build_bago=$BUILD_BAGO"
    say "patch_softphone=$PATCH_SOFTPHONE"
    say "enable_contingency=$ENABLE_CONTINGENCY"
    say "restart_services=$RESTART_SERVICES"
    say "preflight_only=$PREFLIGHT_ONLY"

    report_path_state "web root destino" "$TARGET_WEB_ROOT"
    report_path_state "bago destino" "$TARGET_BAGO_ROOT"
    report_path_state "sivna destino" "$TARGET_SIVNA_ROOT"
    report_path_state "keys destino" "$TARGET_KEYS_ROOT"
    report_path_state "asterisk destino" "$TARGET_ASTERISK_ROOT"
    report_path_state "etc destino" "$TARGET_ETC_ROOT"

    if command -v systemctl >/dev/null 2>&1; then
        say
        say "Estado de servicios relevantes:"
        systemctl is-enabled bago 2>/dev/null || true
        systemctl is-enabled apache2 2>/dev/null || true
        systemctl is-enabled asterisk 2>/dev/null || true
    fi

    say
    say "Bases a restaurar por esquema:"
    say "- assertive"
    say "- asterisk"
    say "- asteriskcdrdb"

    say
    say "Validaciones sugeridas antes de ejecutar restauracion real:"
    say "- Confirmar dominio final y certificados TLS"
    say "- Confirmar acceso MySQL con permisos de CREATE/IMPORT"
    say "- Confirmar servicio systemd de bago o su unidad destino"
    say "- Confirmar instalacion de FreePBX/Asterisk si se restaura telefonia"
}

backup_current_config() {
    step "Respaldando configuracion actual"
    backup_file_if_exists "$TARGET_WEB_ROOT/.env.php"
    backup_file_if_exists "$TARGET_WEB_ROOT/application/config/database.php"
    backup_file_if_exists "$TARGET_ETC_ROOT/freepbx.conf"
    backup_file_if_exists "$TARGET_ETC_ROOT/amportal.conf"
}

deploy_backend() {
    step "Desplegando backend"
    sync_dir "$REPO_ROOT/backend/bago" "$TARGET_BAGO_ROOT"
    if [[ -d "$REPO_ROOT/backend/sivna" ]]; then
        sync_dir "$REPO_ROOT/backend/sivna" "$TARGET_SIVNA_ROOT"
    fi
}

deploy_webroot() {
    step "Desplegando webroot"
    sync_dir "$REPO_ROOT/webroot/admin" "$TARGET_WEB_ROOT/admin"
    sync_dir "$REPO_ROOT/webroot/application" "$TARGET_WEB_ROOT/application"
    sync_dir "$REPO_ROOT/webroot/assets" "$TARGET_WEB_ROOT/assets"
    sync_dir "$REPO_ROOT/webroot/css" "$TARGET_WEB_ROOT/css"
    sync_dir "$REPO_ROOT/webroot/fonts" "$TARGET_WEB_ROOT/fonts"
    sync_dir "$REPO_ROOT/webroot/js" "$TARGET_WEB_ROOT/js"
    sync_dir "$REPO_ROOT/webroot/system" "$TARGET_WEB_ROOT/system"
    sync_dir "$REPO_ROOT/webroot/ucp" "$TARGET_WEB_ROOT/ucp"
    sync_dir "$REPO_ROOT/webroot/webfonts" "$TARGET_WEB_ROOT/webfonts"

    copy_file_if_exists "$REPO_ROOT/webroot/.env.php" "$TARGET_WEB_ROOT/.env.php"
    copy_file_if_exists "$REPO_ROOT/webroot/.env.php.dist" "$TARGET_WEB_ROOT/.env.php.dist"
    copy_file_if_exists "$REPO_ROOT/webroot/.env.phpmosirve" "$TARGET_WEB_ROOT/.env.phpmosirve"
    copy_file_if_exists "$REPO_ROOT/webroot/.htaccess" "$TARGET_WEB_ROOT/.htaccess"
    copy_file_if_exists "$REPO_ROOT/webroot/.gitignore" "$TARGET_WEB_ROOT/.gitignore"
    copy_file_if_exists "$REPO_ROOT/webroot/README.md" "$TARGET_WEB_ROOT/README.md"
    copy_file_if_exists "$REPO_ROOT/webroot/composer.json" "$TARGET_WEB_ROOT/composer.json"
    copy_file_if_exists "$REPO_ROOT/webroot/date.php" "$TARGET_WEB_ROOT/date.php"
    copy_file_if_exists "$REPO_ROOT/webroot/emer.sh" "$TARGET_WEB_ROOT/emer.sh"
    copy_file_if_exists "$REPO_ROOT/webroot/favicon.ico" "$TARGET_WEB_ROOT/favicon.ico"
    copy_file_if_exists "$REPO_ROOT/webroot/index.php" "$TARGET_WEB_ROOT/index.php"
    copy_file_if_exists "$REPO_ROOT/webroot/index.php.dist" "$TARGET_WEB_ROOT/index.php.dist"
}

deploy_keys() {
    step "Desplegando llaves y certificados"
    sync_dir "$REPO_ROOT/keys" "$TARGET_KEYS_ROOT"
    find "$TARGET_KEYS_ROOT" -type f -name '*.key' -exec chmod 600 {} \;
    find "$TARGET_KEYS_ROOT" -type f \( -name '*.crt' -o -name '*.pem' \) -exec chmod 644 {} \;
}

deploy_telephony() {
    step "Desplegando configuracion de telefonia"
    sync_dir "$REPO_ROOT/telephony/etc/asterisk" "$TARGET_ASTERISK_ROOT"
    copy_file_if_exists "$REPO_ROOT/telephony/etc/freepbx.conf" "$TARGET_ETC_ROOT/freepbx.conf"
    copy_file_if_exists "$REPO_ROOT/telephony/etc/amportal.conf" "$TARGET_ETC_ROOT/amportal.conf"
}

restore_schema() {
    step "Restaurando esquema SQL"
    "$MYSQL_BIN" -e "CREATE DATABASE IF NOT EXISTS assertive CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
    "$MYSQL_BIN" -e "CREATE DATABASE IF NOT EXISTS asterisk CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
    "$MYSQL_BIN" -e "CREATE DATABASE IF NOT EXISTS asteriskcdrdb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

    "$MYSQL_BIN" assertive < "$REPO_ROOT/database/schema/assertive.sql"
    "$MYSQL_BIN" asterisk < "$REPO_ROOT/database/schema/asterisk.sql"
    "$MYSQL_BIN" asteriskcdrdb < "$REPO_ROOT/database/schema/asteriskcdrdb.sql"
}

build_bago_binary() {
    step "Compilando bago"
    (cd "$TARGET_BAGO_ROOT" && "$GO_BIN" build -o "$TARGET_BAGO_ROOT/bago" .)
}

run_optional_steps() {
    if [[ "$PATCH_SOFTPHONE" == "1" ]]; then
        step "Aplicando correccion de softphone"
        WEB_ROOT="$TARGET_WEB_ROOT" bash "$REPO_ROOT/scripts/corregir_softphone_webrtc.sh"
    fi

    if [[ "$ENABLE_CONTINGENCY" == "1" ]]; then
        step "Activando contingencia local"
        WEB_ROOT="$TARGET_WEB_ROOT" PHP_ENV_FILE="$TARGET_WEB_ROOT/.env.php" BAGO_ENV_FILE="$TARGET_BAGO_ROOT/.env" bash "$REPO_ROOT/scripts/activar_contingencia_local.sh"
    fi

    if [[ "$RESTART_SERVICES" == "1" ]]; then
        step "Reiniciando servicios"
        systemctl restart bago || true
        systemctl reload apache2 || true
        systemctl restart asterisk || true
    fi
}

validate_restore() {
    step "Validando restauracion"
    php -l "$TARGET_WEB_ROOT/.env.php"
    php -l "$TARGET_WEB_ROOT/application/config/database.php"

    if command -v asterisk >/dev/null 2>&1; then
        asterisk -rx "http show status" || true
        asterisk -rx "pjsip show contacts" || true
    fi

    if [[ -x "$TARGET_BAGO_ROOT/bago" ]]; then
        say "Bago compilado en $TARGET_BAGO_ROOT/bago"
    fi

    say "Validacion final sugerida:"
    say "curl -sk https://localhost:8443/licencia"
    say "curl -vk https://DOMINIO_PUBLICO:8089/ws"
    say "systemctl status bago --no-pager -l"
}

main() {
    check_prereqs

    if [[ "$PREFLIGHT_ONLY" == "1" ]]; then
        preflight_summary
        return 0
    fi

    backup_current_config
    deploy_backend
    deploy_webroot
    deploy_keys
    deploy_telephony

    if [[ "$RESTORE_DB_SCHEMA" == "1" ]]; then
        restore_schema
    fi

    if [[ "$BUILD_BAGO" == "1" ]]; then
        build_bago_binary
    fi

    run_optional_steps
    validate_restore
}

main "$@"