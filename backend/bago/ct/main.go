package ct

import (
    "bytes"
    "crypto/tls"
    "encoding/json"
    "fmt"
    "io"
    "log"
    "net"
    "net/http"
    "os"
    "strconv"
    "strings"
    "time"

    "phonex/bago/forms"
    "phonex/bago/mo"
    "phonex/bago/util"

    "github.com/julienschmidt/httprouter"
)

var licencia Licencia
var licencias Licencias

// Licencia es el modelo para la tabla licencias
type Licencia struct {
    ID             int       `json:"id"`
    IdCampaign     int       `json:"id_campaign"`
    Expira         time.Time `json:"expira"`
    Tipo           string    `json:"tipo"`
    Usuarios       int       `json:"usuarios"`
    Cadena         string    `json:"cadena"`
    Cliente        string    `json:"cliente"`
    DominioPublico string    `json:"dominio_publico"`
    IPPrivada      string    `json:"ip_privada"`
    Error          string    `json:"error"`
}

// LicenciaShow acción de la ruta "Licencia"
func LicenciaShow(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
    util.RespondJSON(w, 200, licencia)
}

func GetLicencia() {
    traeThisIP()
    lic := os.Getenv("LIC")
    if lic != "" && len(lic) >= 40 {
        if parsed, err := licenciaDesdeCadena(lic); err == nil {
            licencia = parsed
            return
        }
    }

    licencia.Error = ""
    if remote, err := fetchRemoteLicencia(); err == nil && remote.ID != 0 {
        licencia = remote
        return
    } else if err != nil {
        log.Printf("licencia remota no disponible: %v", err)
    }

    if local, err := loadLicenciaLocal(); err == nil && local.ID != 0 {
        licencia = local
        return
    } else if err != nil {
        log.Printf("licencia local no disponible: %v", err)
    }

    if contingenciaActiva() {
        licencia = licenciaContingencia()
        return
    }

    licencia = Licencia{}
    licencia.Error = "No fue posible validar la licencia: remoto y respaldo local no disponibles"
}

func licenciaDesdeCadena(lic string) (Licencia, error) {
    d, err := time.Parse("2006-01-02", "20"+lic[38:39]+lic[7:8]+"-"+lic[29:30]+lic[18:19]+"-"+lic[23:24]+lic[28:29])
    if err != nil {
        return Licencia{}, err
    }
    usuarios, err := strconv.Atoi(lic[9:10] + lic[16:17])
    if err != nil {
        return Licencia{}, err
    }
    parsed := Licencia{
        ID:       1,
        Expira:   d,
        Tipo:     lic[11:12] + lic[3:4],
        Usuarios: usuarios,
        Cadena:   lic,
    }
    fmt.Println(parsed)
    return parsed, nil
}

func fetchRemoteLicencia() (Licencia, error) {
    reqBody, err := json.Marshal(map[string]string{"ip": os.Getenv("SIP"), "mac": os.Getenv("SMAC")})
    if err != nil {
        return Licencia{}, err
    }
    transCfg := &http.Transport{
        TLSClientConfig: &tls.Config{InsecureSkipVerify: true},
    }
    client := &http.Client{Transport: transCfg, Timeout: 10 * time.Second}
    resp, err := client.Post("https://"+os.Getenv("LIS")+"/", "application/json", bytes.NewBuffer(reqBody))
    if err != nil {
        return Licencia{}, err
    }
    defer resp.Body.Close()
    if resp.StatusCode >= 400 {
        return Licencia{}, fmt.Errorf("respuesta remota inesperada: %s", resp.Status)
    }
    body, err := io.ReadAll(resp.Body)
    if err != nil {
        return Licencia{}, err
    }
    var remote Licencia
    if err := json.Unmarshal(body, &remote); err != nil {
        return Licencia{}, err
    }
    if remote.ID == 0 {
        return Licencia{}, fmt.Errorf("respuesta remota sin licencia válida")
    }
    return remote, nil
}

func loadLicenciaLocal() (Licencia, error) {
    path := strings.TrimSpace(os.Getenv("LICFILE"))
    if path == "" {
        return Licencia{}, fmt.Errorf("LICFILE no configurado")
    }
    body, err := os.ReadFile(path)
    if err != nil {
        return Licencia{}, err
    }
    var local Licencia
    if err := json.Unmarshal(body, &local); err != nil {
        return Licencia{}, err
    }
    if local.ID == 0 {
        local.ID = 1
    }
    if local.Cadena == "" {
        local.Cadena = "local-file"
    }
    return local, nil
}

func contingenciaActiva() bool {
    switch strings.ToLower(strings.TrimSpace(os.Getenv("LICMODE"))) {
    case "contingencia", "failopen", "offline":
        return true
    default:
        return false
    }
}

func licenciaContingencia() Licencia {
    usuarios, err := strconv.Atoi(strings.TrimSpace(os.Getenv("LICUSERS")))
    if err != nil || usuarios <= 0 {
        usuarios = 9999
    }
    dias, err := strconv.Atoi(strings.TrimSpace(os.Getenv("LICDAYS")))
    if err != nil || dias <= 0 {
        dias = 365
    }
    return Licencia{
        ID:       1,
        Expira:   time.Now().AddDate(0, 0, dias),
        Tipo:     "CT",
        Usuarios: usuarios,
        Cadena:   "contingencia-local",
        Cliente:  "contingencia",
        Error:    "",
    }
}

// traeThisIP() averigua la ip privada de ésta máquina
func traeThisIP() {
    ifaces, err := net.Interfaces()
    if err == nil {
        for _, i := range ifaces {
            a := i.HardwareAddr.String()
            b := i.Name
            if a != "" && (strings.HasPrefix(b, "en") || strings.HasPrefix(b, "wl")) {
                os.Setenv("SMAC", a)
                addrs, _ := i.Addrs()
                for _, addr := range addrs {
                    switch v := addr.(type) {
                    case *net.IPNet:
                        if v.IP.To4() != nil {
                            os.Setenv("SIP", v.IP.String())
                        }
                    case *net.IPAddr:
                        if v.IP.To4() != nil {
                            os.Setenv("SIP", v.IP.String())
                        }
                    }
                }
            }
        }
    }
}

// RecibeLicencia para recibir un vencimiento o actualización manual de licencia desde LISER
func RecibeLicencia(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
    userData, err := forms.Parse(r)
    util.CheckErr(err)
    val := userData.Validator()
    val.Require("cadena")
    val.Require("expira")
    val.Require("usuarios")

    if val.HasErrors() {
        util.RespondError(w, 400, "Información incorrecta")
        return
    }
    cadena := userData.Get("cadena")
    expira := userData.Get("expira")
    usuarios := userData.Get("usuarios")

    //Recorremos todas las licencias hasta encontrar aquella que coincida en cadena para aplicar los cambios a esa licencia de campana
    encuentra := false
    for i, l := range licencias.Licencias {
        if l.Cadena == cadena {
            layout := "2006-01-02 15:04:05.000000"
            licencias.Licencias[i].Expira, _ = time.Parse(layout, expira+" 23:59:59.000000")
            licencias.Licencias[i].Usuarios, _ = strconv.Atoi(usuarios)
            encuentra = true
        }
    }
    if !encuentra { //En caso de no encontrar, puede deberse a que la licencia sea nueva, preguntamos a liser y consultamos de nuevo
        //Geticencias de nuevo
        GetLicencias()
        for i, l := range licencias.Licencias {
            if l.Cadena == cadena {
                layout := "2006-01-02 15:04:05.000000"
                licencias.Licencias[i].Expira, _ = time.Parse(layout, expira+" 23:59:59.000000")
                licencias.Licencias[i].Usuarios, _ = strconv.Atoi(usuarios)
                encuentra = true
            }
        }
    }

    if encuentra {
        UpdateDBClient()
        util.RespondJSON(w, 200, licencias)
    } else {
        util.RespondError(w, 401, "Sin permiso")
        return
    }
}

/*Nuevas funcionalidades*/

// LicenciaShow acción de la ruta "Licencia"
func LicenciasShow(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
    util.RespondJSON(w, 200, licencias)
}

type Licencias struct {
    Licencias []Licencia `json:"licencias"`
    Error     string     `json:"error"`
}

type CampaignData struct {
    ID         int64  `json:"id"`
    IdCampaign int64  `json:"id_campaign"`
    Atributo   string `json:"atributo"`
    Valor      string `json:"valor"`
}

type Campana struct {
    ID   int64  `json:"id"`
    Name string `json:"name"`
}

func ValidaLicencia() Licencia {
    expira := licencia.Expira.In(mo.Local)
    now := time.Now().In(mo.Local)
    diff := expira.Sub(now)
    if diff.Seconds() < 0 || licencia.ID == 0 {
        GetLicencia()
        expira = licencia.Expira.In(mo.Local)
        now = time.Now().In(mo.Local)
        diff = expira.Sub(now)
        if diff.Seconds() < 0 || licencia.ID == 0 {
            if licencia.Error == "" {
                licencia.Error = "Licencia vencida o no disponible"
            }
        }
    }
    return licencia
}

func GetLicencias() Licencias {
    var err error
    licencias.Error = ""

    /* Licencias solicitadas */
    var campanas []Campana
    mo.Dbl.Raw("SELECT id, name FROM campaign WHERE active=1").Scan(&campanas)

    //PREPARAMOS ARRAY CON LAS LICENCIAS A SOLICITAR EN LISER
    datos := map[string]interface{}{
        "ip":       os.Getenv("SIP"),
        "mac":      os.Getenv("SMAC"),
        "campanas": campanas,
    }
    reqBody, err := json.Marshal(datos)
    if err != nil {
        log.Println(err)
    }
    transCfg := &http.Transport{
        TLSClientConfig: &tls.Config{InsecureSkipVerify: true}, // ignore expired SSL certificates
    }

    client := &http.Client{Transport: transCfg}
    resp, err := client.Post("https://"+os.Getenv("LIS")+"/", "application/json", bytes.NewBuffer(reqBody))
    if err != nil {
        log.Println(err)
        licencias.Error = "Error de comunicación con liser"
        return licencias
    }
    defer resp.Body.Close()
    body, err := io.ReadAll(resp.Body)
    if err != nil {
        log.Println(err)
        licencias.Error = "Error en la respuesta de liser"
        return licencias
    }
    json.Unmarshal(body, &licencias.Licencias)
    if licencias.Licencias == nil {
        licencias.Error = "Sin licencias disponibles"
    }
    UpdateDBClient()

    return licencias
}

// Actualizamos la BD cliente: assertive
func UpdateDBClient() Licencias {
    var n int64
    for _, l := range licencias.Licencias {
        //La clave unica es id_campaign + atributo + sub
        //Licencia
        query := mo.Dbl
        query = query.Table("campaign_data")
        query = query.Select("id", "id_campaign", "atributo", "valor")
        query = query.Where("id_campaign = ?", l.IdCampaign).Where("atributo = ?", "licencias")
        query = query.Limit(1)
        query.Count(&n)
        if n == 0 {
            mo.Dbl.Exec("INSERT INTO campaign_data (atributo, id_campaign, valor, orden, sub) VALUES ('licencias',?,?,0,'')", l.IdCampaign, l.Usuarios)
        } else {
            mo.Dbl.Exec("UPDATE campaign_data SET valor = ? WHERE atributo = 'licencias' AND id_campaign = ?", l.Usuarios, l.IdCampaign)
        }
        //Expiracion
        query = mo.Dbl
        query = query.Table("campaign_data")
        query = query.Select("id", "id_campaign", "atributo", "valor")
        query = query.Where("id_campaign = ?", l.IdCampaign).Where("atributo = ?", "expira")
        query = query.Limit(1)
        query.Count(&n)
        if n == 0 {
            mo.Dbl.Exec("INSERT INTO campaign_data (atributo, id_campaign, valor, orden, sub) VALUES ('expira',?,?,0,'')", l.IdCampaign, l.Expira)
        } else {
            mo.Dbl.Exec("UPDATE campaign_data SET valor = ? WHERE atributo = 'expira' AND id_campaign = ?", l.Expira, l.IdCampaign)
        }
    }

    return licencias
}
