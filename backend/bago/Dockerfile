FROM golang:1.21-alpine
WORKDIR /app
COPY go.* ./
RUN go mod download
COPY *.go ./
#COPY localhost.crt ./
#COPY localhost.key ./
COPY ct ./ct/
COPY ct ./forms/
COPY meta ./meta/
COPY mo ./mo/
COPY rutas ./rutas/
COPY util ./util/
COPY vistas ./vistas/
COPY .en* ./
RUN CGO_ENABLED=0 GOOS=linux go build
EXPOSE 8443
CMD ["/app/bago"]
# Ahora también se puede copiar el .env.dist a .env y modificarlo
# éste tiene prioridad sobre las variables de entorno de sistema
