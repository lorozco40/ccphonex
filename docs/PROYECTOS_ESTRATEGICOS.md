# Proyectos estrategicos propuestos

Fecha: 2026-05-06

## Objetivo del documento

Concentrar en un solo lugar los proyectos funcionales y tecnicos que siguen despues del barrido de performance actual, priorizando iniciativas con impacto operativo directo y costo controlado.

## Proyecto 1. Constructor visual de reportes y dashboards

### Objetivo

Permitir que usuarios internos creen dashboards y reportes personalizados sin depender de desarrollo para cada vista nueva, usando bloques configurables y reordenables.

### Alcance propuesto

Primera version orientada a usuarios internos de operacion, supervision y coordinacion.

Datasets iniciales sugeridos:

- llamadas
- agentes
- colas
- descansos y ACW
- campañas
- tickets

Bloques iniciales sugeridos:

- tabla
- KPI
- grafica de barras
- grafica de pastel
- serie temporal simple

Capacidades de la primera version:

- drag and drop de bloques
- configuracion de filtros por bloque y filtros globales
- guardar plantillas por usuario
- compartir plantillas por rol o area
- exportar a Excel y PDF

### Fases sugeridas

#### Fase 0. Definicion funcional

- definir usuarios objetivo
- cerrar datasets iniciales
- definir widgets iniciales
- definir permisos de crear, editar, clonar, compartir y visualizar
- definir alcance de exportaciones

Entregable:

- documento funcional corto aprobado

#### Fase 1. Base del constructor

- crear modelo de plantillas
- persistir layout, filtros y widgets
- construir editor base con lienzo central y panel de configuracion
- renderer que pinte widgets desde configuracion guardada

Entregable:

- crear, guardar y cargar plantilla vacia o basica

#### Fase 2. Widgets operativos

- tabla configurable con columnas, orden y filtros
- KPI configurable
- barras, pastel y linea simple
- drag and drop y resize basico por grid

Entregable:

- primer dashboard funcional con datos reales

#### Fase 3. Exportacion y comparticion

- exportacion Excel por widget y por reporte
- exportacion PDF del dashboard completo
- clonado y comparticion por usuario o rol

Entregable:

- dashboards reutilizables por area

#### Fase 4. Madurez

- formulas derivadas
- pivotes basicos
- versionado de plantillas
- auditoria de cambios
- cache selectiva

### Recomendacion tecnica

Para este sistema conviene iniciar con un constructor visual guiado sobre datasets predefinidos, no con un motor libre tipo SQL.

Motivos:

- mejor control de performance
- menor riesgo de seguridad
- mas rapido de construir
- mas simple de soportar

### Modelo minimo sugerido

Tablas conceptuales:

- report_templates
- report_template_widgets
- report_template_filters
- report_template_shares

Campos minimos por plantilla:

- id
- nombre
- dataset_base
- owner_user_id
- layout_json
- config_json
- scope
- created_at
- updated_at

## Proyecto 2. Analitica de calidad con speech-to-text

### Objetivo

Analizar grabaciones de llamadas para generar indicadores de calidad por llamada, por agente y por campaña usando herramientas libres o de bajo costo, minimizando dependencia de servicios externos por minuto.

### Aclaracion funcional

Para evaluar calidad de llamadas, lo que aporta valor principal es speech-to-text, no text-to-speech.

Text-to-speech podria agregarse despues para:

- lectura automatica de resumentes
- asistentes de supervision
- alertas habladas

Pero el nucleo del proyecto es transcripcion y analitica de audio.

### Ancla en el sistema actual

La estructura actual favorece este proyecto porque el sistema ya referencia grabaciones y las vincula con llamadas y campañas.

Puntos confirmados en el codigo:

- las grabaciones se guardan en call_entry.grabacion
- existen reportes y modales que ya consumen el campo grabacion
- hay rutas de trabajo que resuelven audios desde /var/spool/asterisk/monitor
- el codigo ya construye rutas por fecha para ubicar audios bajo /var/spool/asterisk/monitor/YYYY/MM/DD

Esto permite montar un pipeline batch sin rediseñar toda la captura de audio.

### Enfoque de menor costo recomendado

#### Opcion recomendada para arrancar

Procesamiento batch local o en servidor dedicado usando herramientas open source.

Stack sugerido:

- FFmpeg para normalizacion y conversion a wav mono 16 kHz
- Faster-Whisper para transcripcion offline
- Silero VAD para detectar voz y recortar silencios
- Python workers por cola o cron para procesamiento asincrono
- MySQL para guardar resultados y metricas

Ventajas:

- costo por llamada cercano a cero despues de infraestructura
- no depende de API externa por minuto
- mejor control de privacidad
- se adapta bien a lote nocturno o por prioridad

#### Opcion intermedia

Whisper API o proveedor cloud solo para campañas premium o muestreo.

Ventajas:

- menor esfuerzo inicial
- buena calidad en algunos escenarios

Desventajas:

- costo recurrente por minuto
- menos control de privacidad

#### Opcion no recomendada para iniciar

Analitica en tiempo real con diarizacion avanzada y scoring semantico completo desde el dia 1.

Motivos:

- sube mucho el costo computacional
- complica la arquitectura
- no hace falta para validar valor de negocio inicial

### Arquitectura propuesta de bajo costo

#### Fase 1. Ingestion y normalizacion

- detectar llamadas terminadas con grabacion disponible
- resolver ruta fisica del audio
- convertir a wav mono 16 kHz
- registrar cola de procesamiento

#### Fase 2. Transcripcion

- correr transcripcion offline con Faster-Whisper
- guardar texto, confianza promedio y tiempos por segmento
- opcionalmente generar resumen corto por llamada

#### Fase 3. Indicadores por llamada

Indicadores iniciales de bajo costo:

- duracion total
- porcentaje estimado de silencio
- tiempo de habla estimado
- presencia de saludo requerido
- presencia de cierre requerido
- menciones de palabras clave
- deteccion de frases prohibidas o de riesgo
- longitud de transcripcion
- score basico de cumplimiento del script

Indicadores fase 2:

- clasificacion de motivo de llamada
- deteccion de sentimiento o tono aproximado
- deteccion de intencion de cancelacion, queja o venta perdida

#### Fase 4. Indicadores agregados por campaña

- cumplimiento promedio del script
- porcentaje de llamadas sin saludo correcto
- distribucion de motivos
- ranking de palabras y temas frecuentes
- campañas con mas llamadas de riesgo
- agentes o campañas con mas quejas detectadas

### Indicadores sugeridos

#### Por llamada

- id de llamada
- campaña
- agente
- fecha
- duracion
- audio disponible
- texto transcrito
- score de confianza
- score de cumplimiento
- banderas de riesgo
- resumen automatico

#### Por campaña

- volumen analizado
- promedio de duracion
- promedio de cumplimiento
- porcentaje con saludo correcto
- porcentaje con cierre correcto
- porcentaje con frases de riesgo
- top motivos
- top keywords

### Como minimizar costo de verdad

1. Procesar solo llamadas terminadas y con duracion mayor a un umbral.
2. Empezar con lote nocturno, no tiempo real.
3. Priorizar primero campañas de mayor volumen o valor.
4. No hacer diarizacion en la fase 1.
5. Guardar texto y metricas; evitar persistir copias extra del audio.
6. Reprocesar solo cuando cambie el modelo o las reglas.

### Herramientas sugeridas

#### Opcion open source y realista

- Python
- FFmpeg
- Faster-Whisper
- Silero VAD
- pandas para agregados
- cron o supervisord para workers

#### Si despues se requiere subir precision

- WhisperX para mejor alineacion temporal
- pyannote.audio para diarizacion
- modelos de clasificacion de texto para sentimiento o categorias

### Limitaciones esperadas

- sin diarizacion no siempre se separara perfecto agente y cliente
- las grabaciones ruidosas o con baja calidad tendran menor precision
- si hay mezcla de canales en un solo audio, la analitica avanzada requerira una segunda fase

### Fases sugeridas del proyecto

#### Fase 0. Descubrimiento tecnico

- auditar muestra real de grabaciones
- medir formatos, tamaño, ruido y consistencia de nombres
- validar rendimiento de transcripcion por hora de audio

Entregable:

- informe de factibilidad y costo estimado por 1000 llamadas

#### Fase 1. MVP de transcripcion y scoring basico

- pipeline batch
- guardar transcripcion
- reglas basicas por llamada
- vista simple por campaña

Entregable:

- dashboard inicial de calidad por llamada y campaña

#### Fase 2. Analitica operativa

- clasificacion de motivos
- alertas por riesgo
- filtros por agente, campaña y fecha
- exportaciones

Entregable:

- modulo util para supervision y coaching

#### Fase 3. Analitica avanzada

- diarizacion
- score semantico mas fino
- recomendaciones automáticas
- resumen ejecutivo por campaña

### Prioridad recomendada

Orden sugerido de ejecucion:

1. terminar remanentes de performance de reportes actuales
2. definir funcionalmente el constructor visual
3. ejecutar descubrimiento tecnico del proyecto de speech-to-text
4. construir MVP de analitica por llamada y campaña

## Decision recomendada para iniciar

Si se busca el mejor balance entre impacto y costo:

- Constructor visual: iniciar con datasets controlados y bloques guiados.
- Analitica de voz: iniciar con Faster-Whisper + FFmpeg + reglas basicas batch, sin tiempo real y sin diarizacion en la primera fase.

## Proyecto 3. Enriquecimiento del reporte Inbound con transcripcion y auditoria IA

### Objetivo

Expandir el reporte de llamadas Inbound para que cada fila de llamada muestre no solo el acceso al audio, sino tambien informacion operativa adicional y dos nuevas superficies funcionales:

- Transcripcion
- Auditoria de calidad por IA

### Idea funcional

En el reporte Inbound, cada llamada deberia poder exponer al final de su fila:

1. Ruta o referencia tecnica de la grabacion.
2. Informacion adicional relevante de la llamada.
3. Campo o accion de Transcripcion.
4. Campo o accion de Auditoria IA.

Esto convierte al reporte Inbound en una bandeja de analisis, no solo en un listado historico.

### Estado actual

Actualmente el reporte ya cuenta con:

- identificador de llamada
- campaña
- agente
- estatus
- boton de audio
- evaluacion cualitativa manual basada en grabacion

La base tecnica ya existe porque cada fila ya conoce la grabacion asociada.

### Alcance propuesto sin mover codigo aun

Primera etapa de planeacion:

- documentar columnas nuevas
- definir comportamiento de cada nueva columna
- decidir si se mostraran como texto, boton, badge o modal
- definir flujo de procesamiento de transcripcion y scoring IA fuera del render principal del reporte

### Columnas sugeridas para agregar mas adelante

#### Opcion conservadora

- Grabacion
- Ruta audio
- Transcripcion
- Auditoria IA

#### Opcion mas util para operacion

- Grabacion
- Ruta audio
- Resumen IA
- Transcripcion
- Score IA
- Auditoria IA
- Riesgos detectados

### Comportamiento sugerido por columna

#### Ruta audio

- mostrar la referencia resuelta o una forma resumida de la ruta
- permitir copiar o inspeccionar si el usuario tiene permiso
- en interfaz normal conviene mostrar version resumida y no la ruta completa cruda

#### Transcripcion

- si no existe: badge Pendiente o boton Generar
- si existe: boton Ver transcripcion
- si fallo: badge Error o Reintentar

#### Auditoria IA

- si no existe: badge Pendiente
- si existe: score o boton Ver auditoria
- si hay hallazgos criticos: badge Riesgo

### Arquitectura funcional sugerida

#### Fase 1. Superficies en el reporte

- agregar columnas nuevas en Inbound
- mostrar estados vacios o pendientes
- no ejecutar procesamiento pesado durante la carga del reporte

#### Fase 2. Procesamiento asincrono

- un worker toma llamadas con grabacion disponible
- genera transcripcion
- genera indicadores o score de auditoria IA
- persiste resultados para ser mostrados en el reporte

#### Fase 3. Visualizacion detallada

- modal de transcripcion completa
- modal o panel de auditoria IA
- resumen por llamada con hallazgos y score

### Principio tecnico recomendado

La transcripcion y la auditoria IA no deben ejecutarse dentro de la consulta del reporte ni durante el render de la tabla.

Deben vivir como un pipeline asincrono con persistencia separada y estados por llamada.

### Estructura de datos sugerida

Tablas conceptuales futuras:

- call_ai_transcription
- call_ai_audit
- call_ai_jobs

Campos sugeridos minimos:

- call_entry_id
- linkedid
- grabacion
- status_proceso
- transcript_text
- transcript_confidence
- ai_score
- ai_summary
- ai_flags_json
- processed_at

### Beneficio esperado

- el supervisor podra revisar una llamada desde un solo grid
- se reduce el salto entre escuchar audio, evaluar manualmente y revisar hallazgos automaticos
- se prepara el sistema para reportes agregados de calidad por campaña y por agente

### Nota de implementacion futura

Este proyecto debe montarse sobre el proyecto de speech-to-text ya descrito arriba.

Orden recomendado:

1. discovery tecnico del audio
2. pipeline batch de transcripcion
3. persistencia de resultados por llamada
4. columnas y modales nuevos en Inbound
5. auditoria IA y scores agregados

### Estado del proyecto

Planeado solamente.

No se aplicaron cambios de codigo ni de interfaz en esta etapa.

## Proyecto 4. Agentes de voz IA para mesa de ayuda y operaciones simples

### Objetivo

Construir agentes de voz que atiendan llamadas entrantes y resuelvan operaciones sencillas usando guiones existentes, captura estructurada de respuestas y automatizacion posterior de acciones como levantar tickets, informar precios o entregar informacion operativa.

### Idea funcional

El agente de voz debe:

1. contestar la llamada
2. reproducir un guion base segun la necesidad
3. escuchar y entender la respuesta del usuario
4. capturar datos clave
5. validar informacion minima
6. ejecutar una accion o escalar a humano cuando corresponda

En una primera etapa, el foco sugerido es mesa de ayuda.

### Casos de uso iniciales sugeridos

- levantar ticket de soporte
- consultar estado de ticket
- recopilar datos para mesa de ayuda
- dar informacion basica y preguntas frecuentes
- registrar incidencia y clasificarla
- transferir a humano cuando detecte complejidad o frustracion

### Por que este proyecto tiene sentido aqui

El sistema ya trabaja con llamadas, campañas, guiones, grabaciones y flujos operativos de atencion. Eso permite que el agente de voz no sea un producto aislado, sino una extension natural del ecosistema existente.

### Enfoque recomendado

No construir un bot completamente libre desde el inicio.

Conviene iniciar con un agente guiado por flujos y slots, es decir:

- guion controlado
- preguntas secuenciales
- captura de datos estructurados
- reglas claras de salida

Eso reduce costo, errores y riesgo operativo.

### Arquitectura funcional sugerida

#### Fase 0. Definicion de flujos

- identificar tramites simples y repetitivos
- priorizar mesa de ayuda
- descomponer cada flujo en pasos y validaciones
- definir criterios de transferencia a humano

Entregable:

- catalogo de flujos iniciales y mapa conversacional

#### Fase 1. Agente guiado por script

- el bot reproduce prompts de voz
- captura respuestas del usuario
- rellena campos estructurados
- confirma informacion capturada
- genera ticket o registro

Entregable:

- MVP para alta de ticket por voz

#### Fase 2. Integracion operativa

- consulta de tickets
- respuestas frecuentes
- consulta simple de precios o servicios
- reglas de enrutamiento por tipo de solicitud

Entregable:

- modulo de atencion automatizada para operaciones simples

#### Fase 3. Mejora conversacional

- mayor tolerancia a variaciones del lenguaje
- resumen automatico de la llamada
- sugerencia de categoria o prioridad
- deteccion de intencion de escalar

Entregable:

- experiencia mas natural y mejor precision de captura

### Componentes tecnicos conceptuales

- motor de reconocimiento de voz a texto
- motor de prompts de voz
- orquestador de dialogo
- catalogo de guiones
- motor de slots y validaciones
- integracion con tickets o sistemas internos
- bitacora de conversacion y resultados

### Opcion de menor costo recomendada

Para una primera fase funcional y de bajo costo conviene usar componentes abiertos o de bajo costo con flujo guiado.

Stack conceptual sugerido:

- STT offline o batch casi tiempo real con Faster-Whisper cuando aplique
- prompts pregrabados o TTS economico segun calidad requerida
- motor de reglas propio o flujo declarativo por pasos
- backend de integracion para crear tickets

### Recomendacion practica de arranque

Para no elevar complejidad desde el dia 1:

- usar prompts controlados, no conversacion completamente libre
- limitar a 1 o 2 flujos de mesa de ayuda
- capturar solo datos obligatorios
- transferir a humano en cuanto el flujo se salga del guion

### Datos a capturar en MVP de mesa de ayuda

- nombre
- empresa o cuenta
- telefono
- correo
- tipo de incidencia
- descripcion breve
- prioridad sugerida
- confirmacion final

### Integraciones futuras sugeridas

- creacion automatica de ticket
- consulta de estado por folio
- notificacion al area correspondiente
- resumen de llamada en CRM o mesa de ayuda

### Riesgos principales

- mala captura de nombres propios o datos alfanumericos
- usuarios que se salen del guion
- necesidad de fallback rapido a agente humano
- calidad de audio variable

### Principio de implementacion

Este proyecto debe iniciar como automatizacion guiada, no como asistente general.

La metrica de exito inicial no debe ser "conversacion natural", sino:

- porcentaje de tickets levantados correctamente
- tiempo promedio de captura
- porcentaje de llamadas escaladas a humano
- satisfaccion operativa del area de soporte

### Estado del proyecto

Planeado solamente.

No se aplicaron cambios de codigo ni de infraestructura en esta etapa.

## Proyecto 5. Manual de uso de Assertive enfocado en telefonia

### Objetivo

Crear un manual de uso claro, operativo y mantenible para los modulos de telefonia de Assertive, orientado a agentes, supervisores y personal de soporte interno.

### Alcance inicial sugerido

Primera version enfocada solo en telefonia.

Cobertura sugerida:

- acceso al sistema
- consola de agente
- llamadas entrantes
- llamadas salientes
- auxiliares o descansos
- colas en espera
- transferencia basica
- escucha de grabaciones
- evaluacion de calidad
- reportes principales de telefonia
- errores comunes y resolucion rapida

### Tipos de manual recomendados

No conviene hacer un solo manual gigante.

Conviene separar en tres capas:

Decision tomada:

- se haran tres manuales separados, no un unico documento consolidado
- cada manual tendra su propio alcance, lenguaje y nivel de detalle

#### 1. Manual rapido de agente

- entrar al sistema
- atender llamada
- poner auxiliar
- terminar auxiliar
- hacer llamada
- transferir
- consultar informacion basica

#### 2. Manual operativo de supervision

- monitoreo basico
- revision de grabaciones
- evaluacion de calidad
- lectura de reportes de telefonia
- seguimiento de colas y agentes

#### 3. Manual tecnico funcional interno

- rutas funcionales del sistema
- conceptos base
- permisos relacionados
- dependencias operativas
- guia de soporte de primer nivel

### Formato recomendado

La mejor combinacion para adopcion y mantenimiento suele ser:

- documento maestro en Markdown
- version PDF o HTML para distribucion
- capturas de pantalla actualizadas
- seccion de preguntas frecuentes

### Estructura sugerida del manual de telefonia

#### Capitulo 1. Introduccion

- que es Assertive en el contexto de telefonia
- perfiles de usuario
- alcance del manual

#### Capitulo 2. Acceso y entorno

- ingreso al sistema
- elementos de la interfaz
- recomendaciones de uso

#### Capitulo 3. Consola del agente

- panel principal
- auxiliares
- colas en espera
- datos visibles durante llamada

#### Capitulo 4. Operacion de llamadas

- recibir llamada
- realizar llamada saliente
- transferir llamada
- finalizar llamada
- uso correcto de estados

#### Capitulo 5. Auxiliares y disponibilidad

- cuando usar cada auxiliar
- impacto operativo
- errores comunes

#### Capitulo 6. Grabaciones y calidad

- escuchar una grabacion
- iniciar evaluacion cualitativa
- interpretar resultados de calidad

#### Capitulo 7. Reportes de telefonia

- inbound
- outbound
- abandono
- tiempo de espera
- llamadas por agente
- ACW y descansos

#### Capitulo 8. Problemas frecuentes

- no entra audio
- no se visualiza llamada
- no cambia auxiliar
- no carga grabacion
- no aparece informacion en reporte

#### Capitulo 9. Buenas practicas

- uso correcto de auxiliares
- cierre de llamada
- captura de informacion
- escalamiento oportuno

### Enfoque de construccion recomendado

No escribirlo solo desde la perspectiva tecnica.

Conviene construirlo con base en tareas reales del usuario:

- que necesita hacer
- donde hace clic
- que resultado debe ver
- que hacer si falla

### Fases sugeridas

#### Fase 0. Inventario funcional

- identificar modulos de telefonia activos
- identificar perfiles de usuario
- listar flujos frecuentes

Entregable:

- indice aprobado del manual

#### Fase 1. Manual de agente

- redactar pasos operativos basicos
- capturas de pantalla
- resolucion de errores comunes

Entregable:

- version 1 del manual de agente como documento independiente

#### Fase 2. Manual de supervision

- reportes
- monitoreo
- calidad
- grabaciones

Entregable:

- version 1 del manual operativo de supervision como documento independiente

#### Fase 3. Manual tecnico funcional interno

- soporte de primer nivel
- permisos
- dependencias y rutas funcionales

Entregable:

- manual interno para soporte y onboarding como documento independiente

### Principio recomendado

El manual debe reflejar el sistema real despues de la modernizacion visual y de los ajustes operativos recientes.

Por eso conviene generarlo una vez estabilizados los cambios prioritarios de consola, reportes y telefonia.

### Estado del proyecto

Planeado solamente.

No se ha iniciado redaccion del manual en esta etapa.

Configuracion aprobada:

- Manual 1: Agente
- Manual 2: Supervision
- Manual 3: Tecnico funcional interno

## Siguiente paso sugerido

Antes de desarrollar cualquiera de los dos proyectos, conviene producir dos entregables ligeros:

1. especificacion funcional del constructor visual
2. discovery tecnico de grabaciones para speech-to-text con muestra real