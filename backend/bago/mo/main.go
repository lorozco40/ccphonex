package mo

import (
	"fmt"
	"log"
	"os"
	"time"

	"phonex/bago/util"

	"gorm.io/driver/mysql"
	"gorm.io/gorm"
	"gorm.io/gorm/schema"
)

// Dbl es la conexión a la base de datos
var Dbl *gorm.DB

// Local es la zona horaria, default: Mexico_City
var Local *time.Location

// LoggedUsers es un mapa de usuarios conectados
var LoggedUsers = make(map[uint]LogedUser)

type LogedUser struct {
	UserConPer
	SesKey  string
	LastReq time.Time
	IP      string
	Ini     time.Time
	For     int // Tiempo que durará la sesión abierta
}

func init() {
	var err error
	Local, err = time.LoadLocation("America/Mexico_City")
	util.CheckErr(err)
}

// Initdbl inicia la conexión a la base de datos
func Initdbl() {
	db, err := gorm.Open(mysql.Open(os.Getenv("DBU")+":"+os.Getenv("DBP")+"@("+os.Getenv("DBH")+
		")/"+os.Getenv("DBB")+"?charset=utf8&parseTime=True&loc=Local&timeout=10s"), &gorm.Config{
		NamingStrategy: schema.NamingStrategy{
			SingularTable: true,
		},
	})
	if err != nil {
		log.Printf("!Error conectando a la base de datos: %+v\n", err)
	} else {
		fmt.Println("Conectado db principal " + os.Getenv("DBB") + " en " + os.Getenv("DBH"))
	}
	if os.Getenv("ENV") == "dev" {
		Dbl = db.Debug()
	} else {
		Dbl = db
	}
	DoMigration()
	// genModelos()
}

// DoMigration para actualizar toda la base de datos con los campos faltantes
func DoMigration() {
	Dbl.AutoMigrate(
		&ApiConEp{},
		&BannedIps{},
		&BagoLog{},
		&Campaign{},
		&Catalogs{},
		&ChatInstance{},
		&ChatInstanceDefs{},
		&ChatSession{},
		&ChatEntry{},
		&ChatinternoEntry{},
		&EmailAccount{},
		&EmailEntry{},
		&EmailTransfer{},
		&Endpoint{},
		&EpParam{},
		&Extapi{},
		&Form{},
		&FormFields{},
		&FormApi{},
		&MetaMsgrHook{},
		&Permission{},
		&RepInbound{},
		&RepOutbound{},
		&SmsEntry{},
		&User{},
		&UserTrans{},
		&UserTransOpts{},
	)
}

type FilterParams struct {
	Uid        string      // User ID
	Cid        string      // Campaign ID
	IgnoreCams bool        // Ignorar campañas, usar ÚNICAMENTE en tablas que no tengan campo id_campaign
	Model      interface{} // Modelo si no hay modelo especificar tabla y viceversa
	Table      string      // Tabla
	Campos     string      // Campos, si se especifican el target será un []map[string]interface{}
	Joins      []string    // Joins
	Target     interface{} // Slice guardar resultados
	CampoFecha string      // Campo de fecha
	DateFr     string      // Fecha desde
	DateTo     string      // Fecha hasta
	Page       string      // Página
	Rpp        string      // Registros por página
	Other      string      // Otro campo
	OVal       string      // Valor otro campo
	Ofa        string      // Otro campo A
	Ova        string      // Valor otro campo A
	Ofb        string      // Otro campo B
	Ovb        string      // Valor otro campo B
	Ofc        string      // Otro campo C
	Ovc        string      // Valor otro campo C
	Ofd        string      // Otro campo D
	Ovd        string      // Valor otro campo D
	Ofe        string      // Otro campo E
	Ove        string      // Valor otro campo E
	Order      string      // Orden
}

// Modop es la estructura de respuesta global de los modelos para los controladores
// ej ct.TicketAdd mo.TicketAdd
type Modop struct {
	Status int    // 200, 300, 400, 401, 404, 500, etc
	Sinstr string // Mensaje
	Sinint int    // Número
	Extra  string // Info extra
	Complx interface{}
}

func GetFilteredData(params FilterParams, requser UserFull) map[string]interface{} {
	var query *gorm.DB
	var res = map[string]interface{}{"total": 0, "data": []interface{}{}}
	if params.Model != nil {
		query = Dbl.Model(params.Model)
	} else if params.Table != "" {
		query = Dbl.Table(params.Table)
	} else {
		return res
	}
	if params.Cid != "" { // Campaña
		if util.InComaArray(params.Cid, requser.Campanas) {
			query = query.Where("id_campaign = ? OR id_campaign IS NULL", params.Cid)
		} else {
			return res
		}
	} else if !params.IgnoreCams {
		query = query.Where("id_campaign IN (" + requser.Campanas + ")")
	}
	if params.Uid != "" {
		query = query.Where("id_user = ?", params.Uid)
	}
	if (params.DateFr != "" || params.DateTo != "") && params.CampoFecha != "" {
		if params.DateFr == "" {
			// Fecha 1 de enero de 2000 tiempo local
			params.DateFr = "2000-01-01"
		}
		ini := util.Str2Date(params.DateFr)
		fin := util.Str2Date(params.DateTo)
		if fin.IsZero() || fin.Before(ini) {
			params.DateTo = time.Now().In(Local).Format("2006-01-02")
		}
		query = query.Where("DATE("+params.CampoFecha+") >= ? AND DATE("+params.CampoFecha+") <= ?", params.DateFr, params.DateTo)
	}
	if params.Other != "" {
		query = query.Where(params.Other+" = ?", params.OVal)
	}
	if params.Ofa != "" {
		query = query.Where(params.Ofa+" = ?", params.Ova)
	}
	if params.Ofb != "" {
		query = query.Where(params.Ofb+" = ?", params.Ovb)
	}
	if params.Ofc != "" {
		query = query.Where(params.Ofc+" = ?", params.Ovc)
	}
	if params.Ofd != "" {
		query = query.Where(params.Ofd+" = ?", params.Ovd)
	}
	if params.Ofe != "" {
		query = query.Where(params.Ofe+" = ?", params.Ove)
	}
	if params.Campos != "" {
		query.Select(params.Campos)
	}
	for _, v := range params.Joins {
		query = query.Joins(v)
	}
	var cuenta int64
	query.Count(&cuenta)
	res["total"] = cuenta
	if cuenta == 0 {
		res["data"] = []string{}
		return res
	}
	if params.Order != "" {
		query = query.Order(params.Order)
	}
	var pag, rpp int
	if params.Page != "" {
		pag = util.Str2Int(params.Page)
		if pag < 1 {
			pag = 1
		}
		rpp = 20
		if params.Rpp != "" {
			rpp = util.Str2Int(params.Rpp)
		}
		query = query.Offset((pag - 1) * rpp).Limit(rpp)
	}
	if params.Target == nil {
		var target []map[string]interface{}
		query.Find(&target)
		res["data"] = target
	} else {
		query.Find(&params.Target)
		res["data"] = params.Target
	}
	if pag != 0 {
		res["pag"] = pag
		res["rpp"] = rpp
	}

	return res
}

/**
 * Obtiene el id del agente al que se le asignará una tarea
 * actualmente soporta sólo "para" chat
 * ToDo: implementar para email, whatsapp, videoconferencia, CRM, SMS, PIT etc.
 * @param para - El tipo de tarea
 * @param cid - La campaña
 * @return El id del agente
 */
func IntGetAgentePara(para string, cid string) (agente int) {
	opciones := getAgentesOnlineXCamConPermiso(cid, para)
	if len(opciones) == 0 {
		return
	} else if len(opciones) == 1 {
		return opciones[0]
	}
	// A partir de aquí ya se que hay al menos 2 agentes disponibles para lo que se pide
	switch para {
	case "chat":
		var anteriores []int
		Dbl.Raw(`SELECT id_user FROM chat_session WHERE id IN
			(SELECT max(id) FROM chat_session
			WHERE id_campaign = ? AND id_user IS NOT NULL GROUP BY id_user)`, cid).Scan(&anteriores)
		if len(anteriores) == 0 {
			return opciones[0]
		}
		for _, v := range opciones {
			if !util.InArray(v, anteriores) {
				return v
			}
		}
		for _, v := range anteriores {
			if util.InArray(v, opciones) {
				return v
			}
		}
	case "email":
	}

	return
}

func getAgentesOnlineXCamConPermiso(cam string, perm string) (agentes []int) {
	Dbl.Raw(`SELECT uf.id FROM user_full uf
		LEFT JOIN user_data ud ON ud.id_user = uf.id
		LEFT JOIN catalogs c ON c.id = ud.id_catalog
		WHERE uf.active = 1 AND FIND_IN_SET(uf.campanas, ?) AND c.cat = 'permisoSec' AND c.val = ?
		AND uf.id IN (SELECT DISTINCT uid FROM ses_ab WHERE uid IS NOT NULL AND uid <> 0)`, cam, perm).Scan(&agentes)

	return
}
