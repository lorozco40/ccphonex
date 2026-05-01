#!/usr/bin/env bash

set -euo pipefail

WEB_ROOT="${WEB_ROOT:-/var/www/html}"
APP_JS="${APP_JS:-$WEB_ROOT/js/app.js}"
ENV_PHP="${ENV_PHP:-$WEB_ROOT/.env.php}"
STAMP="$(date +%Y%m%d_%H%M%S)"

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

backup_file() {
    local file_path="$1"
    cp "$file_path" "${file_path}.bak_${STAMP}"
}

require_file() {
    local file_path="$1"
    [[ -f "$file_path" ]] || fail "No existe ${file_path}"
}

read_putenv_value() {
    local key="$1"
    grep -E "putenv\('${key}=" "$ENV_PHP" 2>/dev/null | sed -E "s/.*${key}=([^']*).*/\1/" | tail -n 1
}

validate_env() {
    step "Validando variables de /var/www/html/.env.php"
    require_file "$ENV_PHP"

    local web_domain bago_burl bago_furl ari_burl ari_furl
    web_domain="$(read_putenv_value "WEB_DOMAIN")"
    bago_burl="$(read_putenv_value "BAGO_BURL")"
    bago_furl="$(read_putenv_value "BAGO_FURL")"
    ari_burl="$(read_putenv_value "ARI_BURL")"
    ari_furl="$(read_putenv_value "ARI_FURL")"

    say "WEB_DOMAIN=${web_domain}"
    say "BAGO_BURL=${bago_burl}"
    say "BAGO_FURL=${bago_furl}"
    say "ARI_BURL=${ari_burl}"
    say "ARI_FURL=${ari_furl}"

    [[ -n "$web_domain" ]] || fail "WEB_DOMAIN no esta definido"
    [[ -n "$ari_furl" ]] || fail "ARI_FURL no esta definido"
    [[ "$ari_furl" =~ ^https?://[^/]+:8089/?$ ]] || say "AVISO: ARI_FURL no tiene el formato esperado https://dominio:8089/"
}

already_patched() {
    grep -q "servaskUrl.replace(/^http/i, 'ws')+'/ws'" "$APP_JS"
}

apply_patch_with_perl() {
    command -v php >/dev/null 2>&1 || fail "php CLI es requerido para aplicar la correccion automaticamente"
    php <<'PHP' "$APP_JS"
<?php
$path = $argv[1];
$content = file_get_contents($path);
if ($content === false) {
    fwrite(STDERR, "No se pudo leer {$path}\n");
    exit(1);
}

$oldBlock = <<<'OLD'
var iniurl = "wss://";
var finurl = ":8089/ws";
if (location.protocol != 'https:') {
    iniurl = "ws://";
    finurl = ":8088/ws";
}

$(document).ready(function(){
    $("#txtRegStatus").html('<i class="fa fa-ban" style="color:red;"></i> '+'En espera de datos');
    if (typeof(agente) === 'undefined') {
        agente = JSON.parse(localStorage.getItem('SIPCreds'));
    }
    if (typeof(agente) != 'undefined' && agente.exten != null && agente.exten != "") {
        ctxSip = {
            config : {
                password        : agente.passask,
                displayName     : agente.name+" "+agente.last,
                uri             : agente.exten+'@'+agente.servask,
                wsServers       : iniurl+agente.servask+finurl,
OLD;

$newBlock = <<<'NEW'
$(document).ready(function(){
    $("#txtRegStatus").html('<i class="fa fa-ban" style="color:red;"></i> '+'En espera de datos');
    if (typeof(agente) === 'undefined') {
        agente = JSON.parse(localStorage.getItem('SIPCreds'));
    }
    if (typeof(agente) != 'undefined' && agente.exten != null && agente.exten != "") {
        var servaskUrl = (agente.servask || '').replace(/\/+$/, '');
        if (!/^https?:\/\//i.test(servaskUrl)) {
            servaskUrl = ((location.protocol == 'https:') ? 'https://' : 'http://') + servaskUrl;
        }
        var servaskHost = servaskUrl.replace(/^https?:\/\//i, '');

        ctxSip = {
            config : {
                password        : agente.passask,
                displayName     : agente.name+" "+agente.last,
                uri             : agente.exten+'@'+servaskHost,
                wsServers       : servaskUrl.replace(/^http/i, 'ws')+'/ws',
NEW;

$count = 0;
$content = str_replace($oldBlock, $newBlock, $content, $count);
if ($count !== 1) {
    fwrite(STDERR, "No se encontro el bloque esperado para correccion automatica\n");
    exit(1);
}

if (file_put_contents($path, $content) === false) {
    fwrite(STDERR, "No se pudo escribir {$path}\n");
    exit(1);
}
PHP
}

patch_app_js() {
    step "Aplicando correccion de softphone en app.js"
    require_file "$APP_JS"

    if already_patched; then
        say "El archivo ya contiene la correccion. No se realizan cambios."
        return 0
    fi

    grep -q "uri             : agente.exten+'@'+agente.servask" "$APP_JS" || fail "No se encontro el patron esperado de uri"
    grep -q "wsServers       : iniurl+agente.servask+finurl" "$APP_JS" || fail "No se encontro el patron esperado de wsServers"

    backup_file "$APP_JS"
    apply_patch_with_perl

    already_patched || fail "La correccion no quedo aplicada"
    say "Correccion aplicada en ${APP_JS}"
}

validate_result() {
    step "Validando resultado"
    grep -n "servaskUrl\|servaskHost\|wsServers\|uri             :" "$APP_JS"
    if command -v php >/dev/null 2>&1; then
        php -l "$ENV_PHP" >/dev/null && say "Sintaxis PHP OK en ${ENV_PHP}"
    fi
}

show_next_checks() {
    step "Chequeos directos recomendados"
    say "asterisk -rx \"http show status\""
    say "ss -ltnp | grep -E ':(8088|8089|5060|5160)\\b'"
    say "curl -vk https://$(read_putenv_value "WEB_DOMAIN"):8089/ws"
    say "asterisk -rx \"pjsip show contacts\""
}

main() {
    validate_env
    patch_app_js
    validate_result
    show_next_checks
}

main "$@"