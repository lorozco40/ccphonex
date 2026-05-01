#!/usr/bin/env bash

set -euo pipefail

WEB_ROOT="${WEB_ROOT:-/var/www/html}"
PHP_ENV_FILE="${PHP_ENV_FILE:-$WEB_ROOT/.env.php}"
BAGO_ENV_FILE="${BAGO_ENV_FILE:-/root/bago/.env}"
WEB_DOMAIN="${WEB_DOMAIN:-}"
BAGO_LOCAL_URL="${BAGO_LOCAL_URL:-https://localhost:8443/}"
BAGO_PUBLIC_URL="${BAGO_PUBLIC_URL:-}"
LIC_USERS="${LIC_USERS:-9999}"
LIC_DAYS="${LIC_DAYS:-365}"
LIC_FILE="${LIC_FILE:-/root/bago/licencia.json}"
STAMP="$(date +%Y%m%d_%H%M%S)"

say() {
    printf '%s\n' "$*"
}

step() {
    printf '\n>> %s\n' "$*"
}

backup_file() {
    local file_path="$1"
    if [[ -f "$file_path" ]]; then
        cp "$file_path" "${file_path}.bak_${STAMP}"
    fi
}

replace_or_append_env() {
    local file_path="$1"
    local key="$2"
    local value="$3"
    if grep -q "^${key}=" "$file_path" 2>/dev/null; then
        sed -i "s|^${key}=.*|${key}=${value}|" "$file_path"
    else
        printf '%s=%s\n' "$key" "$value" >> "$file_path"
    fi
}

replace_or_append_putenv() {
    local file_path="$1"
    local key="$2"
    local value="$3"
    local escaped_value
    escaped_value="$(printf '%s' "$value" | sed 's/[&/]/\\&/g')"
    if grep -q "putenv('${key}=" "$file_path" 2>/dev/null; then
        sed -i "s|putenv('${key}=[^']*')|putenv('${key}=${escaped_value}')|" "$file_path"
    else
        perl -0pi -e "s#\?>#putenv('${key}=${escaped_value}');\\n\\n?>#s" "$file_path"
    fi
}

detect_domain() {
    if [[ -n "$WEB_DOMAIN" ]]; then
        return
    fi
    if [[ -f "$PHP_ENV_FILE" ]]; then
        WEB_DOMAIN="$(grep -E "WEB_DOMAIN=" "$PHP_ENV_FILE" 2>/dev/null | sed -E "s/.*WEB_DOMAIN=([^']+).*/\1/" | tail -n 1)"
    fi
    if [[ -z "$WEB_DOMAIN" ]]; then
        WEB_DOMAIN="$(hostname -f 2>/dev/null || hostname)"
    fi
}

prepare_urls() {
    detect_domain
    if [[ -z "$BAGO_PUBLIC_URL" ]]; then
        BAGO_PUBLIC_URL="https://${WEB_DOMAIN}:8443/"
    fi
}

check_prereqs() {
    step "Verificando prerequisitos"
    if [[ ! -f "$PHP_ENV_FILE" ]]; then
        say "No existe $PHP_ENV_FILE"
        exit 1
    fi
    if [[ ! -f /root/bago/ct/main.go ]]; then
        say "No existe /root/bago/ct/main.go"
        exit 1
    fi
    if ! grep -q 'LICMODE\|contingenciaActiva\|loadLicenciaLocal' /root/bago/ct/main.go 2>/dev/null; then
        say "Este bago no tiene desplegado el codigo de contingencia."
        say "Necesitas copiar primero los cambios de codigo de bago antes de usar este script."
        exit 2
    fi
}

write_license_file() {
    step "Escribiendo licencia local de respaldo"
    mkdir -p "$(dirname "$LIC_FILE")"
    cat > "$LIC_FILE" <<EOF
{
  "id": 1,
  "id_campaign": 0,
  "expira": "$(date -d "+${LIC_DAYS} days" '+%Y-%m-%d 23:59:59')",
  "tipo": "CT",
  "usuarios": ${LIC_USERS},
  "cadena": "contingencia-local",
  "cliente": "contingencia",
  "dominio_publico": "${WEB_DOMAIN}",
  "ip_privada": "",
  "error": ""
}
EOF
}

configure_php() {
    step "Configurando PHP"
    if [[ ! -f "$PHP_ENV_FILE" ]]; then
        say "No existe $PHP_ENV_FILE"
        return 1
    fi
    backup_file "$PHP_ENV_FILE"
    replace_or_append_putenv "$PHP_ENV_FILE" "BAGO_URL" "$BAGO_LOCAL_URL"
    replace_or_append_putenv "$PHP_ENV_FILE" "BAGO_JS_URL" "${WEB_DOMAIN}:8443"
    replace_or_append_putenv "$PHP_ENV_FILE" "BAGO_BURL" "$BAGO_LOCAL_URL"
    replace_or_append_putenv "$PHP_ENV_FILE" "BAGO_FURL" "$BAGO_PUBLIC_URL"
}

configure_bago_env() {
    step "Configurando entorno de bago"
    mkdir -p "$(dirname "$BAGO_ENV_FILE")"
    [[ -f "$BAGO_ENV_FILE" ]] || touch "$BAGO_ENV_FILE"
    backup_file "$BAGO_ENV_FILE"
    replace_or_append_env "$BAGO_ENV_FILE" "LICMODE" "contingencia"
    replace_or_append_env "$BAGO_ENV_FILE" "LICUSERS" "$LIC_USERS"
    replace_or_append_env "$BAGO_ENV_FILE" "LICDAYS" "$LIC_DAYS"
    replace_or_append_env "$BAGO_ENV_FILE" "LICFILE" "$LIC_FILE"
}

validate_services() {
    step "Validando bago local"
    curl -sk "${BAGO_LOCAL_URL%/}/licencia" || true
    say
    say "Estado bago:"
    systemctl status bago --no-pager -l | sed -n '1,20p' || true
}

show_failure_context() {
    say
    say "Fallo detectado. Ultimos logs de bago:"
    journalctl -u bago -n 30 --no-pager -l || true
}

main() {
    trap show_failure_context ERR
    prepare_urls
    check_prereqs
    step "Activando contingencia local"
    say "web_root=$WEB_ROOT"
    say "php_env_file=$PHP_ENV_FILE"
    say "bago_env_file=$BAGO_ENV_FILE"
    say "web_domain=$WEB_DOMAIN"
    say "bago_local_url=$BAGO_LOCAL_URL"
    say "bago_public_url=$BAGO_PUBLIC_URL"
    say "lic_users=$LIC_USERS"
    say "lic_days=$LIC_DAYS"
    say "lic_file=$LIC_FILE"

    configure_php
    configure_bago_env
    write_license_file

    step "Reiniciando servicios"
    systemctl restart bago
    systemctl reload apache2

    validate_services
}

main "$@"