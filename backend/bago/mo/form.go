package mo

import (
	"strings"
	"time"

	"phonex/bago/forms"
	"phonex/bago/util"
)

// Form es el modelo de los formularios, tabla form
type Form struct {
	ID             uint         `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdCampaign     uint         `json:"id_campaign" gorm:"type:int(11);not null"`
	IdEmailAccount *uint        `json:"-" gorm:"type:int(11)"`
	Name           string       `json:"name" gorm:"type:varchar(100);not null"`
	ShortName      string       `json:"-" gorm:"type:varchar(10);not null"`
	Type           bool         `json:"-" gorm:"type:tinyint(1);not null;default:0"`
	Crm            bool         `json:"-" gorm:"type:tinyint(1);not null;default:0"`
	Repstatdet     int          `json:"-" gorm:"type:tinyint(1);not null;default:1"`
	Active         bool         `json:"active" gorm:"type:tinyint(1);not null;default:1"`
	LastUpdate     time.Time    `json:"-" gorm:"type:datetime;not null;default:current_timestamp()"`
	CreatedBy      uint         `json:"-" gorm:"type:int(11);not null"`
	CreatedWhen    time.Time    `json:"created" gorm:"type:datetime;not null;default:current_timestamp()"`
	Campaign       Campaign     `json:"-" gorm:"ForeignKey:IdCampaign"`
	EmailAccount   EmailAccount `json:"-" gorm:"ForeignKey:IdEmailAccount"`
}

// FormFields es el modelo de los campos de los formularios, tabla form_fields
type FormFields struct {
	ID         uint   `json:"-" gorm:"primary_key;type:int(11);auto_increment"`
	IdForm     uint   `json:"-" gorm:"type:int(11);not null"`
	Name       string `json:"name" gorm:"type:varchar(50);not null"`
	Slug       string `json:"slug" gorm:"type:varchar(50);not null"`
	Type       string `json:"-" gorm:"type:varchar(30);not null"`
	Len        uint   `json:"max_length" gorm:"type:smallint(4);UNSIGNED"`
	Required   bool   `json:"required" gorm:"type:tinyint(1);not null;default:0"`
	Searchable bool   `json:"-" gorm:"type:tinyint(1);not null;default:0"`
	Editable   bool   `json:"-" gorm:"type:tinyint(1);not null;default:1"`
	Base       bool   `json:"-" gorm:"type:tinyint(1);not null;default:0"`
	Front      bool   `json:"-" gorm:"type:tinyint(1);not null;default:1"`
	Api        bool   `json:"-" gorm:"type:tinyint(1);not null;default:0"`
	Report     bool   `json:"-" gorm:"type:tinyint(1);not null;default:1"`
	Values     string `json:"values" gorm:"type:text;not null"`
	Depend     uint   `json:"-" gorm:"type:tinyint(1);not null;default:0"`
	Descen     string `json:"-" gorm:"type:varchar(50);not null"`
	Order      uint   `json:"-" gorm:"type:tinyint(2);not null;default:0"`
	Form       Form   `json:"-" gorm:"ForeignKey:IdForm"`
}

// FormApi es la tabla que relaciona el formulario con las acciones de informar mediante el API
type FormApi struct {
	ID          uint      `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdForm      uint      `json:"-" gorm:"type:int(11);not null"`
	IdExtapi    uint      `json:"-" gorm:"type:int(11)"`
	Proto       string    `json:"proto" gorm:"type:varchar(6);not null;default:'GET'"`
	Endpoint    string    `json:"endpoint" gorm:"type:varchar(127);not null;default:''"`
	OnWhen      uint      `json:"on_show" gorm:"type:tinyint(1);not null;default:2"` // 0 show, 1 open, 2 upd, 3 close, 4 human
	Extra       string    `json:"extra" gorm:"type:text;not null;default:''"`
	MapOutData  string    `json:"map_out_data" gorm:"type:text;not null;default:''"`
	MapInData   string    `json:"map_in_data" gorm:"type:text;not null;default:''"`
	Active      bool      `json:"active" gorm:"type:tinyint(1);not null;default:1"`
	CreatedBy   uint      `json:"-" gorm:"type:int(11);not null"`
	CreatedWhen time.Time `json:"-" gorm:"type:datetime;not null;default:current_timestamp()"`
	Form        Form      `json:"-" gorm:"ForeignKey:IdForm"`
	Extapi      Extapi    `json:"-" gorm:"ForeignKey:IdExtapi"`
}

// FormFull es la tabla form con los campos de form_fields
type FormFull struct {
	Form
	Fields []FormFields `json:"fields" gorm:"-"`
}

// FormEntries es la estructura básica para las tablas de los formularios, tabla tipo formd_123
// Agregar campos variables desde tabla form_fields
type FormEntries struct {
	ID       uint      `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdUser   uint      `json:"id_user" gorm:"type:int(11);not null"`
	Apertura time.Time `json:"apertura" gorm:"type:datetime;not null;default:current_timestamp()"`
	Uniqueid string    `json:"uniqueid" gorm:"type:varchar(50);not null"`
	Linkedid string    `json:"linkedid" gorm:"type:varchar(50);not null"`
}

// FormFile es el modelo de los archivos de los formularios, tabla tipo formd_123_files
type FormFile struct {
	ID         uint      `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdFormd    uint      `json:"id_formd" gorm:"type:int(11);not null"`
	IdUser     uint      `json:"id_user" gorm:"type:int(11);not null"`
	Fecha      time.Time `json:"fecha" gorm:"type:datetime;not null;default:current_timestamp()"`
	Name       string    `json:"nombre" gorm:"type:varchar(255);not null"`
	Filename   string    `json:"url" gorm:"type:varchar(50);not null"`
	Comentario string    `json:"comentario" gorm:"type:text;not null"`
	Active     bool      `json:"-" gorm:"type:tinyint(1);not null;default:1"`
}

// Lista de formularios simples 0, crm 1, etc
func FormList(ru UserFull, tipo int) (forms []Form) {
	// 0 formularios, 1 crm, se pueden agregar más tipos como despachadores, pero por ahora es un ToDo
	Dbl.Where("crm = ?", tipo).Where("id_campaign in (" + ru.Campanas + ")").Find(&forms)

	return
}

/**
 * FormOne un registro de la tabla form
 * @param id string
 * @param cams string campañas del usuario para saber si le corresponde
 * @return form FormFull
 */
func FormOne(id string, ru UserFull) (form FormFull) {
	// Se valida que la campaña del formulario esté en las campañas del usuario
	query := Dbl.Where("id = ?", id)
	if ru.Perfil != "admin" {
		query = query.Where("id_campaign in (" + ru.Campanas + ")").Where("active = 1")
	}
	query.First(&form.Form)

	return
}

// FormAdd agrega un formulario
// // ToDo: Agregar campos a form_fields y crear tablas
func FormAdd(data *forms.Data, ru UserFull) (form Form) {
	if strings.Contains(ru.Campanas, ",") {
		cid := data.Str2Int("cid")
		if cid > 0 && util.CampanaValida(cid, ru.Campanas) {
			form.IdCampaign = uint(cid)
		} else {
			return
		}
	} else {
		form.IdCampaign = util.Str2Uint(ru.Campanas)
	}
	form.Name = data.Get("name")
	form.ShortName = util.Slugify(data.Get("name"))
	if form.Name == "" || form.IdCampaign == 0 {
		return
	}
	if eaid := uint(data.Str2Int("eaid")); eaid > 0 {
		form.IdEmailAccount = &eaid
	}
	form.Active = true
	form.CreatedBy = uint(ru.ID)
	form.CreatedWhen = time.Now().In(Local)
	form.LastUpdate = time.Now().In(Local)

	Dbl.Create(&form)
	return
}

// FormUpd actualiza un formulario
func FormUpd(id string, data *forms.Data, ru UserFull) bool {
	var form Form
	Dbl.Where("id = ?", id).First(&form)
	if form.ID == 0 || !util.CampanaValida(int(form.IdCampaign), ru.Campanas) {
		return false
	}
	if cid := data.Str2Int("cid"); cid > 0 {
		if util.CampanaValida(cid, ru.Campanas) {
			form.IdCampaign = uint(cid)
		} else {
			return false
		}
	}
	if name := data.Get("name"); name != "" {
		form.Name = name
		form.ShortName = util.Slugify(name)
	}
	if data.Get("active") != "" {
		form.Active = data.GetBool("active")
	}
	if eaid := uint(data.Str2Int("eaid")); eaid > 0 {
		form.IdEmailAccount = &eaid
	}
	form.LastUpdate = time.Now().In(Local)

	Dbl.Save(&form)
	return true
}

// FormDel elimina un formulario y todas sus tablas y registros relacionados
func FormDel(id string) {
	var form Form
	Dbl.Where("id = ?", id).First(&form)
	if form.ID == 0 {
		return
	}
	var tablas []string
	Dbl.Raw("SHOW TABLES LIKE 'formd_" + id + "_%'").Scan(&tablas)
	for _, tabla := range tablas {
		Dbl.Migrator().DropTable(tabla)
	}
	Dbl.Exec("DELETE FROM crm_light WHERE id_form = ?", id)
	Dbl.Exec("DELETE FROM crm_plant_pdf WHERE id_form = ?", id)
	Dbl.Exec("DELETE FROM form_calc_fields WHERE id_form = ?", id)
	Dbl.Exec("DELETE FROM form_closing_operations WHERE id_form = ?", id)
	Dbl.Exec("DELETE FROM form_fields_tbr WHERE id_form = ?", id)
	Dbl.Exec("DELETE FROM form_filter_dep WHERE id_form = ?", id)
	Dbl.Exec("DELETE FROM form_fields WHERE id_form = ?", id)
	Dbl.Exec("DELETE FROM form WHERE id = ?", id)
}

// FormFieldsList listado de campos de un formulario
func FormFieldsList(form FormFull, requser UserFull, tipo string) (fields []FormFields) {
	// tipos: "" todos (no filtro), api, report, front
	query := Dbl.Select("*, if(descen = 'ddeepp', 'cat', `values`) as 'values'").Where("id_form = ?", form.ID)
	if tipo != "" {
		query = query.Where("type <> 'separador'")
	}
	if tipo == "api" {
		query = query.Where("api = 1")
	} else if tipo == "report" {
		query = query.Where("report = 1")
	} else if tipo == "front" {
		query = query.Where("front = 1")
	}
	query.Order("`order`, `name`").Find(&fields)

	return
}
