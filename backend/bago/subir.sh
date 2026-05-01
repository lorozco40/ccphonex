#!/bin/bash

#git checkout master
#git push origin master

rm -f bago
go build

for serv in "$@"
do
    printf "Subiendo bago a servidor % \n" $serv
    scp bago root@10.10.2.$serv:bagos
    printf "Cambiando bago en servidor % \n\n" $serv
    ssh root@10.10.2.$serv /root/pasabago.sh
done
