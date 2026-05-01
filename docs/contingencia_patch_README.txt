Paquete de contingencia para servidor Assertive/Bago

Resumen ejecutivo

Este paquete deja operativo el acceso al sistema cuando el licenciamiento central no permite el arranque normal de bago y agrega la correccion del softphone web cuando el navegador muestra Error de internet por una construccion incorrecta de la URL WebSocket derivada de ARI_FURL.

Que se tiene que hacer

1. Desplegar el parche de contingencia de bago y del frontend PHP.
2. Compilar bago nuevamente.
3. Ejecutar la activacion local de contingencia.
4. Validar que el acceso web quede funcional.
5. Si el softphone web presenta Error de internet, aplicar la correccion del cliente SIP/WebRTC.
6. Validar Asterisk en 8089, /ws y el registro SIP real de la extension afectada.

Contenido del parche de contingencia

- /root/bago/ct/main.go
- /root/bago/bago.go
- /root/bago/mo/generador.go
- /var/www/html/application/helpers/precarga_helper.php
- /var/www/html/application/models/Usuario_model.php
- /var/www/html/application/models/Datos_model.php
- /var/www/html/application/controllers/Api.php
- /var/www/html/application/controllers/Api2.php
- /root/activar_contingencia_local.sh

Correccion adicional de softphone WebRTC

- /var/www/html/js/app.js
- /root/corregir_softphone_webrtc.sh

Destino en el otro servidor

- Copiar root/bago/ct/main.go a /root/bago/ct/main.go
- Copiar root/bago/bago.go a /root/bago/bago.go
- Copiar root/bago/mo/generador.go a /root/bago/mo/generador.go
- Copiar var/www/html/application/helpers/precarga_helper.php a /var/www/html/application/helpers/precarga_helper.php
- Copiar var/www/html/application/models/Usuario_model.php a /var/www/html/application/models/Usuario_model.php
- Copiar var/www/html/application/models/Datos_model.php a /var/www/html/application/models/Datos_model.php
- Copiar var/www/html/application/controllers/Api.php a /var/www/html/application/controllers/Api.php
- Copiar var/www/html/application/controllers/Api2.php a /var/www/html/application/controllers/Api2.php
- Copiar var/www/html/js/app.js a /var/www/html/js/app.js o ejecutar /root/corregir_softphone_webrtc.sh
- Copiar root/activar_contingencia_local.sh a /root/activar_contingencia_local.sh
- Copiar root/corregir_softphone_webrtc.sh a /root/corregir_softphone_webrtc.sh

Variables esperadas en /var/www/html/.env.php para este escenario

- WEB_DOMAIN=ccphonex.assertivebusiness.com.mx
- BAGO_BURL=https://localhost:8443/
- BAGO_FURL=https://ccphonex.assertivebusiness.com.mx:8443/
- ARI_BURL=https://localhost:8089/
- ARI_FURL=https://ccphonex.assertivebusiness.com.mx:8089/

Pasos en el servidor destino

1. Descomprimir el paquete desde /.
2. Compilar bago:
   cd /root/bago && go build -o /root/bago/bago .
3. Dar permisos a los scripts:
   chmod +x /root/activar_contingencia_local.sh /root/corregir_softphone_webrtc.sh
4. Activar contingencia:
   /root/activar_contingencia_local.sh
5. Validar contingencia:
   curl -sk https://localhost:8443/licencia
   systemctl status bago --no-pager -l
6. Validar Asterisk WebSocket:
   asterisk -rx "http show status"
   ss -ltnp | grep -E ":(8088|8089|5060|5160)\\b"
   curl -vk https://DOMINIO_PUBLICO:8089/ws
7. Si el navegador muestra Error de internet, aplicar correccion del softphone:
   /root/corregir_softphone_webrtc.sh
8. Hacer recarga forzada del navegador y probar registro de la extension.
9. Si aun falla, revisar autenticacion SIP:
   asterisk -rx "pjsip show endpoint EXT"
   asterisk -rx "pjsip show auth EXT-auth"
   asterisk -rx "pjsip show contacts"
   tail -n 300 /var/log/asterisk/full | grep -iE "failed to authenticate|register|websocket|tls|transport|EXT"

Que corrige la correccion del softphone

Antes, el cliente armaba la conexion tomando agente.servask desde ARI_FURL y concatenando esquema y puerto de nuevo. Si ARI_FURL ya venia como https://dominio:8089/, el navegador terminaba intentando una URL WebSocket mal formada.

La correccion normaliza servask y construye:

- uri con host limpio, sin http ni https
- wsServers como wss://dominio:8089/ws o ws://dominio:8088/ws segun corresponda

Checklist rapido de validacion final

- Acceso web funcional.
- curl -sk https://localhost:8443/licencia responde.
- asterisk -rx "http show status" muestra /ws habilitado.
- curl -vk https://DOMINIO_PUBLICO:8089/ws responde 426 Upgrade Required.
- El navegador ya no cae inmediatamente en disconnected.
- La extension afectada aparece con contacto o al menos ya genera intento de registro coherente en Asterisk.

Notas operativas

- La correccion del softphone no sustituye un problema de credenciales PJSIP.
- Si openssl s_client contra 8089 devuelve error de cadena, revisar fullchain del certificado, pero eso es una capa aparte del bug de app.js.
- Si node y npm no estan en PATH, el problema pm2/UCP puede existir como incidencia secundaria y no necesariamente bloquea /ws de Asterisk.
