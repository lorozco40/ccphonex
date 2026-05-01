# ccphonex

Respaldo de reinstancia del servidor productivo.

Este repositorio contiene solo lo necesario para levantar otra instancia o servidor:

- Codigo de aplicacion y backend.
- Configuraciones activas.
- Llaves y certificados requeridos.
- Configuracion de Asterisk y FreePBX.
- Scripts de esquema SQL, sin datos.
- Scripts y documentacion de contingencia.

## Estructura

- `backend/`: codigo backend y componentes auxiliares.
- `webroot/`: aplicacion web y frontend desplegados.
- `telephony/etc/`: configuracion de Asterisk y FreePBX.
- `database/schema/`: esquemas SQL sin datos.
- `keys/`: llaves y certificados necesarios para reinstancia.
- `scripts/`: scripts operativos de contingencia y correccion.
- `docs/`: documentacion operativa.

## Incluye

- Version de contingencia de acceso.
- Correccion del softphone WebRTC.
- Configuracion activa de entorno.
- Esquema de bases `assertive`, `asterisk` y `asteriskcdrdb`.

## No incluye

- Audios operativos.
- Datos de negocio o respaldos de contenido.
- Dumps con registros de base de datos.
- Logs voluminosos o datos temporales.

## Uso esperado

1. Restaurar archivos en rutas equivalentes.
2. Restaurar llaves y configuracion de telefonia.
3. Crear las bases desde `database/schema/`.
4. Completar cualquier secreto o ajuste de red necesario para el nuevo entorno.
5. Validar bago, web, Asterisk, FreePBX y softphone.
