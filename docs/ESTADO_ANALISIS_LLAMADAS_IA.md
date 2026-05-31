# Estado de analisis de llamadas IA

Fecha de corte: 2026-05-30

## Resumen ejecutivo

El proyecto ya quedo implementado en una primera version operativa para analisis de llamadas sobre reportes inbound y outbound.

Incluye:

- transcripcion local con Faster-Whisper
- analisis de sentimiento
- analisis de protocolo por reglas
- recomendaciones y score consolidado
- reproceso manual en calidad normal, alta y maxima
- submenu propio de reportes bajo Analisis de llamadas
- modal de analisis a demanda desde la tabla de reportes

El sistema ya funciona de punta a punta, pero la calidad de algunas transcripciones sigue siendo irregular por una combinacion de limite de modelo y, sobre todo, calidad real del audio de origen.

## Componentes implementados

### Backend web

- `webroot/application/controllers/Analisis.php`
- `webroot/application/models/Analisis_model.php`
- `webroot/application/models/Reportes_model.php`
- `webroot/application/views/reportes/reporte.php`
- `webroot/js/reportes/reporte.js`

Capacidades:

- boton `Analisis` por llamada
- estado `no solicitado` si aun no se encola
- solicitud manual de analisis
- reproceso normal
- reproceso alta calidad con `base-clean`
- reproceso maxima calidad con `large-v3-clean`
- acceso restringido a perfil `admin`

### Worker IA

- `scripts/worker_transcripcion.py`
- `scripts/run_worker_transcripcion.sh`
- `scripts/requirements_transcripcion.txt`

Capacidades:

- lectura de pendientes desde `call_ai_analysis`
- resolucion de ruta de audio de grabaciones inbound y outbound
- transcripcion local con Faster-Whisper
- limpieza opcional previa con `ffmpeg`
- sentimiento local heuristico
- pipeline de protocolo, score y recomendaciones
- metricas de tiempo de proceso
- ajuste de parametros por variables de entorno

### Persistencia

Esquemas agregados:

- `database/schema/call_ai_analysis.sql`
- `database/schema/call_ai_protocol.sql`

Tablas principales:

- `call_ai_analysis`
- `call_ai_protocol_rule`
- `call_ai_protocol_result`
- `call_ai_recommendation`
- `call_ai_score`

## Flujo operativo actual

1. El usuario admin abre el reporte inbound u outbound.
2. Si la llamada tiene grabacion, ve el boton `Analisis`.
3. El modal muestra estado actual y permite `Solicitar analisis`.
4. El worker procesa pendientes desde consola o cron.
5. El resultado vuelve al modal y a los reportes IA.
6. Si la calidad es insuficiente, el usuario puede reprocesar con perfiles superiores.

## Estado de calidad de transcripcion

### Lo que ya se hizo

- modelo base por defecto en `tiny` para costo controlado
- escalamiento automatico de `tiny` a `small` en llamadas largas
- VAD desactivado por defecto para evitar recortes agresivos
- perfiles `base-clean` y `large-v3-clean`
- instalacion y uso de `ffmpeg`
- medicion real de tiempo por minuto de audio
- pruebas reales con llamadas del entorno

### Hallazgos reales

- `tiny` puede dejar frases cortadas o deformadas en llamadas largas o sucias
- `base` mejora cobertura, pero no corrige un audio degradado
- en llamadas reales ya se comprobo mejora marginal de caracteres sin recuperar del todo el contenido
- por lo tanto, el problema no es solo de modelo; tambien de fuente de audio

### Que se puede hacer a partir de aqui

1. Tomar una muestra pequena de llamadas malas y clasificarlas por tipo de defecto: bajo volumen, clipping, ruido, eco, traslape o canal deficiente.
2. Ejecutar una matriz corta de pruebas por muestra: `base-clean`, `large-v3-clean` y variantes de `FW_AUDIO_FILTERS`.
3. Afinar decodificacion con `FW_BEAM_SIZE`, `FW_BEST_OF`, `FW_PATIENCE`, `FW_CONDITION_ON_PREVIOUS_TEXT` y `FW_INITIAL_PROMPT`.
4. Si la inteligibilidad humana ya es mala, corregir origen de audio o captura antes de seguir invirtiendo CPU en Whisper.
5. Reservar `large-v3-clean` para auditorias y llamadas criticas, no para todo el lote.

## Reportes habilitados

Bajo el menu `Reportes > Llamadas > Analisis de llamadas`:

- `Analisis IA General`
- `Analisis IA Inbound`
- `Analisis IA Outbound`
- `Protocolo IA General`
- `Protocolo IA Detalle`

## UI y modales

Se modernizaron tres modales compartidos en reportes:

- Audio
- Evaluacion cualitativa
- Analisis de llamada

Estado actual:

- Audio: corregido
- Evaluacion cualitativa: corregida, con metadatos completos y footer fijo con `Cancelar` y `Guardar evaluacion`
- Analisis: funcional, a demanda, con transcripcion ampliada y acciones de reproceso

## Riesgos y pendientes tecnicos

- Probar `large-v3-clean` sobre una muestra real comparativa
- Afinar decodificacion Whisper con nuevos parametros del worker
- Mejorar limpieza de audio por perfil de grabacion
- Evaluar si conviene separar canales o hacer preprocesamiento mas agresivo
- Evitar que la calidad percibida del analisis se juzgue solo por transcripciones de audio muy degradado

## Recomendacion operativa inmediata

No mover todavia todo el volumen a un modelo mas pesado. Primero conviene tomar 10 a 20 llamadas malas, compararlas con una tabla simple de configuraciones y decidir con evidencia si el siguiente salto debe ser:

- mejor filtro de audio
- ajuste de decodificacion
- cambio de modelo por tipo de llamada
- o correccion del origen de grabacion