# Plan de recomendaciones, evaluacion y cumplimiento de script

Fecha: 2026-05-30
Estado actual: transcripcion y sentimiento ya operando sobre `call_ai_analysis`

## Objetivo de la siguiente fase

Convertir la transcripcion y el analisis de sentimiento en una capa real de supervision para call center, capaz de responder tres preguntas operativas:

1. Que tan bien ejecuto el agente el protocolo o script de la campaña.
2. Que recomendaciones concretas se pueden dar al agente o al supervisor.
3. Que indicadores generales y de detalle deben verse en reportes inbound y outbound.

## Base real ya disponible

Hoy ya existe infraestructura reutilizable en la plataforma:

- `call_ai_analysis`: guarda estado, transcripcion, sentimiento y metricas de tiempo del procesamiento.
- `quality` y `quality_fields`: ya resuelven cédulas o formularios de evaluacion por campaña, con ponderaciones y preguntas activas.
- `calidad/guardareval` y `calidad_model::traercampos()`: ya existe un flujo operativo de evaluacion manual.
- Reportes genericos: el sistema ya trabaja con vistas de indicadores, filtros y tablas de detalle.

Esto permite construir una capa IA de cumplimiento sin romper el flujo actual de calidad manual.

## Enfoque recomendado

No mezclar todo en una sola puntuacion de sentimiento.

Separar el problema en 4 capas:

1. Transcripcion
2. Sentimiento
3. Cumplimiento de protocolo o script
4. Recomendaciones operativas

Sentimiento solo responde como fue el tono o la experiencia.
Cumplimiento responde si el agente siguio el proceso esperado.
Recomendaciones responden que debe corregir o repetir el agente.

## Arquitectura funcional propuesta

### Capa 1. Analisis base por llamada

Ya implementada en `call_ai_analysis`.

Se usa como insumo comun para todo lo demas:

- texto transcrito
- score de sentimiento
- proveedor y modelo
- tiempo de procesamiento
- segundos por minuto de audio

### Capa 2. Motor de reglas de protocolo

Crear una capa nueva de reglas por campaña, separada del sentimiento.

Ejemplos de reglas:

- saludo inicial obligatorio
- identificacion de empresa
- validacion de nombre del cliente
- aviso legal o de grabacion
- explicacion del motivo de la llamada
- cierre con siguiente paso
- despedida correcta
- oferta o frase obligatoria de la campaña

Cada regla debe poder evaluarse sobre la transcripcion con resultado:

- cumple
- no_cumple
- no_aplica
- incierto

### Capa 3. Evaluacion automatica

Con las reglas anteriores se calcula una evaluacion automatica por llamada:

- score de cumplimiento total
- score por seccion del script
- hallazgos principales
- frases faltantes
- momentos de riesgo

### Capa 4. Recomendaciones

Con base en sentimiento + cumplimiento + contexto de campaña se generan recomendaciones accionables.

Ejemplos:

- El agente omitio el saludo institucional.
- No valido datos del cliente antes de avanzar.
- Hubo cierre debil: no se confirmo siguiente paso.
- El cliente mostro frustracion; conviene reforzar empatia en el primer minuto.
- La llamada tuvo tono neutro pero sin cumplimiento del speech comercial.

## Modelo de datos recomendado

### 1. Tabla de reglas del script por campaña

Propuesta: `call_ai_protocol_rule`

Campos sugeridos:

- `id`
- `id_campaign`
- `call_type` (`inbound`, `outbound`, `both`)
- `rule_group` ejemplo: apertura, validacion, oferta, cierre
- `rule_name`
- `rule_description`
- `expected_terms`
- `forbidden_terms`
- `evaluation_mode` ejemplo: contains_any, contains_all, semantic, regex, manual_override
- `weight`
- `required`
- `active`
- `created_at`
- `updated_at`

### 2. Resultado de cumplimiento por llamada y regla

Propuesta: `call_ai_protocol_result`

Campos sugeridos:

- `id`
- `id_call_ai_analysis`
- `call_id`
- `call_type`
- `id_campaign`
- `id_rule`
- `rule_group`
- `rule_name`
- `result_status` (`cumple`, `no_cumple`, `no_aplica`, `incierto`)
- `score`
- `evidence_text`
- `position_start`
- `position_end`
- `notes`
- `created_at`

### 3. Resumen automatico de coaching y recomendaciones

Propuesta: `call_ai_recommendation`

Campos sugeridos:

- `id`
- `id_call_ai_analysis`
- `call_id`
- `call_type`
- `recommendation_type` ejemplo: saludo, empatia, validacion, cierre, speech, venta
- `priority` (`alta`, `media`, `baja`)
- `message`
- `source` (`rule-engine`, `sentiment`, `supervisor`)
- `status` (`nueva`, `revisada`, `aplicada`, `descartada`)
- `created_at`
- `reviewed_by`
- `reviewed_at`

### 4. Resumen ejecutivo por llamada

Opcional: `call_ai_score`

Campos sugeridos:

- `id_call_ai_analysis`
- `protocol_score`
- `opening_score`
- `validation_score`
- `offer_score`
- `closing_score`
- `customer_experience_score`
- `risk_level`
- `compliance_summary`

## Integracion con calidad manual actual

La plataforma ya tiene un modelo de evaluacion humana por campaña con `quality` y `quality_fields`.

La mejor estrategia no es reemplazarlo, sino complementarlo.

### Opcion recomendada

Mantener dos capas de evaluacion:

1. Evaluacion humana oficial
2. Evaluacion automatica IA de apoyo

Relacion operativa:

- La IA pre-evalua la llamada.
- El supervisor ve sugerencias y cumplimiento del script.
- El supervisor puede confirmar o corregir la cedula manual.
- Con el tiempo se comparan ambos resultados para calibrar precision.

### Beneficio

Esto evita que un falso positivo de IA afecte directamente la evaluacion oficial del agente.

## Como detectar cumplimiento del script

Se propone una estrategia por niveles.

### Nivel 1. Reglas simples por palabras y frases

Rapido, barato y explicable.

Ejemplos:

- saludo: "buenos dias", "buenas tardes"
- identificacion: "le habla", "de SEGTEC"
- cierre: "algo mas en que le pueda apoyar", "gracias por su tiempo"

### Nivel 2. Reglas semanticas suaves

Buscar intencion, no solo coincidencia literal.

Ejemplos:

- validar si el agente se presento aunque no diga exactamente la frase del script
- detectar si ofrecio seguimiento o siguiente paso con palabras equivalentes

### Nivel 3. Reglas negativas

Detectar omisiones o desviaciones.

Ejemplos:

- no se identifico
- no se confirmo datos
- no se dio cierre
- uso frase prohibida

### Nivel 4. Comparacion contra script versionado

Cuando una campaña tenga speech formal bien definido:

- guardar versiones del script por campaña
- marcar fragmentos obligatorios y opcionales
- medir cobertura del script sobre la transcripcion

## Reportes que siguen el patron del sistema

El sistema ya acostumbra reportes con indicadores, general y detalle. Para esta fase se recomienda construir exactamente eso.

## Reporte 1. Indicadores generales

Pantalla tipo dashboard operativo.

Indicadores sugeridos:

- total de llamadas analizadas
- total inbound
- total outbound
- total listas
- total pendientes
- total con error
- porcentaje de procesamiento exitoso
- tiempo promedio de proceso
- promedio de segundos por minuto de audio
- minutos totales de audio procesados
- porcentaje positivo
- porcentaje neutro
- porcentaje negativo
- porcentaje de cumplimiento de protocolo promedio
- top campañas con menor cumplimiento
- top agentes con mayor desviacion de script

## Reporte 2. Detalle inbound

Filtros sugeridos:

- rango de fechas
- campaña
- agente
- estado de analisis
- sentimiento
- nivel de cumplimiento
- regla incumplida

Columnas sugeridas:

- fecha
- agente
- numero
- campaña
- duracion
- estado de analisis
- sentimiento
- score de sentimiento
- score de protocolo
- regla critica fallida
- recomendacion principal
- tiempo de proceso
- segundos por minuto de audio

## Reporte 3. Detalle outbound

Mismos filtros base, agregando si hace falta:

- tipo de speech comercial
- resultado de oferta o cierre comercial

Columnas sugeridas:

- fecha
- agente
- numero
- campaña
- duracion
- sentimiento
- score de protocolo
- apertura correcta
- speech comercial cumplido
- cierre correcto
- recomendacion principal
- tiempo de proceso
- segundos por minuto de audio

## Reporte 4. Detalle de cumplimiento del script

Vista de auditoria fina por llamada.

Debe mostrar:

- secciones del speech
- regla
- resultado
- evidencia en texto
- observacion
- supervisor

## Reporte 5. Coaching y recomendaciones

Vista centrada en agente y supervisor.

Indicadores sugeridos:

- top recomendaciones recurrentes por agente
- top desviaciones por campaña
- mejora semanal del cumplimiento
- agentes con mas llamadas negativas y bajo cumplimiento

## Fases de implementacion recomendadas

### Fase 1. Consolidar base analitica

Objetivo:

- terminar de estabilizar `call_ai_analysis`
- seguir acumulando llamadas reales
- medir rendimiento por modelo y por minuto de audio

Entregables:

- tabla de metricas completa
- cron estable
- modal y columna de estados ya visibles

### Fase 2. Reglas de protocolo minimas por campaña

Objetivo:

- definir reglas simples para SEGTEC 087
- separar inbound y outbound si el speech cambia

Entregables:

- tabla `call_ai_protocol_rule`
- motor de evaluacion por reglas
- resultados por llamada en `call_ai_protocol_result`

### Fase 3. Recomendaciones automaticas

Objetivo:

- generar coaching util, no solo scores

Entregables:

- tabla `call_ai_recommendation`
- reglas de recomendacion por sentimiento y cumplimiento
- priorizacion alta, media, baja

### Fase 4. Reportes operativos

Objetivo:

- crear indicadores generales y detalles inbound/outbound

Entregables:

- reporte general
- detalle inbound
- detalle outbound
- detalle de protocolo
- vista de coaching

### Fase 5. Calibracion con calidad humana

Objetivo:

- comparar IA contra supervisor

Entregables:

- matriz de coincidencia entre cedula manual e IA
- precision por regla
- reglas a corregir o retirar

## Datos que conviene capturar desde ahora

Para que esta fase sea realmente util, conviene guardar ademas:

- script_version
- opening_score
- closing_score
- protocol_score
- critical_fail_count
- recommendation_count
- first_negative_moment_second
- silence_or_overlap_flags si despues se integra audio mas profundo

## Tiempo y esfuerzo estimado

### Bloque A. Reglas de protocolo para SEGTEC 087

- levantamiento del speech real: 1 a 2 dias
- modelado de reglas y tabla: 1 dia
- primer motor de evaluacion: 2 a 3 dias
- validacion con llamadas reales: 2 dias

### Bloque B. Recomendaciones

- catalogo de recomendaciones: 1 dia
- logica de generacion: 1 a 2 dias
- ajustes con supervision: 1 a 2 dias

### Bloque C. Reportes

- indicadores generales: 1 a 2 dias
- detalle inbound: 1 dia
- detalle outbound: 1 dia
- detalle de cumplimiento y coaching: 1 a 2 dias

## Riesgos reales

- La transcripcion local puede introducir errores foneticos, especialmente en nombres propios o ruido.
- El cumplimiento no debe evaluarse solo con string matching exacto.
- No conviene usar el score de sentimiento como sustituto del cumplimiento de protocolo.
- La version del script por campaña debe estar formalizada; si cambia cada semana sin control, el motor se volvera inestable.

## Recomendacion practica inmediata

El siguiente paso correcto no es saltar directo al reporte final.

Lo correcto es:

1. Levantar el script real de SEGTEC 087 inbound y outbound.
2. Convertirlo en reglas medibles por bloque.
3. Correr esas reglas sobre las llamadas ya transcritas.
4. Despues construir los reportes generales y de detalle.

## Siguiente entregable sugerido

El siguiente entregable tecnico deberia ser uno de estos dos:

1. Documento funcional del speech de SEGTEC 087 convertido a reglas medibles.
2. Esquema SQL y motor inicial de `call_ai_protocol_rule` y `call_ai_protocol_result`.

Ese es el punto donde recomendaciones, evaluacion y cumplimiento dejan de ser una idea y se vuelven un sistema operativo real dentro del call center.