# Plan Priorizado de Actualizacion y Correccion

## Objetivo

Definir una ruta de actualizacion gradual para la base actual, priorizando estabilidad productiva, seguridad y mantenibilidad antes de cambios grandes de arquitectura.

## Hallazgos principales

### 1. Riesgo alto: credenciales y secretos en archivos activos

Evidencia:

- `webroot/.env.php` contiene usuarios, contraseñas y endpoints activos.
- `webroot/application/config/database.php` contiene credenciales de MySQL activas.
- `keys/` y `telephony/etc/asterisk/keys/` contienen material criptografico operativo.

Impacto:

- Dificulta rotacion de secretos.
- Aumenta riesgo operativo en despliegues y respaldos.
- Mezcla codigo con configuracion sensible.

Correccion propuesta:

1. Separar secretos de codigo en una fase controlada.
2. Definir plantilla de variables por entorno.
3. Mantener compatibilidad con el layout actual mientras se migra.

### 2. Riesgo alto: stack web legado y dependencias muy antiguas

Evidencia:

- `webroot/composer.json` exige `php >=5.3.7`.
- El proyecto base sigue estructura CodeIgniter legacy.
- Se usan librerias frontend antiguas como jQuery, Bootstrap y CKEditor en el webroot.

Impacto:

- Limita upgrades de sistema operativo y PHP.
- Aumenta riesgo de incompatibilidades y deuda de seguridad.
- Dificulta pruebas y refactor.

Correccion propuesta:

1. Congelar una baseline estable.
2. Levantar staging fiel al productivo.
3. Planear upgrade por capas, no reescritura total.

### 3. Riesgo alto: interpolacion SQL y construccion dinamica de queries

Evidencia:

- Hay consultas construidas con strings y variables en vistas y modelos, por ejemplo en `webroot/application/views/despachador/cats.php`.
- Existen modelos `.save` dentro del repo con mucho SQL dinamico que tambien representan deuda tecnica.

Impacto:

- Mayor superficie de errores y posibles vulnerabilidades.
- Dificulta mantenimiento, pruebas y revisiones.

Correccion propuesta:

1. Inventariar consultas con interpolacion.
2. Priorizar modulos expuestos a entrada de usuario.
3. Sustituir gradualmente por consultas parametrizadas y helpers de acceso.

### 4. Riesgo medio-alto: credenciales SIP y estados del softphone en localStorage

Evidencia:

- `webroot/js/app.js` usa `localStorage.getItem('SIPCreds')`.
- El softphone persiste `sipCalls` y otros estados en almacenamiento del navegador.
- La configuracion de SIP usa `agente.passask` directamente en frontend.

Impacto:

- Exposicion local de credenciales del agente.
- Riesgo operativo en equipos compartidos o sesiones huérfanas.

Correccion propuesta:

1. Revisar modelo de entrega de credenciales SIP al frontend.
2. Reducir persistencia sensible en `localStorage`.
3. Implementar expiracion o limpieza obligatoria al cerrar sesion.

### 5. Riesgo medio: mezcla de codigo activo con artefactos historicos

Evidencia:

- Existen archivos tipo `.save`, `.bak`, `.dist` y variantes antiguas en varios arboles.

Impacto:

- Complica revisiones.
- Confunde el origen real del comportamiento.
- Amplia el volumen de codigo no confiable.

Correccion propuesta:

1. Marcar que archivos son vigentes y cuales son historicos.
2. Mover historicos a una carpeta de archivo o excluirlos de despliegue.

## Orden recomendado de trabajo

### Fase 0. Control y observabilidad

Duracion sugerida: 1 a 2 semanas.

Objetivo:

Crear base segura para cambiar sin romper producción.

Tareas:

1. Mantener el repo actual como baseline congelada.
2. Definir ambiente staging equivalente.
3. Crear checklist de deploy, rollback y smoke test.
4. Activar validaciones minimas para web, bago, Asterisk y WebSocket.
5. Documentar modulos criticos y owners.

### Fase 1. Seguridad y configuracion

Duracion sugerida: 2 a 4 semanas.

Objetivo:

Separar configuracion sensible y reducir riesgo inmediato.

Tareas:

1. Externalizar credenciales de app y DB.
2. Revisar llaves y certificados realmente necesarios en runtime.
3. Definir rotacion de secretos y rutas estables.
4. Eliminar persistencia sensible del frontend donde no sea necesaria.

Primeros entregables:

1. Plantilla de entorno por servidor.
2. Procedimiento de rotacion de secretos.
3. Lista de llaves activas por servicio.

### Fase 2. Hardening del acceso a datos

Duracion sugerida: 3 a 6 semanas.

Objetivo:

Reducir deuda técnica en consultas y acceso a BD.

Tareas:

1. Escanear modelos y controladores con SQL interpolado.
2. Priorizar modulos con entrada de usuario o mayor tráfico.
3. Reemplazar consultas criticas por parametrizadas.
4. Crear helpers o repositorios pequeños en los puntos mas riesgosos.

Prioridad inicial sugerida:

1. Softphone y autenticacion.
2. CRM y formularios dinamicos.
3. WhatsApp y canales externos.
4. Despachador y reportes de alto uso.

### Fase 3. Estabilizacion del frontend legacy

Duracion sugerida: 3 a 6 semanas.

Objetivo:

Reducir fallos operativos visibles sin reescribir toda la UI.

Tareas:

1. Segmentar `webroot/js/app.js` en modulos claros.
2. Revisar persistencia en `localStorage`.
3. Corregir dependencias directas de estado global.
4. Documentar los contratos del frontend con backend y Asterisk.

Quick wins:

1. Limpieza de credenciales persistidas.
2. Validaciones más claras de conexión WebSocket.
3. Manejo más explícito de errores de registro SIP.

### Fase 4. Modernizacion de plataforma

Duracion sugerida: 6 a 12 semanas.

Objetivo:

Preparar el salto de plataforma con bajo riesgo.

Tareas:

1. Definir versión objetivo de PHP.
2. Definir estrategia para CodeIgniter: endurecer, encapsular o migrar por módulos.
3. Revisar dependencia de FreePBX/UCP/pm2 y su acoplamiento real.
4. Normalizar build y empaquetado de bago.

### Fase 5. Renovacion UX y mantenibilidad

Duracion sugerida: 4 a 10 semanas.

Objetivo:

Actualizar experiencia de usuario sin sacrificar productividad del operador.

Tareas:

1. Rediseñar primero login, consola y softphone.
2. Definir un design system minimo.
3. Reemplazar pantallas criticas por componentes progresivos.
4. Medir impacto operativo por rol.

## Backlog de correcciones inmediatas

### Sprint A

1. Inventario de secretos y llaves en runtime.
2. Inventario de queries interpoladas.
3. Inventario de archivos `.save`, `.bak`, `.dist` y su rol.
4. Pruebas smoke para login, softphone y contingencia.

### Sprint B

1. Reducir uso de `localStorage` para SIP.
2. Parametrizar primer paquete de consultas críticas.
3. Consolidar configuración por entorno.
4. Normalizar logs mínimos y healthchecks.

### Sprint C

1. Segmentar `app.js`.
2. Limpiar dependencias frontend obsoletas más expuestas.
3. Preparar staging para pruebas de upgrade de PHP.

## Criterio de ejecución

1. Cada bloque debe cerrar con validación ejecutable.
2. No mezclar refactor, rediseño visual y cambios de infraestructura en una sola entrega.
3. Todo cambio de telefonía o softphone debe pasar primero por staging.
4. La meta no es modernizar rápido; es modernizar sin perder operación.