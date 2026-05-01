# Respaldo de reinstancia

Este respaldo fue preparado para recrear otra instancia del sistema sin arrastrar datos operativos.

## Alcance

- Codigo y configuracion del backend Go.
- Codigo y configuracion del webroot PHP/JS.
- Configuracion Asterisk y FreePBX.
- Llaves y certificados necesarios.
- Scripts SQL de esquema sin datos.
- Scripts de contingencia.

## Exclusiones deliberadas

- Audios del sistema.
- Datos de usuarios, tickets, conversaciones o archivos operativos.
- Volcados de base con registros.
- Logs y artefactos temporales.

## Validacion minima tras reinstancia

- `curl -sk https://localhost:8443/licencia`
- `asterisk -rx "http show status"`
- `curl -vk https://DOMINIO:8089/ws`
- `asterisk -rx "pjsip show contacts"`

## Notas

Si el nuevo servidor cambia dominio, IP o certificados, actualizar:

- `webroot/.env.php`
- configuracion en `telephony/etc/asterisk/`
- llaves o certificados en `keys/` y `telephony/etc/asterisk/keys/`
