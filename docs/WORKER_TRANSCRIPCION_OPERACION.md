# Operacion del worker de transcripcion

Fecha: 2026-05-30

## Ubicacion del boton Analisis

El boton `Analisis` aparece en los reportes de llamadas inbound y outbound, en su propia columna `Analisis` al mismo nivel que `Grabacion` y `Calidad`.

Flujo actual:

- abrir el modal ya no registra automaticamente la llamada para procesamiento
- si la llamada aun no tiene analisis, el modal muestra el estado `no solicitado`
- desde el mismo modal se usa el boton `Solicitar analisis` para encolarla manualmente
- si ya existe resultado listo o error, se puede usar `Reprocesar`
- `Reprocesar alta calidad` ahora usa perfil `base-clean`
- `Reprocesar maxima calidad` usa perfil `large-v3-clean` solo para casos excepcionales

Condiciones para verlo:

- Cualquier campana con grabacion disponible
- Usuario con perfil `admin`

## Modelo por defecto

Para este servidor el modelo base por defecto es `tiny`, pero ya no se queda siempre en `tiny`.

Motivo:

- en CPU local `base` y `small` elevan mucho el tiempo de proceso si se usan siempre
- `tiny` mantiene buen tiempo para llamadas cortas
- las llamadas largas son las que mas sufren recortes o perdida de contexto con `tiny`

Regla operativa actual:

- si la corrida pide `tiny` y la llamada dura 90 segundos o mas, el worker sube automaticamente a `small`
- si la llamada es mas corta, se conserva `tiny`
- si se solicita `base-clean`, el worker limpia audio con `ffmpeg` antes de transcribir y luego usa `base`
- si se solicita `large-v3-clean`, el worker limpia audio con `ffmpeg` y luego usa `large-v3`
- `large-v3-clean` debe quedar reservado para llamadas criticas o auditorias puntuales por costo de CPU

Se puede forzar otro modelo con `FW_MODEL=base` o `FW_MODEL=small` si despues se decide priorizar calidad sobre tiempo.
Tambien se puede ajustar el escalamiento automatico con:

- `FW_LONG_AUDIO_SECONDS`
- `FW_LONG_AUDIO_MODEL`

## Ejecucion manual

Desde la raiz del repo:

```bash
scripts/run_worker_transcripcion.sh
```

Con limite y modelo explicitos:

```bash
scripts/run_worker_transcripcion.sh 10 tiny
scripts/run_worker_transcripcion.sh 5 small
AI_DB_HOST=localhost AI_DB_USER=aldo AI_DB_PASS=4ss3rt1v3 AI_DB_NAME=assertive ./.venv_transcripcion/bin/python scripts/worker_transcripcion.py --limit 1 --model base-clean
AI_DB_HOST=localhost AI_DB_USER=aldo AI_DB_PASS=4ss3rt1v3 AI_DB_NAME=assertive ./.venv_transcripcion/bin/python scripts/worker_transcripcion.py --limit 1 --model large-v3-clean
```

## Variables usadas

- `AI_DB_HOST`
- `AI_DB_USER`
- `AI_DB_PASS`
- `AI_DB_NAME`
- `FW_MODEL`
- `FW_LIMIT`
- `FW_VENV_DIR`
- `FW_VAD_FILTER`
- `FW_LONG_AUDIO_SECONDS`
- `FW_LONG_AUDIO_MODEL`
- `FW_AUDIO_FILTERS`
- `FW_BEAM_SIZE`
- `FW_BEST_OF`
- `FW_TEMPERATURE`
- `FW_PATIENCE`
- `FW_CONDITION_ON_PREVIOUS_TEXT`
- `FW_INITIAL_PROMPT`

Si no se definen, el runner usa los valores locales del entorno actual de este servidor.

Nota operativa:

- `FW_VAD_FILTER` ahora queda desactivado por defecto para evitar recortes agresivos en llamadas largas.
- Si se quiere volver a probar con VAD, usar `FW_VAD_FILTER=1`.
- `FW_LONG_AUDIO_SECONDS` por defecto vale `90`.
- `FW_LONG_AUDIO_MODEL` por defecto vale `small`.
- `FW_AUDIO_FILTERS` controla la cadena de filtros de `ffmpeg`; por defecto usa `highpass=f=120,lowpass=f=3800,afftdn=nf=-20,volume=1.8`.
- `FW_BEAM_SIZE` por defecto vale `5`.
- `FW_BEST_OF` por defecto vale `5`.
- `FW_TEMPERATURE` por defecto vale `0.0`.
- `FW_PATIENCE` por defecto vale `1.0`.
- `FW_CONDITION_ON_PREVIOUS_TEXT` por defecto vale `1`; si se observan alucinaciones o arrastres entre segmentos se puede probar `0`.
- `FW_INITIAL_PROMPT` permite inyectar una guia corta para llamadas en espanol, por ejemplo vocabulario del cliente o del guion.

## Si la transcripcion sale muy mal

Subir de `tiny` a `small`, `base` o `large-v3` ayuda, pero no corrige por si solo un audio deficiente. En este proyecto ya se comprobo con llamadas reales que parte del problema esta en la fuente de audio.

Causas probables en este entorno:

- grabaciones telefonicas de banda angosta, muy comprimidas o con clipping
- audio muy bajo o saturado
- ruido de fondo, eco o manos libres
- traslape de voces entre agente y cliente
- una de las dos voces llega mas debil o sucia que la otra
- recortes previos por VAD o por silencio mal detectado

Orden recomendado de mejora:

1. Validar el audio original fuera del worker y escuchar si realmente es inteligible.
2. Reprocesar con `base-clean` y, si sigue mal, con `large-v3-clean` solo en muestras criticas.
3. Ajustar `FW_AUDIO_FILTERS` por tipo de audio, no solo dejar la cadena por defecto.
4. Probar decodificacion mas robusta con `FW_BEAM_SIZE`, `FW_BEST_OF`, `FW_PATIENCE` y `FW_CONDITION_ON_PREVIOUS_TEXT`.
5. Inyectar `FW_INITIAL_PROMPT` con nombres de cliente, productos o frases frecuentes del guion.
6. Si el audio sigue deformado, atacar la captura o el origen de la grabacion antes de seguir subiendo modelo.

Ejemplos de pruebas:

```bash
FW_BEAM_SIZE=8 FW_BEST_OF=8 FW_PATIENCE=1.2 scripts/run_worker_transcripcion.sh 1 base-clean
FW_CONDITION_ON_PREVIOUS_TEXT=0 FW_AUDIO_FILTERS='highpass=f=100,lowpass=f=3500,afftdn=nf=-25,volume=2.2' scripts/run_worker_transcripcion.sh 1 base-clean
FW_INITIAL_PROMPT='Llamada de call center en espanol de Mexico. Terminos frecuentes: SEGTEC, poliza, servicio, folio, confirmacion.' scripts/run_worker_transcripcion.sh 1 large-v3-clean
```

## Limite actual conocido

En pruebas reales ya hubo mejora al pasar de `tiny` a `base`, pero la salida siguio incompleta o deformada. Eso confirma que hoy el cuello de botella principal no es solo Whisper; tambien lo es la calidad del audio fuente.

## Cron recomendado

Ejemplo para correr cada 5 minutos:

```cron
*/5 * * * * cd /tmp/ccphonex_repo_clean && /tmp/ccphonex_repo_clean/scripts/run_worker_transcripcion.sh 5 tiny >> /var/log/worker_transcripcion.log 2>&1
```

## Preparacion del entorno Python

Secuencia validada en este servidor:

```bash
apt-get install -y python3.8-venv
apt-get install -y ffmpeg
cd /tmp/ccphonex_repo_clean
python3 -m venv .venv_transcripcion
. .venv_transcripcion/bin/activate
pip install --upgrade pip setuptools wheel
pip install --only-binary=:all: tokenizers==0.20.3 ctranslate2==4.5.0 huggingface-hub==0.36.2 av==12.3.0 tqdm==4.67.1 pyyaml==6.0.2 filelock==3.16.1 fsspec==2025.3.0 typing-extensions==4.13.2 packaging==24.2 requests==2.32.3
pip install --only-binary=:all: onnxruntime==1.19.2 pymysql==1.1.2
pip install --no-deps faster-whisper==1.1.0
```

## Validacion real realizada

- Llamada outbound real `17843909`
- Estado final: `listo`
- Modelo usado: `tiny`
- Resultado persistido en `call_ai_analysis`

Validaciones adicionales:

- `ffmpeg` y `ffprobe` instalados y funcionales en `/usr/bin`
- el helper de limpieza genero correctamente un WAV temporal limpio sobre una llamada real
- llamada inbound `17847857` reprocesada con `base`; mejoro de `1291` a `1447` caracteres, pero siguio con texto deformado, lo que apunta a limite de audio ademas de modelo

## Metricas de rendimiento guardadas

La tabla `call_ai_analysis` ya contempla metricas operativas para futuros indicadores:

- `processing_started_at`
- `processing_duration_seconds`
- `transcription_seconds_per_minute`

Con esto ya se puede medir cuanto tarda el motor local por minuto de audio y construir indicadores generales y de detalle para inbound y outbound.

## Reportes disponibles

Se habilitaron estos reportes bajo el submenu `Analisis de llamadas`:

- `reportes/analisis_general`
- `reportes/analisis_inbound`
- `reportes/analisis_outbound`
- `reportes/protocolo_general`
- `reportes/protocolo_detalle`

Todos usan la misma vista generica de reportes, con filtros por fecha, campaña y agente; adicionalmente los reportes IA incluyen filtros por estado y sentimiento.