# Revision de performance en reportes

Fecha: 2026-05-05

## Hallazgos confirmados

### 1. Dashboard Assertive

- El cliente hacia polling cada 3 segundos.
- Se permitian solicitudes solapadas si la anterior no habia terminado.
- En cada vuelta se recreaban tabla de colas y 4 graficas aunque no hubiera cambios.

Correccion aplicada:

- Polling reducido a 10 segundos.
- Bloqueo de peticiones concurrentes.
- Pausa cuando la pestana no esta visible.
- Reutilizacion de instancias de Google Charts.
- Redibujo solo cuando cambia el payload.

Archivos:

- js/reportes/dashboard.js

### 2. Historico de llamadas

- La vista hacia una consulta inicial al cargar.
- Despues volvia a consultar al resolver horarios de campana.
- Las graficas se reconstruian por completo en cada respuesta.
- El backend filtraba con DATE/TIME sobre datetime_received, lo que castiga indices.
- El backend hacia una consulta extra solo para totales inbound.

Correccion aplicada:

- Se elimino la doble carga inicial.
- Se bloquearon solicitudes solapadas.
- Se reutilizaron instancias de Google Charts.
- Se cambiaron filtros a rangos directos sobre datetime_received.
- Los totales inbound se derivan de los grupos ya obtenidos.

Archivos:

- js/reportes/historico.js
- application/models/Reportes_model.php

### 3. Reportes -> Llamadas -> Inbound

Hallazgos de vista:

- Usa la vista generica reportes/reporte.php y el cliente generico js/reportes/reporte.js.
- La vista no tenia el problema de doble carga de Historico, pero si arrastra el patron generico de reconstruir por completo la tabla HTML en cada respuesta.

Hallazgos de performance:

- El controlador llamaba a repoback_model->inbound() al entrar a la vista, materializando rep_inbound antes de renderizar la pantalla.
- La materializacion de rep_inbound recorria todo el dia actual usando date(c.datetime_received) = fecha.
- El modelo inbound filtraba con date(ri.fecha) entre min y max.
- La paginacion real no ocurria en SQL: Datos_model::manejadorqueries ejecuta la consulta completa, trae todos los registros a PHP y luego hace array_slice.

Correcciones aplicadas:

- Inbound ya pagina en SQL real con LIMIT/OFFSET.
- Inbound ya usa rango indexable sobre ri.fecha en lugar de date(ri.fecha).
- La materializacion diaria de rep_inbound ahora es incremental por id maximo del dia y usa rango datetime indexable.

Archivos:

- application/models/Reportes_model.php
- application/models/Repoback_model.php

### 4. Reportes -> Llamadas -> Outbound y Abandono

Hallazgos de performance:

- Outbound seguia filtrando con date(r.fecha) y traia el resultado completo antes de paginar.
- Abandono y Abandono total seguian resolviendo desde consulta completa y paginacion en PHP.
- Las materializaciones diarias de rep_outbound y rep_abandono seguian recorriendo todo el dia por cada acceso a la vista.

Correcciones aplicadas:

- Outbound ya pagina en SQL real con LIMIT/OFFSET.
- Outbound ya usa rango indexable sobre r.fecha.
- Abandono y Abandono total ya usan rep_abandono con rango indexable y paginacion SQL real.
- rep_outbound y rep_abandono ya se materializan de forma incremental por max(id) del dia.

Archivos:

- application/models/Reportes_model.php
- application/models/Repoback_model.php

### 5. Reportes -> Llamadas -> Tiempo de espera, Exitosas e IVR

Hallazgos de performance:

- Los tres seguian usando DATE(datetime_received) en filtros de rango.
- Los tres seguian dependiendo del flujo generico que ejecuta la consulta completa y pagina en PHP.
- Tiempo de espera y Exitosas agrupan por DID, por lo que traer todo el resultado para cortar solo 20 filas no era eficiente.

Correcciones aplicadas:

- Los tres ya usan rango datetime indexable.
- Los tres ya usan paginacion SQL real con COUNT separado y LIMIT/OFFSET.
- Se conserva el mismo contrato de columnas y la misma salida para la vista generica.

Archivos:

- application/models/Reportes_model.php

### 6. Reportes -> Comparativo cada media hora, Llamadas por Agente y Log de usuarios

Hallazgos de performance:

- Comparativo cada media hora seguia filtrando con DATE(datetime_received) sobre tablas materializadas.
- Llamadas por Agente seguia resolviendo la consulta completa y recortando en PHP.
- Log de usuarios seguia filtrando con DATE(ul.evento) y paginando desde memoria.

Correcciones aplicadas:

- Comparativo cada media hora ya usa rangos datetime indexables.
- Llamadas por Agente ya pagina en SQL real con COUNT separado y LIMIT/OFFSET.
- Log de usuarios ya usa rango indexable sobre ul.evento y paginacion SQL real.

Archivos:

- application/models/Reportes_model.php

### 7. Autoajuste del grid generico

Hallazgos de vista:

- El grid generico usaba un ancho minimo fijo alto y dejaba todas las celdas en no-wrap salvo excepciones manuales.
- Eso obligaba a depender demasiado del scroll horizontal incluso en pantallas amplias, especialmente en Inbound y Tickets detalle.

Correcciones aplicadas:

- El grid ahora calcula mejor su ancho minimo en funcion del numero de columnas visibles.
- Se habilitaron columnas envolvibles para reportes con textos largos como Inbound, Outbound, Llamadas por Agente, Log de usuarios y Tickets detalle.
- Tickets detalle ahora usa contenedor amplio para aprovechar mejor el ancho disponible.

Archivos:

- webroot/css/main.css
- webroot/js/reportes/reporte.js
- webroot/application/controllers/Reportes.php
- webroot/application/controllers/Crm.php

### 8. Reportes -> Descansos y After Call Work

Hallazgos de performance:

- Ambos seguian usando el helper generico que trae todo el resultado y pagina en PHP.
- Descansos ademas seguia filtrando con date(datetime_init) sobre break_entry.
- Descansos tiende a ensanchar mucho la tabla por las columnas dinamicas de tipos de break.

Correcciones aplicadas:

- Descansos ya usa rango datetime indexable sobre datetime_init.
- Descansos y ACW ya usan paginacion SQL real con COUNT separado y LIMIT/OFFSET.
- Ambos quedaron marcados como reportes anchos/densos para aprovechar mejor el viewport disponible.

Archivos:

- application/models/Reportes_model.php
- webroot/application/controllers/Reportes.php

### 9. Reporte -> Tiempo de sesion

Hallazgos de performance:

- La vista disparaba repoback_model->sesion() en cada entrada al reporte.
- El listado seguia usando el helper generico que carga todo y pagina en PHP.
- En desktop el grid seguia desperdiciando ancho util para un reporte de indicadores donde conviene ver la mayor cantidad de columnas posible.

Correcciones aplicadas:

- Tiempo de sesion ya pagina en SQL real con COUNT separado y LIMIT/OFFSET.
- La materializacion de rep_sesion ahora evita recalculo cuando ya existe informacion del dia y no hay eventos nuevos posteriores al ultimo evento procesado.
- El reporte queda marcado como ancho/denso para priorizar visibilidad de columnas en desktop, manteniendo el ajuste responsivo para movil y tablet.

Archivos:

- application/models/Reportes_model.php
- application/models/Repoback_model.php
- webroot/application/controllers/Reportes.php
- webroot/css/main.css

### 10. Reportes -> Buzon de voz, Atendidas cada media hora y Abandonadas cada media hora

Hallazgos de performance:

- Buzon de voz seguia filtrando con DATE(vm.datetime_received) y usando el helper generico que carga todo para paginar en PHP.
- Atendidas cada media hora y Abandonadas cada media hora seguian pasando por el helper generico aun cuando la salida es agrupada por media hora.
- La materializacion de rep_atendidas seguia recorriendo el dia completo con DATE(c.datetime_received) en cada acceso.

Correcciones aplicadas:

- Buzon de voz ya usa rango datetime indexable y paginacion SQL real con COUNT separado y LIMIT/OFFSET.
- Atendidas cada media hora y Abandonadas cada media hora ya usan paginacion SQL real sin traer toda la consulta a PHP.
- rep_atendidas ahora se materializa de forma incremental por max(id) del dia y con rango datetime indexable.

Archivos:

- application/models/Reportes_model.php
- application/models/Repoback_model.php

### 11. Reportes graficos -> Distribucion por agente y Analisis por colas

Hallazgos de performance:

- Ambos endpoints seguian filtrando con DATE(datetime_received) sobre call_entry, lo que inutilizaba indices por fecha/hora.
- El cliente disparaba la carga de Google Charts en cada respuesta y reemplazaba la grafica renderizada por una imagen fija, afectando redibujo y respuesta visual.

Correcciones aplicadas:

- Ambos endpoints ya usan rangos datetime completos desde 00:00:00 hasta 23:59:59.
- Las vistas quedaron integradas al shell visual moderno de reportes para mantener consistencia con el resto del barrido.
- El JS ahora carga Google Charts una sola vez, evita solicitudes concurrentes, mantiene la grafica viva para resize y conserva la exportacion PDF usando getImageURI del chart activo.

Archivos:

- application/models/Reportes_model.php
- webroot/application/views/reportes/poragentegra.php
- webroot/application/views/reportes/porcolasgra.php
- webroot/js/reportes/poragentegra.js
- webroot/js/reportes/porcolasgra.js

### 12. Consola -> Colas en espera y Auxiliares

Hallazgos de interfaz:

- Los widgets flotantes seguian con una capa visual antigua basada en colores fijos y sin jerarquia visual.
- El colapso de auxiliares dependia principalmente de estilos inline desde JS.

Correcciones aplicadas:

- Se modernizo la presentacion de ambos widgets con encabezado, etiqueta de estado y superficies consistentes con el resto del front.
- El panel de auxiliares ahora usa una clase is-collapsed para su estado visual de colapso.
- Se mantiene el comportamiento draggable, el cronometro y las acciones de iniciar/terminar auxiliar.

Archivos:

- webroot/application/views/consola/consola.php
- webroot/js/consola/consola.js
- webroot/css/main.css

### 13. Materializaciones remanentes -> ACW, Llamadas por Agente e Inbound/Outbound con formularios

Hallazgos de performance:

- Las materializaciones diarias de rep_acw y rep_poragente seguian leyendo origen con DATE(datetime_init) y DATE(datetime_received).
- El flujo inOutBoundForm seguia filtrando call_entry con date(c.datetime_received) para exportes/listados diarios ligados a formularios.

Correcciones aplicadas:

- rep_acw ahora usa rango datetime indexable y conserva la misma fecha diaria en la tabla materializada.
- rep_poragente ahora usa rango datetime indexable y conserva la misma llave diaria agregada por usuario, tipo y campaña.
- inOutBoundForm ahora filtra call_entry con rango completo desde 00:00:00 hasta 23:59:59 en lugar de envolver datetime_received en DATE().

Archivos:

- application/models/Repoback_model.php
- application/models/Reportes_model.php

## Patron repetido en otros reportes

El problema no esta limitado a Inbound.

Hay un patron transversal en Datos_model::manejadorqueries:

- Ejecuta la consulta completa.
- Obtiene num_rows de todo el resultado.
- Convierte todo a result_array.
- Despues pagina en PHP con array_slice.

Esto significa que cualquier reporte grande puede sentirse lento aunque el usuario solo pida 20 registros.

Archivo:

- application/models/Datos_model.php

## Recomendacion priorizada

1. Migrar primero los reportes mas usados a paginacion SQL real, iniciando por Inbound, Tiempo de sesion, Outbound, Abandono, Tiempo de espera, Exitosas, IVR, Llamadas por Agente, Log de usuarios, Descansos y ACW.
2. Sustituir filtros DATE(columna) y TIME(columna) por rangos directos sobre datetime cuando exista indice.
3. Evitar precargas pesadas al entrar a una vista si esa misma preparacion puede hacerse de forma incremental o bajo demanda.
4. Mantener las tablas materializadas diarias con insercion incremental, no con recalculo completo por cada acceso a la vista.
5. Revisar indices en tablas fuente y tablas materializadas para datetime_received, fecha, id_campaign, id_user, status y type.

## Riesgos pendientes

- El helper generico manejadorqueries sigue afectando a otros reportes no intervenidos.
- Algunos modelos siguen usando DATE(datetime_received) entre rangos, por lo que todavia hay margen de mejora en varias rutas.
- Las vistas genericas siguen reconstruyendo todo el HTML del grid por respuesta; eso es aceptable para paginas de 20 filas, pero no escala igual de bien en tablas con mas columnas y acciones.

## Punto de reanudacion sugerido

Al retomar el trabajo, el siguiente corte logico ya identificado es:

1. Reemplazar filtros diarios con DATE(NOW()) y DATE(CURDATE()) al inicio de application/models/Reportes_model.php por rangos datetime indexables.
2. Revisar application/models/Repoback_model.php::sesion() y remanentes sobre user_log que todavia usan date(evento).
3. Evaluar si conviene atacar despues el helper application/models/Datos_model.php::manejadorqueries o seguir migrando solo los reportes mas usados a paginacion SQL real.

Nota de alcance:

- Los widgets de consola Auxiliares y Colas en espera ya quedaron funcionales y estabilizados.
- Los reportes graficos por agente y por colas ya quedaron modernizados visualmente y con filtros indexables.
- Lo pendiente fuerte ya no esta en la capa visual principal sino en remanentes de backend y en la decision de si se construye un reportador personalizado drag & drop.