# AI Assist Service (shadow mode)

Microservicio IA desacoplado para ccphonex. Está diseñado para operar en paralelo y no bloquear flujos del contact center.

## Endpoints

- `GET /health`
- `POST /v1/summarize`
- `POST /v1/recommendations`

## Ejecutar local

```bash
cd /tmp/workspace/lorozco40/ccphonex/backend/ai-assist
python3 -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
uvicorn main:app --host 0.0.0.0 --port 8090
```

## Integración con `backend/sivna`

Variables de entorno:

- `AI_ENABLED` (default: `false`)
- `AI_SHADOW_MODE` (default: `true`)
- `AI_SERVICE_URL` (default: `http://127.0.0.1:8090`)
- `AI_TIMEOUT_MS` (default: `1200`)

Cuando está activado, `sivna/chati.js` envía eventos de chat al servicio IA de forma asíncrona. Si falla IA, el chat sigue operando normalmente.
