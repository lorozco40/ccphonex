package mo

import (
	"net/http"
	"time"

	"phonex/bago/forms"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

type Dispatcher struct {
	ID          uint        `json:"id" gorm:"primaryKey;type:int(11);autoIncrement:true"`
	IdCampaign  uint        `json:"id_campaign" gorm:"type:int(11)"`
	Name        string      `json:"name" gorm:"type:varchar(100);not null;default ''"`
	Gateway     string      `json:"gateway" gorm:"type:varchar(15);not null;default:10.10.2.133"`
	Dialer      string      `json:"dialer" gorm:"type:varchar(60);not null;default:alinker"`
	Maskname    string      `json:"maskname" gorm:"type:varchar(60);not null;default:Corporativo"`
	Masknum     string      `json:"masknum" gorm:"type:varchar(20);not null;default:5553750000"`
	Rounds      int         `json:"rounds" gorm:"type:tinyint(1);not null;default:7"`
	Multi       int         `json:"multi" gorm:"type:tinyint(1);not null;default:1"`
	Active      bool        `json:"active" gorm:"type:tinyint(1);not null;default:1"`
	Running     bool        `json:"running" gorm:"type:tinyint(1);not null;default:0"`
	Autodial    string      `json:"autodial" gorm:"type:tinyint(1);not null;default:0"`
	Queue       string      `json:"queue" gorm:"type:varchar(5)"`
	CreatedBy   int         `json:"created_by" gorm:"type:int(11)"`
	CreatedWhen time.Time   `json:"created_when" gorm:"type:datetime not null;default current_timestamp()"`
	Campaign    Campaign    `json:"campaign" gorm:"foreignkey:IdCampaign"`
	DispFields  []DispField `json:"fields" gorm:"foreignkey:IdDispatcher"`
}

type DispField struct {
	ID           uint   ` json:"id" gorm:"primaryKey;type:int(11);autoIncrement:true"`
	IdDispatcher uint   ` json:"id_dispatcher" gorm:"type:int(11);not null"`
	Name         string ` json:"name" gorm:"type:varchar(40);not null;default ''"`
	Slug         string ` json:"slug" gorm:"type:varchar(40);not null;default ''"`
	Type         string ` json:"type" gorm:"type:varchar(40);not null;default ''"`
	Typedb       bool   ` json:"typedb" gorm:"type:tinyint(1);not null;default:0"`
	Sfdes        bool   ` json:"sfdes" gorm:"type:tinyint(1);not null;default:0"`
	Depend       int    ` json:"depend" gorm:"type:tinyint(1);not null;default:0"`
	Options      string ` json:"options" gorm:"type:text;not null;default ''"`
	Showform     bool   ` json:"showform" gorm:"type:tinyint(1);not null;default:0"`
	Readonly     bool   ` json:"readonly" gorm:"type:tinyint(1);not null;default:0"`
	Required     bool   ` json:"required" gorm:"type:tinyint(1);not null;default:0"`
	Order_       int32  ` json:"order" gorm:"type:tinyint(2);not null;default:0"`
}

// DispLead es la estructura básica para los leads despachadores
// el resto de campos se agrega entre teléfono y qualif
// No es necesariamente una tabla sera de tipo disp_1, disp_2, etc
// Para mostrar
type DispLead struct {
	ID         uint       `json:"id" gorm:"primaryKey;type:int(11);autoIncrement:true"`
	Telefono   string     `json:"phone" gorm:"type:varchar(20);not null"`
	Qualif     string     `json:"last_tipi" gorm:"type:varchar(40);not null;default ''"`
	Llamadas   int        `json:"calls" gorm:"type:tinyint(2);not null;default:0"`
	Invalid    bool       `json:"invalid" gorm:"type:tinyint(1);not null;default:0"`
	Access     int        `json:"access" gorm:"type:tinyint(2);not null;default:0"`
	Busy       *time.Time `json:"-" gorm:"type:datetime"`
	Status     bool       `json:"status" gorm:"type:tinyint(1);not null;default:0"`
	LastUpdate *time.Time `json:"last_update" gorm:"type:datetime"`
	Added      time.Time  `json:"added" gorm:"type:datetime"`
	Since      *time.Time `json:"-" gorm:"type:datetime"`
}

// Para insertar a la base de datos
type realDispLead struct {
	ID       uint      `json:"id"`
	Telefono string    `json:"telefono"`
	Added    time.Time `json:"added"`
}

// DispQualif es la estructura básica de las tipificaciones de los leads
// el resto de campos se agrega al final
// No es necesariamente una tabla sera de tipo disp_1_qualif, disp_2_qualif, etc
type DispQualif struct {
	ID           uint      `json:"id" gorm:"primaryKey;type:int(11);autoIncrement:true"`
	IdDispData   uint      `json:"id_lead" gorm:"type:int(11);not null"`
	Uniqueid     string    `json:"-" gorm:"type:varchar(50);not null"`
	Linkedid     string    `json:"uniqueid" gorm:"type:varchar(50);not null"`
	Tipificacion string    `json:"tipificacion" gorm:"type:varchar(20);not null;default ''"`
	Comentarios  string    `json:"comentarios" gorm:"type:text;not null;default ''"`
	SavedBy      uint      `json:"id_user" gorm:"type:int(11);not null"`
	SavedWhen    time.Time `json:"date" gorm:"type:datetime"`
}

// DispList regresa una lista de todos los despachadores
func DispList(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	var losdisps []Dispatcher
	Dbl.Table("dispatcher").Preload("Campaign").Where("id_campaign in (" + requser.Campanas + ")").Find(&losdisps)
	ret := make([]interface{}, len(losdisps))
	for i, eldisp := range losdisps {
		ret[i] = util.RecortaStruc(eldisp, "Campaign,DispFields,Dialer,Gateway,Multi,CreatedBy,CreatedWhen")
	}

	util.RespondJSON(w, 200, ret)
}

// DispOne muestra los detalles y tipificaciones de un solo despacharo por id
func DispOne(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	id := p.ByName("id")
	var eldisp Dispatcher
	Dbl.Preload("Campaign").Preload("DispFields").Where("id_campaign in ("+requser.Campanas+")").First(&eldisp, id)
	if eldisp.ID == 0 {
		util.RespondError(w, 403, "No autorizado")
		return
	}
	ret := util.RecortaStruc(eldisp, "Campaign,DispFields,Dialer,Gateway,Multi,CreatedBy,CreatedWhen")
	campos := make([]interface{}, 0, len(eldisp.DispFields))
	for _, field := range eldisp.DispFields {
		if !util.InArray(field.Slug, []string{"telefono"}) && !field.Typedb {
			campos = append(campos, util.RecortaStruc(field, "ID,IdDispatcher,Type,Typedb,Sfdes,Depend,Options,Showform"))
		}
	}
	ret["fields"] = campos

	util.RespondJSON(w, 200, ret)
}

// DispLeadList accion de la ruta POST despachador/:id/lead
func DispLeadList(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	id := p.ByName("id") // id del despachador
	var eldisp Dispatcher
	Dbl.Preload("DispFields").Where("id_campaign in ("+requser.Campanas+")").First(&eldisp, id)
	if eldisp.ID == 0 {
		util.RespondError(w, 403, "No autorizado")
		return
	}
	campos := "id, telefono as phone, qualif as last_tipi, llamadas as calls, invalid, access, status, last_update, added"
	for _, field := range eldisp.DispFields {
		if !util.InArray(field.Slug, []string{"telefono"}) && !field.Typedb {
			campos += ", " + field.Slug
		}
	}
	filtros := FilterParams{
		Table:      "disp_" + id,
		Campos:     campos,
		Page:       reqdata.Get("pag"),
		Rpp:        reqdata.Get("rpp"),
		Other:      reqdata.Get("of"),
		OVal:       reqdata.Get("ov"),
		IgnoreCams: true,
	}

	util.RespondJSON(w, 200, GetFilteredData(filtros, requser))
}

func DispLeadOne(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	id := p.ByName("id") // id del despachador
	if id == "" {
		util.RespondError(w, 401, "Información incorrecta id")
		return
	}
	var eldisp Dispatcher
	Dbl.Preload("DispFields").Where("id_campaign in ("+requser.Campanas+")").First(&eldisp, id)
	if eldisp.ID == 0 {
		util.RespondError(w, 403, "No autorizado")
		return
	}
	lid := p.ByName("lid") // id del lead
	if lid == "" {
		util.RespondError(w, 401, "Información incorrecta lid")
		return
	}
	camposd := "id, telefono as phone, qualif as last_tipi, llamadas as calls, invalid, access, status, last_update, added"
	camposc := "id, linkedid as uniqueid, saved_by as id_user, saved_when as date"
	for _, field := range eldisp.DispFields {
		if !util.InArray(field.Slug, []string{"telefono"}) && !field.Typedb {
			camposd += ", " + field.Slug
		}
		if field.Typedb {
			camposc += ", " + field.Slug
		}
	}
	var target map[string]interface{}
	Dbl.Table("disp_"+id).Select(camposd).Where("id = ?", lid).Find(&target)
	var llamadas []map[string]interface{}
	Dbl.Table("disp_"+id+"_qualif").Select(camposc).Where("id_disp_data = ?", lid).Find(&llamadas)
	target["t_calls"] = llamadas

	util.RespondJSON(w, 200, target)
}

// DispTipis Detalle de llamadas de registro de un despachador
func DispCalls(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	id := p.ByName("id") // id del despachador
	var eldisp Dispatcher
	Dbl.Preload("DispFields").Where("id_campaign in ("+requser.Campanas+")").First(&eldisp, id)
	if eldisp.ID == 0 {
		util.RespondError(w, 403, "No autorizado")
		return
	}
	campos := "id, id_disp_data as id_lead, linkedid as uniqueid, saved_by as id_user, saved_when as date"
	for _, field := range eldisp.DispFields {
		if field.Typedb {
			campos += ", " + field.Slug
		}
	}
	filtros := FilterParams{
		Table:      "disp_" + id + "_qualif",
		Campos:     campos,
		Page:       reqdata.Get("pag"),
		Rpp:        reqdata.Get("rpp"),
		CampoFecha: "saved_when",
		DateFr:     reqdata.Get("desde"),
		DateTo:     reqdata.Get("hasta"),
		Other:      reqdata.Get("of"),
		OVal:       reqdata.Get("ov"),
		IgnoreCams: true,
	}

	util.RespondJSON(w, 200, GetFilteredData(filtros, requser))
}

// DispNewLead inserta un nuevo lead en la tabla correspondiente
func DispLeadNew(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	id := p.ByName("id") // id del despachador
	var eldisp Dispatcher
	Dbl.Preload("DispFields").Where("id_campaign in ("+requser.Campanas+")").First(&eldisp, id)
	if eldisp.ID == 0 {
		util.RespondError(w, 403, "No autorizado")
		return
	}
	var nuevolead realDispLead
	nuevolead.Telefono = util.NumericOnly(reqdata.Get("tel"))
	if nuevolead.Telefono == "" || (len(nuevolead.Telefono) != 10 && len(nuevolead.Telefono) != 12) {
		util.RespondError(w, 401, "Información incorrecta")
		return
	}
	if len(nuevolead.Telefono) == 10 {
		nuevolead.Telefono = "52" + nuevolead.Telefono
	}
	toadd := util.StrucToMap(nuevolead)
	toadd["added"] = time.Now().In(Local).Format("2006-01-02 15:04:05")
	util.PrintJson(toadd)
	for _, field := range eldisp.DispFields {
		if !util.InArray(field.Slug, []string{"telefono"}) && !field.Typedb {
			toadd[field.Slug] = reqdata.Get(field.Slug)
		}
	}
	Dbl.Table("disp_" + id).Create(&toadd)
	toadd["id"] = toadd["@id"]
	delete(toadd, "@id")

	util.RespondJSON(w, 200, toadd)
}
