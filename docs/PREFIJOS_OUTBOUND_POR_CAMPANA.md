# Prefijos outbound por campana

Fecha de levantamiento: 2026-05-20
Fuente principal: telephony/etc/asterisk/extensions_custom.conf y pjsip.endpoint.conf

## Como funciona

Para llamadas manuales, el prefijo que marca el ejecutivo no se decide en el reporte.
Se decide en el dialplan custom de Asterisk. En los contextos con selector, el usuario marca 11 digitos:
- el primer digito selecciona la etiqueta o subcampana
- el sistema recorta ese primer digito con ${EXTEN:1}
- la llamada sale con los 10 digitos restantes
- luego call_entry y rep_outbound reflejan esa clasificacion por campaign/name o por el contexto aplicado

El atributo prefijo en campaign_data existe en la app, pero en este entorno se usa para flujos de despachador/carga de bases, no como catalogo general de marcacion manual.

## Chuleta operativa

### Contexto segtec

Extensiones asociadas:
1050-1065, 1066-1076

Prefijos:
- 5 -> SEGTEC 085
- 6 -> SEGTEC 016
- 7 -> SEGTEC 079
- 8 -> SEGTEC 087

### Contexto Provetecnia

Extensiones asociadas:
1200-1216, 1411

Prefijos:
- 1 -> Provetecnia
- 2 -> Otros clientes
- 3 -> Ingenieria operativa
- 4 -> Provetecnia 4
- 5 -> SEGTEC 085
- 6 -> Provetecnia cargo
- 7 -> SEGTEC 016
- 8 -> IOSA RX

### Contexto assertivec

Extensiones asociadas:
1117, 1118, 5000, 5001, 5002, 5003, 5004

Prefijos:
- 7 -> Assertive Ventas
- 8 -> Phonex Ventas

### Contexto LaSalle

Extensiones asociadas:
1023, 1300-1322, 1324-1328, 5096

Prefijos:
- 8 -> Universidad Hebraica
- 9 -> Universidad Iberoamericana

### Contexto sinergis

Extensiones asociadas:
1122, 1123, 1124, 1125, 1126, 1127

Prefijos:
- 3 -> Sinergis Brasil

### Contexto vibe

No se encontraron extensiones asignadas a este contexto en pjsip.endpoint.conf, pero el dialplan define:
- 6 -> Vibe
- 7 -> Vibe
- 8 -> Vibe
- 9 -> Vibe

## Contextos sin prefijo selector claro

Varios clientes usan patrones de 10 digitos sin selector inicial visible en el dialplan. En esos casos no hay evidencia de un prefijo manual de campana similar a SEGTEC.
Ejemplos observados: carlzeiss, toyota, cintegra, bumeran, admsalud, ingenieria-financiera, kyocera, inmuebles24, comercial, clarodirecto.

## Archivos clave

- telephony/etc/asterisk/extensions_custom.conf
- telephony/etc/asterisk/pjsip.endpoint.conf
- webroot/application/models/Datos_model.php
- webroot/application/models/Repoback_model.php
- webroot/application/models/Reportes_model.php

## Notas de soporte

- Si una llamada saliente aparece con un numero iniciando en 8, 7, 6, 5 o 9, primero revisar el contexto de la extension que marco.
- Para SEGTEC 087, el selector confirmado es 8.
- El reporte outbound no decide el prefijo; solo muestra la campana ya clasificada desde call_entry/rep_outbound.
- Si se requiere un catalogo unico por campana para marcacion manual, hoy no esta centralizado en la app y habria que modelarlo o depender del dialplan.
