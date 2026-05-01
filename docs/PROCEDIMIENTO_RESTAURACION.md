# Procedimiento de restauracion para otra instancia

## Objetivo

Habilitar otra instancia del sistema a partir de este repositorio, restaurando:

- Codigo backend y web.
- Configuracion de aplicacion.
- Configuracion Asterisk y FreePBX.
- Llaves y certificados.
- Esquema SQL sin datos.
- Scripts de contingencia.

No restaura:

- Audios.
- Datos de negocio.
- Dumps con registros.
- Archivos operativos como `files`, `emailfiles`, `wafiles` o repositorios de contenido.

## Requisitos del servidor destino

Minimos:

1. Linux con acceso root.
2. Git.
3. rsync.
4. mysql y mysqldump o cliente compatible.
5. php CLI.
6. go para recompilar bago.
7. Asterisk y FreePBX instalados cuando se vaya a restaurar telefonia.
8. Apache o equivalente para servir el webroot en `/var/www/html`.

Recomendados:

1. Certificados DNS/TLS ya resueltos para el dominio final.
2. Servicio `bago` creado en systemd.
3. Servicio `apache2` y `asterisk` gestionados por systemd.
4. Acceso a la base MySQL local o remota con privilegios para crear bases e importar esquema.

## Variables importantes a revisar despues de restaurar

1. `webroot/.env.php`
2. `webroot/application/config/database.php`
3. `telephony/etc/amportal.conf`
4. `telephony/etc/freepbx.conf`
5. `telephony/etc/asterisk/http_custom.conf`, `http_additional.conf` y `keys/`

## Uso rapido

Desde el root del repositorio clonado en el servidor destino:

```bash
chmod +x scripts/restaurar_instancia.sh
./scripts/restaurar_instancia.sh
```

## Preflight no destructivo

Para validar requisitos, rutas y dependencias sin tocar archivos, bases o servicios:

```bash
PREFLIGHT_ONLY=1 ./scripts/restaurar_instancia.sh
```

Este modo:

1. Verifica prerequisitos.
2. Revisa rutas esperadas.
3. Resume variables efectivas.
4. Informa bases y servicios implicados.
5. No copia archivos.
6. No importa esquema.
7. No compila bago.
8. No reinicia servicios.

## Ejemplos utiles

Solo desplegar archivos y esquema, sin reinicios:

```bash
./scripts/restaurar_instancia.sh
```

Desplegar, compilar bago y reiniciar servicios:

```bash
RESTART_SERVICES=1 ./scripts/restaurar_instancia.sh
```

Desplegar y activar contingencia local al final:

```bash
ENABLE_CONTINGENCY=1 ./scripts/restaurar_instancia.sh
```

Desplegar y aplicar correccion del softphone WebRTC:

```bash
PATCH_SOFTPHONE=1 ./scripts/restaurar_instancia.sh
```

Validar primero y ejecutar despues:

```bash
PREFLIGHT_ONLY=1 ./scripts/restaurar_instancia.sh
RESTART_SERVICES=1 PATCH_SOFTPHONE=1 ./scripts/restaurar_instancia.sh
```

Cambiar rutas por un layout distinto:

```bash
TARGET_WEB_ROOT=/srv/www/ccphonex \
TARGET_BAGO_ROOT=/opt/bago \
TARGET_SIVNA_ROOT=/opt/sivna \
./scripts/restaurar_instancia.sh
```

## Flujo recomendado

1. Instalar paquetes base del servidor.
2. Clonar este repositorio privado.
3. Ejecutar `scripts/restaurar_instancia.sh`.
4. Ajustar dominio, IP y credenciales si el nuevo entorno cambia.
5. Si aplica, crear o revisar el servicio systemd de bago.
6. Validar web, bago, Asterisk y WebSocket.
7. Si el servidor entra en contingencia, usar `scripts/activar_contingencia_local.sh`.
8. Si el softphone falla con `Error de internet`, usar `scripts/corregir_softphone_webrtc.sh`.

## Validaciones minimas posteriores

```bash
php -l /var/www/html/.env.php
curl -sk https://localhost:8443/licencia
asterisk -rx "http show status"
curl -vk https://DOMINIO_PUBLICO:8089/ws
asterisk -rx "pjsip show contacts"
systemctl status bago --no-pager -l
```

## Riesgos controlados por el script

1. No borra datos de negocio.
2. No restaura audios.
3. No reinicia servicios salvo que se indique.
4. No crea contenido de base con datos, solo esquema.
5. Respeta rutas configurables por variables de entorno.
6. Puede ejecutarse primero en modo preflight sin tocar nada.

## Punto de atencion

Este repositorio contiene configuracion sensible y llaves. Debe permanecer privado y con acceso restringido.