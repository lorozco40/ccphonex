# Avance de Plataforma a Ubuntu 24 y Comunicacion a Clientes

## Objetivo

Dejar documentado el estado actual de la plataforma Assertive despues del salto de Ubuntu 18 a Ubuntu 20, asi como la ruta segura para una futura migracion a Ubuntu 24.

Este documento tambien sirve como base para comunicar a clientes:

1. avance de plataforma;
2. necesidad de ventanas de mantenimiento;
3. validaciones previas y posteriores;
4. cierre exitoso o rollback controlado.

## Estado actual

Situacion confirmada al dia de hoy:

1. El servidor ya fue actualizado de Ubuntu 18 a Ubuntu 20.
2. Assertive continua operando correctamente sobre Ubuntu 20.
3. Los componentes criticos visibles siguen funcionales en el estado actual del servidor.
4. Ya existe una baseline de respaldo y reinstancia en este repositorio.
5. Ya existe un plan priorizado de modernizacion y actualizacion para reducir riesgo antes de Ubuntu 24.

## Lectura ejecutiva

El sistema se encuentra estable en Ubuntu 20, pero eso no significa que un salto a Ubuntu 24 deba ejecutarse directo sobre produccion.

La siguiente actualizacion debe tratarse como una migracion controlada de plataforma, no solo como un upgrade del sistema operativo. El mayor riesgo no esta en Linux por si mismo, sino en la compatibilidad conjunta de:

1. telephony stack;
2. PHP y librerias del sistema;
3. aplicacion web legacy;
4. bago y servicios auxiliares;
5. certificados, WebSocket y softphone.

## Avance ya realizado

### Infraestructura y recuperacion

1. Se genero un respaldo limpio para reinstancia.
2. Se documento y automatizo un procedimiento de restauracion base.
3. Se dejaron scripts y documentacion de contingencia.
4. Se preservo configuracion critica, llaves y esquemas SQL sin datos operativos.

### Aplicacion y operacion

1. Se estabilizo el acceso web en contingencia cuando fue necesario.
2. Se corrigio el problema del softphone WebRTC derivado de construccion incorrecta de URL WebSocket.
3. Se validaron elementos visuales y operativos del frontend en modulos criticos.
4. Se dejo una ruta de modernizacion por fases, priorizando estabilidad productiva.

### Planeacion de actualizacion

1. Ya se identificaron riesgos altos de configuracion sensible, stack legado, SQL dinamico y persistencia insegura en frontend.
2. Ya existe una recomendacion formal de no ejecutar salto directo a Ubuntu 24 sobre el productivo actual.
3. Ya quedo definido que el siguiente paso correcto es clonar el entorno actual y validar en staging.

## Lo que falta antes de Ubuntu 24

Orden recomendado de prioridad:

### Prioridad 1. Baseline y staging fiel

1. Congelar la linea base actual en Ubuntu 20.
2. Levantar una replica tecnica fiel del servidor productivo.
3. Confirmar que staging reproduzca web, bago, Asterisk, FreePBX, WebSocket y softphone.

### Prioridad 2. Compatibilidad de plataforma

1. Validar version objetivo de PHP para Ubuntu 24.
2. Validar compatibilidad de la aplicacion web legacy con esa version.
3. Validar compatibilidad de Asterisk, FreePBX, PJSIP y WebRTC.
4. Validar certificados, TLS y endpoints WebSocket.

### Prioridad 3. Configuracion y seguridad

1. Separar secretos del codigo.
2. Normalizar rutas y variables por entorno.
3. Inventariar llaves y certificados realmente usados en runtime.
4. Reducir persistencia sensible del frontend donde aplique.

### Prioridad 4. Validacion operativa

1. Crear checklist de smoke test.
2. Validar login, softphone, llamadas entrantes y salientes, reportes, bago y monitoreo.
3. Probar rollback completo antes de cualquier corte.

## Criterio para pedir ventana

Se debe pedir ventana de mantenimiento cuando se vaya a ejecutar cualquiera de estas actividades:

1. cambio de version de sistema operativo;
2. actualizacion de PHP o librerias base del sistema;
3. cambios en Asterisk, FreePBX, PJSIP, TLS o certificados;
4. sustitucion de servidor o corte a nueva instancia;
5. pruebas productivas que involucren reinicio de servicios criticos.

No hace falta pedir ventana amplia para actividades de analisis, staging, inventario, endurecimiento documental o validaciones fuera de produccion.

## Recomendacion operativa

Para Ubuntu 24, la recomendacion actual es:

1. no hacer do-release-upgrade directo sobre el productivo actual;
2. preparar una nueva instancia o clon controlado en Ubuntu 24;
3. validar integralmente en staging;
4. agendar ventana de corte solo cuando exista rollback probado.

## Plantillas de mensajes para clientes

Las siguientes plantillas estan pensadas para enviarse por correo, WhatsApp corporativo o ticket. Deben ajustarse con fecha, hora, alcance y contacto responsable.

### 1. Aviso preventivo de trabajo preparatorio

Asunto sugerido:

Avance de plataforma y preparacion de actualizacion controlada

Mensaje:

Estimado cliente,

Queremos informar que la plataforma Assertive se encuentra operando de forma estable despues de la actualizacion previa del servidor a Ubuntu 20.

Como parte de la estrategia de continuidad y seguridad, iniciaremos actividades preparatorias para una futura actualizacion controlada de plataforma hacia Ubuntu 24. En esta etapa no se contempla afectacion operativa, ya que el trabajo se enfocara en revision tecnica, validacion de compatibilidad y preparacion de ambiente de pruebas.

En caso de requerirse una ventana de mantenimiento para actividades productivas, se notificara por separado con el detalle de alcance, horario, validaciones y plan de reversa.

Quedamos atentos a cualquier duda.

### 2. Solicitud de ventana de mantenimiento

Asunto sugerido:

Solicitud de ventana de mantenimiento para actualizacion controlada de plataforma

Mensaje:

Estimado cliente,

Como parte del plan de actualizacion controlada de la plataforma Assertive, solicitamos una ventana de mantenimiento para ejecutar actividades programadas sobre la infraestructura del sistema.

Propuesta de ventana:

1. Fecha: [FECHA]
2. Hora de inicio: [HORA]
3. Duracion estimada: [DURACION]
4. Alcance: [ALCANCE]

Objetivo de la ventana:

1. ejecutar actualizacion o corte controlado;
2. validar servicios criticos;
3. confirmar operacion posterior al cambio.

Servicios a validar:

1. acceso web;
2. softphone y WebRTC;
3. telefonia y colas;
4. monitoreo y reportes;
5. servicios auxiliares del sistema.

Se cuenta con plan de reversa en caso de detectar una afectacion no controlada.

Favor de confirmarnos su aprobacion o, en su caso, proponer una ventana alternativa.

### 3. Recordatorio previo a la ventana

Asunto sugerido:

Recordatorio de ventana programada de mantenimiento

Mensaje:

Estimado cliente,

Les recordamos que se mantiene programada la ventana de mantenimiento de la plataforma Assertive para:

1. Fecha: [FECHA]
2. Hora: [HORA]
3. Duracion estimada: [DURACION]

Durante este periodo podria presentarse intermitencia o indisponibilidad parcial del servicio, dependiendo de la fase del cambio.

Les compartiremos confirmacion al inicio y cierre de la actividad.

### 4. Inicio de ventana

Mensaje:

Estimado cliente,

Iniciamos la ventana de mantenimiento programada para la plataforma Assertive a las [HORA].

En este momento se estan ejecutando las actividades previstas de actualizacion y validacion. Estaremos informando cualquier novedad relevante y confirmaremos el cierre al finalizar.

### 5. Avance durante la ventana

Mensaje:

Estimado cliente,

Compartimos avance de la ventana de mantenimiento:

1. actividad ejecutada: [ACTIVIDAD]
2. estatus actual: [EN CURSO / VALIDANDO / CON OBSERVACIONES]
3. siguiente paso: [SIGUIENTE PASO]
4. impacto observado: [SIN IMPACTO / INTERMITENCIA / BAJO OBSERVACION]

Continuamos con el plan establecido y enviaremos el siguiente corte de informacion al concluir la validacion principal.

### 6. Cierre exitoso

Asunto sugerido:

Cierre de mantenimiento y validacion exitosa

Mensaje:

Estimado cliente,

La ventana de mantenimiento de la plataforma Assertive ha concluido de forma exitosa.

Resultado:

1. actividad realizada: [ACTIVIDAD]
2. hora de cierre: [HORA]
3. validaciones completadas: acceso web, telefonia, softphone, monitoreo y servicios asociados
4. estatus final: operativo

Quedamos atentos por si requieren una validacion adicional de su lado.

### 7. Cierre con reversa controlada

Asunto sugerido:

Cierre de ventana con reversa preventiva

Mensaje:

Estimado cliente,

Durante la ventana de mantenimiento programada se detecto una condicion que recomendaba no continuar con el cambio en produccion, por lo que se ejecuto la reversa controlada conforme al plan previsto.

Resultado:

1. la plataforma fue devuelta a su estado estable anterior;
2. el servicio queda operativo;
3. se realizara un nuevo analisis antes de proponer una nueva ventana.

Compartiremos la siguiente propuesta una vez concluida la validacion complementaria.

## Mensaje corto para WhatsApp o aviso rapido

### Solicitud de ventana

Buen dia. Solicitamos una ventana de mantenimiento para actividades controladas de actualizacion de plataforma Assertive el dia [FECHA] a las [HORA], con una duracion estimada de [DURACION]. Al concluir enviaremos confirmacion y resultado de validaciones.

### Inicio

Buen dia. Iniciamos ventana programada de mantenimiento de Assertive a las [HORA]. Estaremos informando avances y cierre.

### Cierre

Buen dia. La ventana de mantenimiento concluyo de forma correcta. La plataforma queda operativa y validada en los servicios principales.

## Recomendacion de uso interno

Antes de enviar cualquier mensaje a cliente, completar siempre:

1. objetivo real del cambio;
2. entorno afectado;
3. fecha, hora y duracion;
4. impacto esperado;
5. responsable tecnico;
6. plan de rollback;
7. lista de validaciones post-cambio.

## Referencias internas

1. `docs/PLAN_ACTUALIZACION_PRIORIZADO.md`
2. `docs/PLAN_MODERNIZACION.md`
3. `docs/PROCEDIMIENTO_RESTAURACION.md`
4. `docs/contingencia_patch_README.txt`
