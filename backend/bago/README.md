# README #

### Backend Golang Assertive ###

Aplicación RESTfull que valida licencias, entrega registros e información en general de assertive

### Ejecutable ###

Genera un solo archivo ejecutable, utiliza todos los siguientes parámetros:

* dbh : Data Base host
* dbu : Data Base user
* dbp : Data Base password
* dbb : Data Base nombre de la base de datos
* crt : Certificado para uso de SSL
* key : Llave ligada al certificado SSL
* cor : Cross origin permitido (assertive.phonex-servicios.com, 10.10.2.102)

ejecución inicial antes de ser un servicio:

./bagos -parametro=valor

cada parámetro tiene un valor default ejemplo dbb es assertive, si así se llama la base de datos se puede omitir su declaración


#---- Compilar para otro sistema operativo diferente al anfitrión ---#

env GOOS=linux GOARCH=amd64 go build -o bagos .
