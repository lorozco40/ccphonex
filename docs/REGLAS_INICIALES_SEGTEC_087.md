# Reglas iniciales SEGTEC 087

Fecha: 2026-05-30
Version inicial: segtec087-v1

## Alcance

Estas reglas son la primera semilla operativa para medir cumplimiento de protocolo sobre llamadas ya transcritas en `call_ai_analysis`.

Cobertura inicial:

- campaña `SEGTEC 087`
- llamadas `inbound` y `outbound`
- evaluacion heuristica simple basada en texto transcrito
- modo `contains_any`

## Contexto outbound relevante

En este entorno el prefijo de marcacion manual no se decide en el reporte.
Se decide en el dialplan de Asterisk.

Para el contexto `segtec`:

- extensiones asociadas: `1050-1065`, `1066-1076`
- selector confirmado para `SEGTEC 087`: `8`

Eso significa que una llamada outbound de SEGTEC 087 normalmente nace marcando `8` mas los 10 digitos destino en el contexto correspondiente.
Ese selector sirve para clasificacion operativa de campaña, pero no se evalua dentro del protocolo de audio; el protocolo se mide sobre lo dicho por el agente durante la llamada.

## Reglas semilla cargadas en SQL

### Reglas comunes

1. `saludo_inicial`
   - grupo: `apertura`
   - obligatorio: si
   - peso: `1.50`
   - terminos esperados: `buenos dias`, `buenas tardes`, `buenas noches`, `hola`

2. `identificacion_empresa`
   - grupo: `apertura`
   - obligatorio: si
   - peso: `1.50`
   - terminos esperados: `segtec`, `phonex`, `le habla`, `mi nombre es`, `habla`

3. `confirmacion_datos_cliente`
   - grupo: `validacion`
   - obligatorio: si
   - peso: `1.75`
   - terminos esperados: `numero de serie`, `folio`, `domicilio`, `equipo`, `nombre del cliente`, `titular`, `telefono de contacto`

4. `empatia_basica`
   - grupo: `gestion`
   - obligatorio: no
   - peso: `1.00`
   - terminos esperados: `con gusto`, `permitame`, `le apoyo`, `le ayudo`, `una disculpa`, `entiendo`

5. `confirmacion_siguiente_paso`
   - grupo: `cierre`
   - obligatorio: si
   - peso: `1.50`
   - terminos esperados: `se genera reporte`, `queda registrado`, `le contactaran`, `siguiente paso`, `se canaliza`, `se agenda`, `se da seguimiento`

6. `despedida_cordial`
   - grupo: `cierre`
   - obligatorio: si
   - peso: `1.25`
   - terminos esperados: `gracias`, `excelente dia`, `buen dia`, `buena tarde`, `hasta luego`, `estamos para servirle`

### Regla inbound

7. `confirmacion_motivo_contacto`
   - grupo: `validacion`
   - obligatorio: si
   - peso: `1.25`
   - terminos esperados: `en que le puedo ayudar`, `motivo de su llamada`, `en que le apoyo`, `cual es su reporte`, `cual es su problema`

### Regla outbound

8. `explicacion_motivo_llamada`
   - grupo: `apertura`
   - obligatorio: si
   - peso: `1.50`
   - terminos esperados: `le llamo`, `motivo de la llamada`, `me comunico`, `le contacto`, `le marco`

## Limitaciones actuales

- estas reglas no sustituyen una cedula formal de calidad
- aun no existe el motor que las calcule automaticamente sobre cada transcripcion
- los falsos negativos son esperables porque la transcripcion local puede deformar nombres y frases cortas
- todavia no hay reglas negativas ni semanticas; esta version es solo de arranque

## Uso inmediato

La estructura ya quedo preparada para el siguiente paso tecnico:

1. leer llamadas `listo` desde `call_ai_analysis`
2. evaluar estas reglas y guardar resultados en `call_ai_protocol_result`
3. consolidar puntajes en `call_ai_score`
4. exponer coaching en `call_ai_recommendation`

## Reporte disponible

Se habilito la ruta `reportes/protocolo_general` para ver el resumen operativo de cumplimiento IA.

Mientras aun no exista motor de evaluacion, el reporte mostrara volumen de llamadas analizadas y columnas de cumplimiento en cero o `NULL`.