# Reportes futuros de analisis y sentimiento

Fecha: 2026-05-30

## Avances implementados

- Tabla `call_ai_analysis` separada para no contaminar `rep_inbound` y `rep_outbound`.
- Boton `Analisis` disponible en reportes inbound y outbound para `SEGTEC 087`.
- Modal con estado, audio, sentimiento, transcripcion y reproceso.
- Flujo del modal ajustado a demanda: abrir el modal ya no encola automaticamente; ahora primero muestra el estado y permite solicitar el analisis manualmente.
- Modal ampliado a `modal-xl` con scroll interno mas visible para leer transcripciones largas.
- Worker local con Faster-Whisper y heuristica de sentimiento.
- Cron operativo cada 5 minutos.
- Captura de metricas de rendimiento por llamada:
  - `processing_started_at`
  - `processing_duration_seconds`
  - `transcription_seconds_per_minute`
- Reportes ya disponibles en la infraestructura generica de sistema:
    - `reportes/analisis_general`
    - `reportes/analisis_inbound`
    - `reportes/analisis_outbound`

## Reportes implementados

### 1. General

Ruta:

- `reportes/analisis_general`

Contenido actual:

- agrupacion por tipo de llamada y campaña
- total de llamadas encoladas/analizadas
- conteos por estado (`listo`, `pendiente`, `procesando`, `error`)
- distribucion basica de sentimiento (`positivo`, `neutro`, `negativo`)
- promedio de duracion de audio
- promedio de tiempo de proceso
- promedio de segundos por minuto de audio

### 2. Detalle inbound

Ruta:

- `reportes/analisis_inbound`

Contenido actual:

- fecha
- numero
- agente
- campaña
- duracion
- estado del analisis
- sentimiento y score
- confianza de transcripcion
- tiempo de proceso
- segundos por minuto de audio
- fecha de procesamiento
- extracto de transcripcion
- acceso directo a audio y modal de analisis

### 3. Detalle outbound

Ruta:

- `reportes/analisis_outbound`

Contenido actual:

- mismos campos operativos que inbound
- acceso directo a audio y modal de analisis

## Base de datos disponible

Tabla principal:

- `call_ai_analysis`

Cruces naturales:

- `rep_inbound`
- `rep_outbound`
- `campaign`
- `user_full` o `user`

## Indicadores generales sugeridos

### Generales compartidos

- Total de llamadas analizadas
- Total pendientes
- Total procesando
- Total con error
- Total omitidas
- Total listas
- Porcentaje de exito de procesamiento
- Tiempo promedio de transcripcion por llamada
- Tiempo promedio por minuto de audio
- Mediana de tiempo por minuto de audio
- Total de minutos de audio procesados

### Sentimiento compartido

- Total positivo
- Total neutro
- Total negativo
- Total mixto
- Distribucion porcentual por sentimiento
- Top campanas por negativo
- Top agentes por negativo

### Separados por canal

- Indicadores inbound
- Indicadores outbound
- Comparativo inbound vs outbound

## Detalles sugeridos

### Detalle inbound

- Fecha llamada
- Campana
- Agente
- Numero
- Duracion de audio
- Estado del analisis
- Modelo usado
- Tiempo total de proceso
- Segundos por minuto de audio
- Etiqueta de sentimiento
- Score de sentimiento
- Resumen de sentimiento
- Inicio de transcripcion
- Texto completo

### Detalle outbound

- Fecha llamada
- Campana
- Agente
- Numero
- Duracion de audio
- Estado del analisis
- Modelo usado
- Tiempo total de proceso
- Segundos por minuto de audio
- Etiqueta de sentimiento
- Score de sentimiento
- Resumen de sentimiento
- Inicio de transcripcion
- Texto completo

## Consultas base sugeridas

### Indicadores generales

```sql
SELECT
    call_type,
    COUNT(*) total,
    SUM(processing_status = 'listo') listas,
    SUM(processing_status = 'pendiente') pendientes,
    SUM(processing_status = 'procesando') procesando,
    SUM(processing_status = 'error') errores,
    SUM(processing_status = 'omitido') omitidas,
    ROUND(AVG(processing_duration_seconds), 2) promedio_segundos_proceso,
    ROUND(AVG(transcription_seconds_per_minute), 2) promedio_segundos_por_minuto,
    ROUND(SUM(duracion_segundos) / 60, 2) minutos_audio
FROM call_ai_analysis
GROUP BY call_type;
```

### Sentimiento por canal

```sql
SELECT
    call_type,
    sentiment_label,
    COUNT(*) total
FROM call_ai_analysis
WHERE processing_status = 'listo'
GROUP BY call_type, sentiment_label
ORDER BY call_type, total DESC;
```

### Detalle operativo

```sql
SELECT
    fecha_llamada,
    call_type,
    campaign_name,
    agente,
    numero,
    duracion_segundos,
    processing_status,
    model_name,
    processing_duration_seconds,
    transcription_seconds_per_minute,
    sentiment_label,
    sentiment_score,
    sentiment_summary
FROM call_ai_analysis
ORDER BY fecha_llamada DESC;
```

## Siguiente fase recomendada

1. Agregar acceso desde menu para los tres reportes IA si se quiere dejarlos visibles para operacion diaria.
2. Incorporar indicadores derivados: tasa de exito, minutos de audio procesados y comparativo inbound vs outbound.
3. Extender los detalles con transcripcion completa para exportacion CSV.
4. Conectar estos reportes con la siguiente capa de protocolo, cumplimiento y recomendaciones.