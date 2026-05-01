package mo

import (
	"encoding/json"
	"time"
)

// WhatsappContact tabla contactos
type WhatsappContact struct {
	ID               uint      `json:"id"`
	IdWacta          uint      `json:"id_wacta"`
	Account          string    `json:"account"`
	Name             string    `json:"name"`
	Nick             string    `json:"nick"`
	Description      string    `json:"description"`
	DatetimeRegister time.Time `json:"datetime_register"`
	LastAsignedTo    *uint     `json:"last_asigned_to"`
}

// WhatsappCuentas tabla cuentas
type WhatsappCuentas struct {
	ID          uint      `json:"id"`
	IdCamapaign uint      `json:"id_campaign"`
	IdExtapi    uint      `json:"id_extapi"`
	Cuenta      string    `json:"cuenta"`
	Nombre      string    `json:"nombre"`
	Almacen     string    `json:"almacen"`
	Active      bool      `json:"active"`
	AltaQuien   int       `json:"alta_quien"`
	AltaCuando  time.Time `json:"alta_cuando"`
	Campana     Campaign  `json:"campana" gorm:"ForeignKey:IdCampaign"`
	Extapi      Extapi    `json:"extapi" gorm:"ForeignKey:IdExtapi"`
}

// WhatsappEntry tabla mensajes
type WhatsappEntry struct {
	ID               uint            `json:"id"`
	IdContact        uint            `json:"id_contact"`
	IdUser           *uint           `json:"id_user"`
	IdSession        *uint           `json:"id_session"`
	IdWacta          uint            `json:"id_wacta"`
	JSON             json.RawMessage `json:"json"`
	Message          string          `json:"message"`
	DatetimeReceived time.Time       `json:"datetime_received"`
	Type             string          `json:"type"`
	Status           string          `json:"status"`
	Watype           string          `json:"watype"`
	Caption          string          `json:"caption"`
	Mimetype         string          `json:"mimetype"`
	Size             int             `json:"size"`
	Duration         int             `json:"duration"`
	Lat              float64         `json:"lat"`
	Lng              float64         `json:"lng"`
	Thumb            string          `json:"thumb"`
	URL              string          `json:"url"`
}

// WhatsappGateway tabla mensajes para pasar a otros servidores
type WhatsappGateway struct {
	ID       uint
	Queviene json.RawMessage
	DestTel  string
	DestIP   string
	DestResp string
	Hora     time.Time
}

// WhatsappHooks tabla eventos desde wabox
type WhatsappHooks struct {
	ID               uint            `json:"id"`
	JSON             json.RawMessage `json:"json"`
	DatetimeReceived time.Time       `json:"datetime_received"`
}

// WhatsappServe tabla servidores para gateway
type WhatsappServe struct {
	ID      uint   `json:"id"`
	Numero  string `json:"numero"`
	IP      string `json:"ip"`
	Detalle string `json:"detalle"`
	Activo  bool   `json:"activo"`
}

// WhatsappSession tabla sesiones
type WhatsappSession struct {
	ID               uint      `json:"id"`
	IdContact        uint      `json:"id_contact"`
	IdUser           uint      `json:"id_user"`
	IdWacta          uint      `json:"id_wacta"`
	IdUserTransfer   *uint     `json:"id_user_transfer"`
	DatetimeAssigned time.Time `json:"datetime_assigned"`
	DatetimeStart    time.Time `json:"datetime_start"`
	DatetimeEnd      time.Time `json:"datetime_end"`
	DurationWait     int       `json:"duration_wait"`
	Duration         int       `json:"duration"`
	Type             string    `json:"type"`
	Message          string    `json:"message"`
}

// type WaCtas []WhatsappCuentas

// WaCtaLista GET whatsapp/cuenta devuelve una lista paginada de cuentas de Whatsapp
// func WaCtaLista(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
//     userData, err := forms.Parse(r)
//     CheckErr(err)
//     val := userData.Validator()
//     val.Require("pag")
//     val.Require("uid")
//     if val.HasErrors() {
//         log.Println(val.Messages())
//         respondError(w, 400, "Datos incorrectos o incompletos")
//         return
//     }
//     uid := userData.Get("uid")
//     var udata UserDatas
//     udata = GetUserData(uid)
//     var campanas string
//     for i := range udata {
//         if udata[i].Cat == "userData" && udata[i].Eti == "campanas" {
//             campanas = udata[i].Val
//             if campanas == "" {
//                 log.Println(uid + ": Solicita lista email y no tiene campañas asignadas")
//                 respondError(w, 400, "No tienes campañas asignadas")
//                 return
//             }
//             break
//         }
//     }
//     var pagina EmailAccountsPag
//     pagina.Pag, _ = strconv.Atoi(userData.Get("pag"))
//     pagina.Lim = 20
//     if _, ok := r.URL.Query()["lim"]; ok {
//         pagina.Lim, _ = strconv.Atoi(userData.Get("lim"))
//     }
//     result := Dbl.Where("id_campaign in (?)", campanas).Find(&pagina.Data)
//     pagina.Regs = result.RowsAffected
//     Dbl.Where("id_campaign in (?)", campanas).Limit(pagina.Lim).Offset(pagina.Pag).Find(&pagina.Data)
//     respondJSON(w, 200, pagina)
// }

// GetWaSesByContact encuentra una sesion de whatsapp abierta por medio del id del contacto
func GetWaSesByContact(idc, idw uint) WhatsappSession {
	var res WhatsappSession
	Dbl.Where("id_contact = ? and datetime_end is null", idc).Find(&res)
	if res.ID == 0 {
		usr := GetNextWaUser(idw)
		if usr.ID > 0 {
			res.IdContact = uint(idc)
			res.IdUser = usr.ID
			res.DatetimeStart = time.Now().In(Local)
			Dbl.Save(&res)
		}
	}
	return res
}

// GetNextWaUser busca el siguiente agente para asignar wa, conectado
func GetNextWaUser(idw uint) User {
	var u User
	return u
}
