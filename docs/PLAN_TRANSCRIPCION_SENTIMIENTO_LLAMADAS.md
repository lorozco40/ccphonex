# Plan de transcripcion y sentimiento de llamadas

Fecha: 2026-05-20
Alcance inicial: inbound y outbound de SEGTEC 087

## Decisiones cerradas

- Canales incluidos: inbound y outbound
- Campana inicial: SEGTEC 087
- Llamadas elegibles: mayores a 15 segundos
- Motor de transcripcion: Faster-Whisper local
- UI inicial: modal simple desde el reporte
- Persistencia: tabla nueva separada
- Procesamiento: worker asincrono
- Acceso: todos los perfiles excepto agente

## Objetivo

Agregar un tercer boton en los reportes inbound y outbound para consultar la transcripcion y el analisis de sentimiento de una llamada, sin bloquear la carga del reporte ni procesar audio en la peticion web.

La arquitectura debe apoyarse en el flujo actual de reportes y en las grabaciones ya existentes, pero separar el procesamiento IA del frontend PHP.

## Reglas funcionales iniciales

1. Solo se procesan llamadas de la campana SEGTEC 087.
2. Solo se procesan llamadas terminadas con grabacion disponible.
3. Solo se procesan llamadas con duracion mayor a 15 segundos.
4. El boton Analisis aparece en inbound y outbound para usuarios con perfil distinto de agente.
5. Si el analisis no existe aun, el modal muestra estado pendiente o procesando.
6. El procesamiento corre en segundo plano; nunca en el click del usuario.
7. Debe existir opcion de reprocesar para perfiles con permisos de supervision o administracion.

## Arquitectura propuesta

### Capa web actual

- Controlador PHP: expone endpoints de consulta y reproceso.
- Modelo PHP: agrega el boton Analisis a la tabla de inbound y outbound.
- Vista y JS actuales: abren modal y pintan estado, transcripcion y sentimiento.

### Capa IA separada

- Microservicio Python interno para transcripcion con Faster-Whisper.
- Analisis de sentimiento local a partir de la transcripcion.
- Worker asincrono o cron que consulta pendientes y llama al microservicio.

### Persistencia

- Tabla nueva `call_ai_analysis` en MySQL.
- No se modifica la estructura de `rep_inbound` ni `rep_outbound` para guardar texto largo o estados IA.

## Esquema SQL propuesto

```sql
CREATE TABLE call_ai_analysis (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    call_id BIGINT UNSIGNED NOT NULL,
    call_type ENUM('inbound', 'outbound') NOT NULL,
    id_campaign BIGINT UNSIGNED NOT NULL,
    campaign_name VARCHAR(150) NOT NULL DEFAULT '',
    id_agente BIGINT UNSIGNED NULL,
    agente VARCHAR(150) NOT NULL DEFAULT '',
    numero VARCHAR(40) NOT NULL DEFAULT '',
    fecha_llamada DATETIME NOT NULL,
    duracion_segundos INT UNSIGNED NOT NULL DEFAULT 0,
    grabacion VARCHAR(255) NOT NULL DEFAULT '',
    processing_status ENUM('pendiente', 'procesando', 'listo', 'error', 'omitido') NOT NULL DEFAULT 'pendiente',
    processing_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    processing_error TEXT NULL,
    provider VARCHAR(80) NOT NULL DEFAULT 'faster-whisper-local',
    model_name VARCHAR(80) NOT NULL DEFAULT 'tiny',
    transcription_language VARCHAR(20) NOT NULL DEFAULT 'es',
    transcription_text MEDIUMTEXT NULL,
    transcription_confidence DECIMAL(5,4) NULL,
    sentiment_label ENUM('positivo', 'neutro', 'negativo', 'mixto', 'sin_dato') NOT NULL DEFAULT 'sin_dato',
    sentiment_score DECIMAL(6,4) NULL,
    sentiment_summary TEXT NULL,
    analyzed_by_rule VARCHAR(80) NOT NULL DEFAULT 'local-v1',
    requested_by BIGINT UNSIGNED NULL,
    processing_started_at DATETIME NULL,
    processing_duration_seconds INT UNSIGNED NULL,
    transcription_seconds_per_minute DECIMAL(10,2) NULL,
    processed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_call_ai_analysis_call (call_id, call_type),
    KEY idx_call_ai_analysis_status (processing_status),
    KEY idx_call_ai_analysis_campaign (id_campaign, call_type),
    KEY idx_call_ai_analysis_fecha (fecha_llamada),
    KEY idx_call_ai_analysis_processed (processed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Notas del esquema

1. `call_id` debe apuntar al id original de `call_entry`, porque ese id ya es base de inbound y outbound.
2. `call_type` evita colisiones conceptuales y deja claro el origen del reporte.
3. `campaign_name`, `agente`, `numero` y `grabacion` se duplican de forma controlada para evitar consultas adicionales al cargar el detalle.
4. `transcription_text` usa `MEDIUMTEXT` para soportar llamadas largas.
5. `processing_status` permite operar la cola y mostrar estados legibles en UI.
6. `processing_duration_seconds` y `transcription_seconds_per_minute` permiten medir rendimiento real del motor por minuto de audio.

## Criterios de elegibilidad del worker

El worker debe marcar o procesar solo registros que cumplan:

```text
id_campaign = campana SEGTEC 087
call_type IN ('inbound', 'outbound')
duracion_segundos > 15
grabacion no vacia
processing_status IN ('pendiente', 'error')
```

Adicionalmente:

- Si no existe archivo de audio, el estado pasa a `error`.
- Si la llamada no cumple duracion o campana, el estado puede quedar en `omitido`.

## Endpoints propuestos

Se propone crear un controlador nuevo, por ejemplo `Analisis.php`, para no mezclar demasiado esta logica con `Reportes.php`.

### 1. Consultar estado o detalle

Ruta:

```text
POST /analisis/detalle
```

Request:

```json
{
  "call_id": 123456,
  "call_type": "outbound"
}
```

Comportamiento:

- Valida permisos: todos excepto perfil agente.
- Busca registro en `call_ai_analysis`.
- Si no existe y la llamada es elegible, puede crearlo en `pendiente`.
- Devuelve metadatos, estado y resultado si ya fue procesado.

### 2. Solicitar procesamiento manual

Ruta:

```text
POST /analisis/solicitar
```

Request:

```json
{
  "call_id": 123456,
  "call_type": "inbound"
}
```

Comportamiento:

- Crea registro `pendiente` si no existe.
- Si existe en `error`, lo deja listo para reintento segun politica.
- Si existe en `listo`, no reprocesa por defecto.

### 3. Reprocesar

Ruta:

```text
POST /analisis/reprocesar
```

Request:

```json
{
  "call_id": 123456,
  "call_type": "outbound",
  "force": 1
}
```

Comportamiento:

- Solo supervisor o admin.
- Reinicia estado a `pendiente`.
- Limpia `processing_error`.

### 4. Lote opcional para backfill

Ruta:

```text
POST /analisis/enqueue_batch
```

Request:

```json
{
  "call_type": "outbound",
  "campaign_name": "SEGTEC 087",
  "min": "2026-05-01",
  "max": "2026-05-20"
}
```

Comportamiento:

- Encola historicos de forma controlada.
- Recomendado solo para uso administrativo.

## JSON de respuesta propuesto

### Respuesta de `/analisis/detalle` cuando ya existe resultado

```json
{
  "ok": true,
  "data": {
    "call_id": 123456,
    "call_type": "outbound",
    "campaign_name": "SEGTEC 087",
    "agente": "Juan Perez",
    "numero": "5512345678",
    "fecha_llamada": "2026-05-20 10:15:22",
    "duracion_segundos": 184,
    "grabacion": "out-123.wav",
    "processing_status": "listo",
    "provider": "faster-whisper-local",
    "model_name": "tiny",
    "transcription_language": "es",
    "transcription_confidence": 0.9123,
    "sentiment_label": "negativo",
    "sentiment_score": -0.6400,
    "sentiment_summary": "Cliente molesto por tiempos de atencion y por seguimiento pendiente.",
    "transcription_text": "Buenos dias, le llamo de...",
    "processing_started_at": "2026-05-20 10:18:00",
    "processing_duration_seconds": 54,
    "transcription_seconds_per_minute": 17.61,
    "processed_at": "2026-05-20 10:20:41",
    "processing_error": null,
    "can_reprocess": true
  }
}
```

### Respuesta de `/analisis/detalle` cuando esta pendiente

```json
{
  "ok": true,
  "data": {
    "call_id": 123456,
    "call_type": "inbound",
    "processing_status": "pendiente",
    "status_message": "La llamada esta en cola para procesamiento.",
    "can_reprocess": false
  }
}
```

### Respuesta con error funcional

```json
{
  "ok": false,
  "error": "La llamada no es elegible para analisis: duracion menor o igual a 15 segundos."
}
```

## Boton nuevo en reportes

### Ubicacion

Se agrega en el mismo punto donde hoy se generan los botones de Audio y Evaluacion dentro de [webroot/application/models/Reportes_model.php](webroot/application/models/Reportes_model.php).

### Regla de visibilidad

- Visible para perfiles distintos de agente.
- Visible solo cuando la llamada pertenezca a SEGTEC 087.
- Visible en inbound y outbound.

### Texto sugerido

```text
Analisis
```

### Atributos sugeridos del boton

```html
<button
  type="button"
  class="btn btn-warning launch-analysis"
  data-call-id="123456"
  data-call-type="outbound"
  data-campaign="SEGTEC 087"
  data-toggle="modal"
  data-target="#analysisModal">
  Analisis
</button>
```

## Modal simple propuesto

### Objetivo

Mostrar el estado del procesamiento y, si ya existe, la transcripcion y sentimiento sin salir del reporte.

### Estructura sugerida

1. Encabezado
   - Fecha
   - Agente
   - Numero
   - Campana
   - Tipo de llamada

2. Estado
   - Pendiente
   - Procesando
   - Listo
   - Error

3. Reproductor de audio
   - Puede reutilizar el mismo enfoque del modal actual de audio.

4. Tarjeta de sentimiento
   - Etiqueta: Positivo, Neutro, Negativo o Mixto
   - Score
   - Resumen

5. Bloque de transcripcion
   - Area scrollable
   - Texto completo

6. Acciones
   - Cerrar
   - Reprocesar, si aplica

### Ejemplo de markup de referencia

```html
<div class="modal fade" id="analysisModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Analisis de llamada</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="analysis-meta"></div>
        <div id="analysis-status" class="mb-3"></div>
        <div id="analysis-audio" class="mb-3"></div>
        <div id="analysis-sentiment" class="mb-3"></div>
        <div id="analysis-transcription" class="border rounded p-3" style="max-height: 320px; overflow:auto;"></div>
        <div id="analysis-error" class="text-danger mt-3"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-secondary d-none" id="analysis-reprocess">Reprocesar</button>
      </div>
    </div>
  </div>
</div>
```

## Logica JS sugerida

1. Escuchar click en `.launch-analysis`.
2. Abrir modal y pintar un estado inicial de carga.
3. Llamar a `POST /analisis/detalle`.
4. Renderizar estado o resultado.
5. Si `processing_status` es `pendiente` o `procesando`, mostrar mensaje y opcion de refresco simple al reabrir.
6. Si `processing_status` es `listo`, mostrar sentimiento y transcripcion.
7. Si `processing_status` es `error`, mostrar mensaje y boton de reproceso si el usuario tiene permiso.

## Worker asincrono

### Responsabilidades

1. Tomar registros `pendiente`.
2. Marcar `procesando`.
3. Resolver ruta real del audio.
4. Transcribir con Faster-Whisper local.
5. Calcular sentimiento y resumen.
6. Guardar resultado y marcar `listo`.
7. Si falla, guardar error y aumentar intentos.

### Politica inicial

- Concurrencia baja: 1 a 2 workers.
- Maximo de intentos: 3.
- Modelo inicial: `small`.
- Reproceso manual solo para perfiles permitidos.

### Pseudoflujo

```text
buscar pendientes -> tomar lote pequeno -> validar audio -> transcribir -> analizar sentimiento -> guardar -> marcar listo
```

## Regla de acceso

La capa web debe negar acceso al boton y a los endpoints a usuarios con perfil `agente`.

Perfiles habilitados en fase inicial:

- supervisor
- admin
- otros perfiles de monitoreo o calidad distintos de agente

## Fase 1 recomendada

1. Crear tabla `call_ai_analysis`.
2. Crear endpoints `detalle`, `solicitar`, `reprocesar`.
3. Agregar boton Analisis en inbound y outbound para SEGTEC 087.
4. Crear modal simple.
5. Implementar worker con Faster-Whisper local.

## Riesgos controlados en esta fase

1. No se procesa todo el universo de llamadas, solo SEGTEC 087.
2. No se modifica la carga actual del reporte.
3. No se procesa a demanda desde el click del usuario.
4. No se mezclan campos IA con las tablas materializadas del reporte.

## Siguiente paso de implementacion

1. Crear migracion SQL.
2. Implementar controlador `Analisis.php`.
3. Agregar columna visual o boton nuevo en `Reportes_model::inbound` y `Reportes_model::outbound`.
4. Extender `reporte.php` y `reporte.js` para abrir el modal y consultar el detalle.
5. Crear microservicio Python y worker de cola.