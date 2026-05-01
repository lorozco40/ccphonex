package main

import (
    "bufio"
    "log"
    "net/http"
    "os"
    "strings"

    "phonex/bago/ct"
    "phonex/bago/mo"
    "phonex/bago/rutas"
    "phonex/bago/util"

    _ "github.com/go-sql-driver/mysql"
)

func main() {
    loadEnv()        // Carga las variables de entorno del archivo .env y las default
    mo.Initdbl()     // Inicializa la conexión a la base de datos
    ct.GetLicencia() // Trae las licencias de liser o de token

    go ct.Cron() // Procesos permanentes que se ejecutan cada minuto

    rou := rutas.NewRouter()
    if os.Getenv("USETLS") == "si" {
        log.Printf("HTTPS Escuchando en puerto %s", os.Getenv("PRT"))
        log.Fatal(http.ListenAndServeTLS(":"+os.Getenv("PRT"), os.Getenv("CRT"), os.Getenv("KEY"), rou))
    } else {
        log.Printf("HTTP Escuchando en puerto %s", os.Getenv("PRT"))
        log.Fatal(http.ListenAndServe(":"+os.Getenv("PRT"), rou))
    }
}

func loadEnv() {
    setDefaultEnvs()
    // Abrir archivo .env para sobreescribir las variables por defecto
    file, err := os.Open(".env")
    util.CheckErr(err)
    defer file.Close()
    // Escanear el archivo línea por línea
    scanner := bufio.NewScanner(file)
    for scanner.Scan() {
        line := scanner.Text()
        line = strings.TrimSpace(line)
        // Ignorar líneas que inicien con "#" o lineas en blanco
        if line == "" || strings.HasPrefix(line, "#") {
            continue
        }
        // Dividir cada línea por el signo "=" para obtener la variable y su valor
        parts := strings.SplitN(line, "=", 2)
        if len(parts) == 2 {
            key := strings.TrimSpace(parts[0])
            value := strings.TrimSpace(parts[1])
            os.Setenv(key, value)
        }
    }
}

func setDefaultEnvs() {
    os.Setenv("APPPATH", "/root/bago/")
    os.Setenv("WEBDIR", "/var/www/html/")
    os.Setenv("VCDOMAIN", "meet.assertivebusiness.mx")
    os.Setenv("VCAPPID", "Un1d3n71f1c4d0rMuyCh1d0")
    os.Setenv("JWTKEY", "Ah0r451gu3371d3n7i8460")
    os.Setenv("USETLS", "si")                // Despachar contenido por https o http
    os.Setenv("DBU", "aldo")                 // Usuario de la base de datos
    os.Setenv("DBP", "4ss3rt1v3")            // Password de la base de datos
    os.Setenv("DBH", "localhost")            // Host de la base de datos
    os.Setenv("DBB", "assertive")            // Nombre de la base de datos
    os.Setenv("CRT", "/root/bago/ser34.crt") // Certificado ssl
    os.Setenv("KEY", "/root/bago/ser34.key") // Llave ssl
    os.Setenv("LIS", "10.10.2.101:8442")     // servidor de donde se traerá la licencia, liser
    os.Setenv("PRT", "8443")                 // Puerto primario de escucha de bago
    os.Setenv("ENV", "prod")                 // Environment, default prod, cambiable de dev (producción, desarrollo)
    os.Setenv("BFURL", "https://localhost/") // Basic frontend url
    os.Setenv("LICFILE", "/root/bago/licencia.json")
    os.Setenv("LICMODE", "remote")
    os.Setenv("LICUSERS", "9999")
    os.Setenv("LICDAYS", "365")
}
