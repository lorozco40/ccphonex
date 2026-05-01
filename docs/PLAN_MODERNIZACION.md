# Plan de modernizacion

## Enfoque

Modernizar por capas y con riesgo controlado, partiendo de este respaldo como baseline estable.

## Fase 0

- Mantener este repositorio como linea base.
- Crear staging fiel al productivo.
- Automatizar respaldos y validaciones smoke.

## Fase 1

- Separar secretos de codigo.
- Normalizar configuracion por entorno.
- Consolidar monitoreo y logs.

## Fase 2

- Encapsular integraciones con Asterisk, ARI, AMI y DB.
- Cubrir flujos criticos con pruebas smoke.

## Fase 3

- Modernizar UX de login, consola y softphone.
- Reducir JS legacy acoplado en zonas criticas.

## Fase 4

- Evaluar upgrade de plataforma y despliegue repetible.
- Consolidar CI/CD y rollback controlado.
