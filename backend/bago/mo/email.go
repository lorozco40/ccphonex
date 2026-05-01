package mo

import (
	"fmt"
	"io"
	"log"
	"net/http"
	"os"
	"path/filepath"
	"strconv"
	"time"

	"phonex/bago/forms"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

// EmailAccount modelo gorm tabla email_account
type EmailAccount struct {
	ID            uint         `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdCampaign    uint         `json:"id_campaign" gorm:"type:int(11);not null"`
	Email         string       `json:"email" gorm:"size:150;not null"`
	Nombre        string       `json:"nombre" gorm:"size:100;not null"`
	Tipo          string       `json:"tipo" gorm:"size:4;not null"`
	SignatureText string       `json:"signature_text" gorm:"size:254;not null"`
	SignatureImg  string       `json:"signature_img" gorm:"size:100;not null"`
	Use           uint         `json:"use" gorm:"type:tinyint(2)"`
	InServidor    string       `json:"in_servidor" gorm:"size:70;not null"`
	InPuerto      uint         `json:"in_puerto" gorm:"type:smallint(4);not null"`
	InSeguridad   string       `json:"in_seguridad" gorm:"size:10;not null"`
	InUser        string       `json:"in_user" gorm:"size:150;not null"`
	InPass        string       `json:"-" gorm:"size:150;not null"`
	InTipo        string       `json:"-" gorm:"size:254;not null"`
	OutServidor   string       `json:"out_servidor" gorm:"size:70;not null"`
	OutPuerto     uint         `json:"out_puerto" gorm:"type:smallint(4);not null"`
	OutSeguridad  string       `json:"out_seguridad" gorm:"size:10;not null"`
	OutUser       string       `json:"out_user" gorm:"size:150;not null"`
	OutPass       string       `json:"-" gorm:"size:150;not null"`
	Enviar        bool         `json:"enviar" gorm:"not null;default:0"`
	Activa        bool         `json:"activa" gorm:"not null;default:1"`
	CreatedBy     uint         `json:"-" gorm:"type:int(11);not null"`
	CreatedWhen   time.Time    `json:"-" gorm:"not null;default:current_timestamp()"`
	Campana       Campaign     `json:"-" gorm:"foreignKey:IdCampaign"`
	Mensajes      []EmailEntry `json:"-" gorm:"foreignKey:IdAccount"`
}

// EmailEntry modelo gorm de la tabla email_entry
type EmailEntry struct {
	ID               uint         `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdAccount        uint         `json:"id_account" gorm:"type:int(11);not null"`
	IdUser           *uint        `json:"id_user" gorm:"type:int(11)"`
	Transfer         *uint        `json:"transfer" gorm:"type:int(11)"`
	Duration         *uint        `json:"duration" gorm:"type:int(6);UNSIGNED"`
	DurationWait     *uint        `json:"duration_wait" gorm:"type:int(6);UNSIGNED"`
	DurationAsa      *uint        `json:"duration_asa" gorm:"type:int(6);UNSIGNED"`
	Htmlmsg          string       `json:"htmlmsg" gorm:"type:mediumtext;not null"`
	Textmsg          string       `json:"textmsg" gorm:"type:text;not null"`
	Charset          string       `json:"charset" gorm:"size:15;not null"`
	Sender           string       `json:"sender" gorm:"size:100;not null"`
	From             string       `json:"from" gorm:"size:150;not null"`
	To               string       `json:"to" gorm:"size:255;not null"`
	Cc               string       `json:"cc" gorm:"size:255;not null"`
	Cco              string       `json:"cco" gorm:"size:255;not null"`
	Replyto          string       `json:"replayto" gorm:"size:150;not null"`
	Rawdate          string       `json:"rawdate" gorm:"size:50;not null"`
	Date             time.Time    `json:"date" gorm:"not null"`
	Subject          string       `json:"subject" gorm:"type:text;not null"`
	Attachments      string       `json:"attachments" gorm:"not null"`
	DatetimeReceived time.Time    `json:"datetime_received" gorm:"not null"`
	DatetimeAsigned  *time.Time   `json:"datetime_asigned"`
	DatetimeStartre  *time.Time   `json:"datetime_startre"`
	DatetimeReply    *time.Time   `json:"datetime_reply"`
	Type             string       `json:"type" gorm:"size:10;not null"`
	Status           uint         `json:"status" gorm:"type:tinyint(1);not null;default:0"` // 0: Nuevo, 1:Leido, 2:Respondido, 3:RespondidoAll, 4:Reenviado, 5:Transferido, 6:Eliminado
	Account          EmailAccount `json:"-" gorm:"foreignKey:IdAccount"`
	User             User         `gorm:"foreignkey:IdUser"`
}

type EmailTransfer struct {
	ID           uint      `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdEmailEntry uint      `json:"id_email_entry" gorm:"type:int(11);not null"`
	From         uint      `json:"from" gorm:"type:int(11);not null"`
	To           uint      `json:"to" gorm:"type:int(11);not null"`
	CreatedWhen  time.Time `json:"created_when" gorm:"type:timestamp;default:CURRENT_TIMESTAMP"`
}

// EmailPag es una pagina de registros de correos default 20 registros
type EmailPag struct {
	Pag  int          `json:"pag"`
	Regs int64        `json:"regs"`
	Lim  int          `json:"lim"`
	Data []EmailEntry `json:"data"`
}

// EmailAccountsPag es una pagina de registros de cuenta de correo default 20 registros
type EmailAccountsPag struct {
	Pag  int            `json:"pag"`
	Regs int64          `json:"regs"`
	Lim  int            `json:"lim"`
	Data []EmailAccount `json:"data"`
}

// EmailConsolaData devuelve todos los entrantes y salientes para el usuario que lo solicita
type EmailConsolaData struct {
	Cuenta  []map[string]interface{} `json:"cuenta"`
	Agentes []map[string]interface{} `json:"agentes"`
	Data    []EmailEntry             `json:"data"`
}

// EmailConsola trae un listado parcial de email_entry, para consola
func EmailConsola(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	udata := GetUserData(fmt.Sprint(requser.ID))
	var idemcta string
	for i := range udata.Data {
		if udata.Data[i].Cat == "userData" && udata.Data[i].Eti == "email" {
			idemcta = udata.Data[i].Val
			break
		}
	}
	var emCta EmailAccount
	Dbl.First(&emCta, idemcta)
	if idemcta == "0" || idemcta == "" || !emCta.Activa {
		log.Printf("%d: Solicita lista email y no tiene cuenta activa asignada", requser.ID)
		util.RespondError(w, 400, "No tienes cuenta de email asignada")
		return
	}
	var emconsola EmailConsolaData
	emconsola.Cuenta = []map[string]interface{}{{"id": emCta.ID, "id_campaign": emCta.IdCampaign, "email": emCta.Email, "nombre": emCta.Nombre, "activa": emCta.Activa}}
	Dbl.Raw(`SELECT id, concat(name,' ',last) nombre from user
		where id in (select id_user from user_data
		where id_catalog = (SELECT id from catalogs where cat='userData' and val='email')
		and val = ?)`, idemcta).Scan(&emconsola.Agentes)
	maxid := 0
	if _, ok := r.URL.Query()["maxid"]; ok {
		maxid, _ = strconv.Atoi(reqdata.Get("maxid"))
	}
	Dbl.Where("id > ? and id_account = ? and id_user = ?", maxid, idemcta, requser.ID).Order("status, date desc").Find(&emconsola.Data)
	util.RespondJSON(w, 200, &emconsola)
}

// EmailLista GET correo devuelve una lista paginada de correo electrónico
func EmailLista(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	campanas := requser.Campanas
	if _, ok := r.URL.Query()["cam"]; ok {
		tmpcams := reqdata.Get("cam")
		if util.InComaArray(tmpcams, campanas) {
			campanas = tmpcams
		} else {
			util.RespondError(w, 403, "No autorizado")
			return
		}
	}
	var pagina EmailPag
	pagina.Pag = 0
	if _, ok := r.URL.Query()["pag"]; ok {
		pagina.Pag, _ = strconv.Atoi(reqdata.Get("pag"))
	}
	pagina.Lim = 20
	if _, ok := r.URL.Query()["lim"]; ok {
		pagina.Lim, _ = strconv.Atoi(reqdata.Get("lim"))
	}
	query := Dbl.Table("email_entry").
		Joins("JOIN email_account ON email_account.id = email_entry.id_account").
		Where("email_account.id_campaign in (" + campanas + ")")
	query.Count(&pagina.Regs)
	query.Limit(pagina.Lim).Offset(pagina.Pag).Find(&pagina.Data)
	util.RespondJSON(w, 200, &pagina)
}

// EmailUno trae un registro de email por su id
func EmailUno(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	id := p.ByName("id")
	var email EmailEntry
	Dbl.First(&email, id)

	util.RespondJSON(w, 200, &email)
}

// EmailNuevo guarda una cuenta de correo electrónico
func EmailNuevo(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	var correo EmailEntry

	for key, values := range r.Form { // range over map
		for _, value := range values { // range over []string
			log.Println(key, value)
		}
	}

	// correo.IdCampaign, _ = strconv.Atoi(reqdata.Get("id_campaign"))
	// correo.Email = reqdata.Get("email")
	// correo.Nombre = reqdata.Get("nombre")
	// correo.Tipo = reqdata.Get("tipo")
	// correo.InServidor = reqdata.Get("in_servidor")
	// correo.InPuerto, _ = strconv.Atoi(reqdata.Get("in_puerto"))
	// correo.InSeguridad = reqdata.Get("in_seguridad")
	// correo.InUser = reqdata.Get("in_user")
	// correo.InPass = reqdata.Get("in_pass")
	// correo.OutServidor = reqdata.Get("out_servidor")
	// correo.OutPuerto, _ = strconv.Atoi(reqdata.Get("out_puerto"))
	// correo.OutSeguridad = reqdata.Get("out_seguridad")
	// correo.OutUser = reqdata.Get("out_user")
	// correo.OutPass = reqdata.Get("out_pass")
	// correo.Activa = true
	// correo.CreatedBy, _ = strconv.Atoi(reqdata.Get("uid"))
	// correo.CreatedWhen = NULL
	// Dbl.Save(&correo)

	util.RespondJSON(w, 200, &correo)
}

// EmailActu PUT actualiza un email
func EmailActu(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	requser := IntGetUserFromJSON(p.ByName("ru"))
	val := reqdata.Validator()
	val.Greater("id", 0)
	if val.HasErrors() {
		log.Println(val.Messages())
		util.RespondError(w, 400, "Datos incorrectos o incompletos")
		return
	}
	var correo EmailEntry
	id, _ := strconv.Atoi(reqdata.Get("id"))
	Dbl.First(&correo, id)
	if reqdata.Get("transfer") != "" {
		var transfer EmailTransfer
		transfer.IdEmailEntry = correo.ID
		transfer.From = requser.ID
		transfer.To = uint(reqdata.GetFloat("transfer"))
		transfer.CreatedWhen = time.Now().In(Local)
		Dbl.Save(&transfer)
		// correo tiene el registro del correo al cual se le cambiara el id_user
		correo.IdUser = &transfer.To
		correo.Status = 5
		Dbl.Save(&correo)
	} else if reqdata.Get("status") != "" {
		correo.Status = uint(reqdata.GetFloat("status"))
		if correo.DatetimeStartre == nil {
			var ahora = time.Now().In(Local)
			correo.DatetimeStartre = &ahora
		}
		Dbl.Save(&correo)
	} else {
		util.RespondError(w, 418, "Porque modificar un email?")
		return
	}
	util.RespondJSON(w, 200, &correo)
}

// EmailBorra DELETE elimina un email
func EmailBorra(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	id := p.ByName("id")
	var email EmailEntry
	Dbl.First(&email, id)
	Dbl.Delete(&email)
	util.RespondJSON(w, 200, &email)
}

// EmailHistoria GET correo-historia de emails por dirección
func EmailHistoria(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	val := reqdata.Validator()
	val.Require("uid")
	val.MinLength("email", 3)
	if val.HasErrors() {
		log.Println(val.Messages())
		util.RespondError(w, 400, "Datos incorrectos o incompletos")
		return
	}
	uid := reqdata.Get("uid")
	udata := GetUserData(uid)
	var idEmCta string
	for i := range udata.Data {
		if udata.Data[i].Cat == "userData" && udata.Data[i].Eti == "email" {
			idEmCta = udata.Data[i].Val
			break
		}
	}
	if idEmCta == "" || idEmCta == "0" {
		log.Println(uid + ": Solicita lista email y no tiene cuenta asignada.")
		util.RespondError(w, 400, "No tienes cuenta de email asignada.")
		return
	}
	email := reqdata.Get("email")
	var emails []EmailEntry
	Dbl.Where("(`from` like '%"+email+"%' OR `to` like '%"+email+"%' or subject like '%"+email+"%') AND `id_account` = ?", idEmCta).Order("date DESC").Find(&emails)
	util.RespondJSON(w, 200, &emails)
}

// EmailAgentes GET email/agentes/:cid agentes asignados a una cuenta de correo
func EmailAgentes(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	cuenta := GetEmailAccount(p.ByName("cid"), requser)
	if cuenta.ID == 0 {
		util.RespondError(w, 403, "Sin permisos")
		return
	}
	// SELECT id_user, concat(name,' ',last) nombre from user_full where find_in_set(cuenta.ID, ctas_email) > 0
	agentes := []map[string]interface{}{}
	Dbl.Raw(`SELECT uf.id, concat(u.name,' ',u.last) nombre FROM user_full uf WHERE find_in_set(?, uf.ctas_email) > 0`, cuenta.ID).Scan(&agentes)

	util.RespondJSON(w, 200, &agentes)
}

// EmailCtaLista GET correo/cuenta devuelve una lista paginada de cuentas de correo electrónico
func EmailCtaLista(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	_, suid := util.ReqParTo(reqdata.Get("uid"), strconv.Itoa(int(requser.ID)))
	if requser.Campanas == "" || requser.Campanas == "0" {
		log.Println(suid + ": Solicita lista email y no tiene campañas asignadas o no existe")
		util.RespondError(w, 400, "No tienes campañas asignadas")
		return
	}
	var res EmailAccountsPag
	res.Pag, _ = util.ReqParTo(reqdata.Get("pag"), "0")
	res.Lim, _ = util.ReqParTo(reqdata.Get("lim"), "20")
	Dbl.Table("email_account").Where("id_campaign in (" + requser.Campanas + ")").Count(&res.Regs).Limit(res.Lim).Offset(res.Pag).Find(&res.Data)
	util.RespondJSON(w, 200, &res)
}

// EmailCtaUna devuelve un registro de cuenta de correo electrónico
func EmailCtaUna(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	id := p.ByName("id")
	var cuenta EmailAccount
	Dbl.First(&cuenta, id)
	if cuenta.ID != 0 {
		util.RespondJSON(w, 200, &cuenta)
	} else {
		util.RespondError(w, 406, "Cuenta no existe")
	}
}

// EmailCtaNueva guarda una cuenta de correo electrónico
func EmailCtaNueva(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	fileDirectory := os.Getenv("WEBDIR") + "../files/"
	val := reqdata.Validator()
	val.Greater("id_campaign", 0)
	val.MatchEmail("email")
	val.MinLength("nombre", 3)
	val.Require("tipo")
	val.Greater("use", 0)
	val.MaxLength("signature_text", 250)
	val.Require("in_servidor")
	val.Greater("in_puerto", 0)
	val.Require("in_user")
	val.Require("in_pass")
	val.Require("out_servidor")
	val.Greater("out_puerto", 0)
	val.Require("out_user")
	val.Require("out_pass")
	val.Require("uid")
	if val.HasErrors() {
		log.Println(val.Messages())
		util.RespondError(w, 400, "Datos incorrectos o incompletos")
		return
	}
	var cuenta EmailAccount
	var err error
	cuenta.IdCampaign = util.Str2Uint(reqdata.Get("id_campaign"))
	cuenta.Email = reqdata.Get("email")
	cuenta.Nombre = reqdata.Get("nombre")
	cuenta.Tipo = reqdata.Get("tipo")
	cuenta.InServidor = reqdata.Get("in_servidor")
	cuenta.InPuerto = util.Str2Uint(reqdata.Get("in_puerto"))
	cuenta.InSeguridad = reqdata.Get("in_seguridad")
	cuenta.InUser = reqdata.Get("in_user")
	cuenta.InPass, err = util.Esconde(reqdata.Get("in_pass"), reqdata.Get("email"))
	if err != nil {
		util.RespondError(w, 500, "Error de encriptación")
		return
	}
	cuenta.OutServidor = reqdata.Get("out_servidor")
	cuenta.OutPuerto = util.Str2Uint(reqdata.Get("out_puerto"))
	cuenta.OutSeguridad = reqdata.Get("out_seguridad")
	cuenta.OutUser = reqdata.Get("out_user")
	cuenta.OutPass, err = util.Esconde(reqdata.Get("out_pass"), reqdata.Get("email"))
	if err != nil {
		util.RespondError(w, 500, "Error de encriptación")
		return
	}
	cuenta.Use = util.Str2Uint(reqdata.Get("use"))
	cuenta.SignatureText = reqdata.Get("signature_text")
	cuenta.Activa = true
	cuenta.CreatedBy = util.Str2Uint(reqdata.Get("uid"))
	cuenta.CreatedWhen = time.Now().In(Local)
	Dbl.Save(&cuenta)
	// Guardamos la imagen una vez que ya tenemos el id de la cuenta
	file, header, err := r.FormFile("signature_img")
	if err == nil {
		defer file.Close()
		extension := filepath.Ext(header.Filename)
		id_cuenta_str := fmt.Sprint(cuenta.ID)
		firma := "firma_email_" + id_cuenta_str + extension
		cuenta.SignatureImg = firma
		out, pathError := os.Create(fileDirectory + firma)
		if pathError != nil {
			util.RespondError(w, 400, "No se puedo guardar el archivo de firma.")
			return
		} else {
			defer out.Close()
			_, copyFileError := io.Copy(out, file)
			if copyFileError != nil {
				util.RespondError(w, 400, "Se produjo un intentar copiar el contenido del archivo de firma")
				return
			}
		}
	}
	Dbl.Save(&cuenta)

	util.RespondJSON(w, 200, &cuenta)
}

// EmailCtaActu actualiza una cuenta de correo
func EmailCtaActu(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	fileDirectory := os.Getenv("WEBDIR") + "../files/"
	val := reqdata.Validator()
	val.Greater("id", 0)
	val.Require("uid")
	if val.HasErrors() {
		log.Println(val.Messages())
		util.RespondError(w, 400, "Datos incorrectos o incompletos")
		return
	}
	var cuenta EmailAccount
	id, _ := strconv.Atoi(reqdata.Get("id"))
	Dbl.First(&cuenta, id)

	if _, ok := r.Form["id_campaign"]; ok {
		cuenta.IdCampaign = util.Str2Uint(reqdata.Get("id_campaign"))
	}
	var passmust bool
	if _, ok := r.Form["email"]; ok {
		tmpemail := reqdata.Get("email")
		if tmpemail != cuenta.Email {
			passmust = true
		}
		cuenta.Email = reqdata.Get("email")
	}
	if _, ok := r.Form["nombre"]; ok {
		cuenta.Nombre = reqdata.Get("nombre")
	}
	if _, ok := r.Form["tipo"]; ok {
		cuenta.Tipo = reqdata.Get("tipo")
	}

	if _, ok := r.Form["use"]; ok {
		cuenta.Use = util.Str2Uint(reqdata.Get("use"))
	}
	if _, ok := r.Form["signature_text"]; ok {
		cuenta.SignatureText = reqdata.Get("signature_text")
	}
	// Evaluamos si se debe de eliminar la firma_actual
	if reqdata.Get("delete_firma") == "1" {
		if cuenta.SignatureImg != "" {
			// Intentar eliminar el archivo
			err := os.Remove(fileDirectory + cuenta.SignatureImg)
			if err != nil {
				util.RespondError(w, 400, "No se puede eliminar el archivo de firma")
				return
			}
			cuenta.SignatureImg = ""
		}
	}
	file, header, err := r.FormFile("signature_img")
	if err == nil {
		defer file.Close()
		extension := filepath.Ext(header.Filename)
		cuenta_id := reqdata.Get("id")
		firma := "firma_email_" + cuenta_id + extension
		cuenta.SignatureImg = firma
		out, pathError := os.Create(fileDirectory + firma)
		if pathError != nil {
			util.RespondError(w, 400, "No se puedo guardar el archivo de firma.")
			return
		} else {
			defer out.Close()
			_, copyFileError := io.Copy(out, file)
			if copyFileError != nil {
				util.RespondError(w, 400, "Se produjo un intentar copiar el contenido del archivo de firma")
				return
			}
		}
	}

	if _, ok := r.Form["in_servidor"]; ok {
		cuenta.InServidor = reqdata.Get("in_servidor")
	}
	if _, ok := r.Form["in_puerto"]; ok {
		cuenta.InPuerto = util.Str2Uint(reqdata.Get("in_puerto"))
	}
	if _, ok := r.Form["in_seguridad"]; ok {
		cuenta.InSeguridad = reqdata.Get("in_seguridad")
	}
	if _, ok := r.Form["in_user"]; ok {
		cuenta.InUser = reqdata.Get("in_user")
	}
	if _, ok := r.Form["in_pass"]; ok {
		tmpinpass := reqdata.Get("in_pass")
		if passmust && tmpinpass == "" {
			util.RespondError(w, 400, "Password email no recibido")
			return
		}
		if tmpinpass != "" {
			// Encriptamos el password
			cuenta.InPass, err = util.Esconde(tmpinpass, reqdata.Get("email"))
			if err != nil {
				util.RespondError(w, 400, "No se pudo encriptar la información")
				return
			}
		}
	}
	if _, ok := r.Form["out_servidor"]; ok {
		cuenta.OutServidor = reqdata.Get("out_servidor")
	}
	if _, ok := r.Form["out_puerto"]; ok {
		cuenta.OutPuerto = util.Str2Uint(reqdata.Get("out_puerto"))
	}
	if _, ok := r.Form["out_seguridad"]; ok {
		cuenta.OutSeguridad = reqdata.Get("out_seguridad")
	}
	if _, ok := r.Form["out_user"]; ok {
		cuenta.OutUser = reqdata.Get("out_user")
	}
	if _, ok := r.Form["out_pass"]; ok {
		tmpoutpass := reqdata.Get("out_pass")
		if passmust && tmpoutpass == "" {
			util.RespondError(w, 400, "Password smtp no recibido")
			return
		}
		if tmpoutpass != "" {
			// Encriptamos el password
			cuenta.OutPass, err = util.Esconde(tmpoutpass, reqdata.Get("email"))
			if err != nil {
				util.RespondError(w, 400, "No se pudo encriptar la información")
				return
			}
		}
	}
	if _, ok := r.Form["activa"]; ok {
		cuenta.Activa, _ = strconv.ParseBool(reqdata.Get("activa"))
	}

	Dbl.Save(&cuenta)

	util.RespondJSON(w, 200, &cuenta)
}

/*
*
  - GetEmailAccount trae una cuenta de correo por su id
    validando la pertenencia de la campaña del usuario que lo solicita
    y que la cuenta de email este activa
  - @param id string el id de la cuenta de email
  - @param ru UserFull el usuario que solicita la cuenta
  - @return cuenta EmailAccount la cuenta de correo
*/
func GetEmailAccount(id string, ru UserFull) (cuenta EmailAccount) {
	Dbl.Where("id_campaign in ("+ru.Campanas+")").Where("activa = 1").First(&cuenta, id)

	return
}
