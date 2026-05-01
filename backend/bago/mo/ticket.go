package mo

import (
	"crypto/md5"
	"encoding/hex"
	"fmt"
	"io"
	"os"
	"phonex/bago/forms"
	"phonex/bago/util"
	"strings"
	"time"
)

// FormTicket es la estructura básica para las tablas de los formularios CRM, tabla tipo formd_123
type FormTicket struct {
	FormEntries
	IdCliente uint       `json:"id_cliente"`
	AsignarA  *uint      `json:"asignar_a"`
	Informar  string     `json:"informar"`
	Detalle   string     `json:"detalle"`
	Tipo      string     `json:"tipo"`
	Prioridad string     `json:"prioridad"`
	Estatus   string     `json:"estatus"`
	Semaforo  string     `json:"semaforo"`
	Cierre    *time.Time `json:"cierre"`
	IdForm    uint       `json:"id_form" gorm:"-"`
}

type FormTicketFull struct {
	FormTicket
	Fields []FormFields `json:"fields" gorm:"-"`
}

// FormLogs es el modelo de la bitácora de los formularios, tabla tipo formd_123_crm
type FormLogs struct {
	ID         uint      `json:"-" gorm:"primary_key;type:int(11);auto_increment"`
	IdFormd    uint      `json:"-" gorm:"type:int(11);not null"`
	IdUser     uint      `json:"id_user" gorm:"type:int(11);not null"`
	Uniqueid   string    `json:"uniqueid" gorm:"type:varchar(50);not null"`
	Linkedid   string    `json:"linkedid" gorm:"type:varchar(50);not null"`
	Fecha      time.Time `json:"fecha" gorm:"type:datetime;not null;default:current_timestamp()"`
	Comentario string    `json:"comentario" gorm:"type:text;not null"`
	Estatus    string    `json:"estatus" gorm:"type:varchar(50);not null"`
}

type FormCats struct {
	ID     uint   `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	Campo  string `json:"campo" gorm:"type:varchar(50);not null"`
	Val    string `json:"val" gorm:"type:varchar(50);not null"`
	Eti    string `json:"eti" gorm:"type:varchar(50);not null"`
	Active bool   `json:"-" gorm:"type:tinyint(1);not null;default:1"`
}

// FormAdd agrega un formulario
// ToDo: Agregar campos CRM a form_fields y crear tablas
func GroupAdd(data *forms.Data, ru UserFull) (form Form) {
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
	if eaid := uint(data.Str2Int("eaid")); eaid > 0 {
		form.IdEmailAccount = &eaid
	}
	if form.IdEmailAccount == nil || form.Name == "" || form.IdCampaign == 0 {
		return
	}
	form.Crm = true
	form.Active = true
	form.CreatedBy = uint(ru.ID)
	form.CreatedWhen = time.Now().In(Local)
	form.LastUpdate = time.Now().In(Local)

	Dbl.Create(&form)
	return
}

func TicketGroupAssignableList() (groups []Form) {
	Dbl.Where("active = 1 AND crm = 1 AND repstatdet = 2").Find(&groups)

	return
}

func TicketList(form FormFull, requser UserFull, reqdata *forms.Data) map[string]interface{} {
	fields := FormFieldsList(form, requser, "report")
	campos := "id,id_user,apertura,detalle,id_cliente,asignar_a,estatus"
	excluir := campos + ",created_by,created_when,last_update,active"
	for _, field := range fields {
		if !util.InComaArray(field.Slug, excluir) {
			campos += ", " + field.Slug
		}
	}
	filtros := FilterParams{
		Table:      "formd_" + fmt.Sprint(form.ID),
		Campos:     campos,
		Page:       reqdata.Get("pag"),
		Rpp:        reqdata.Get("rpp"),
		Other:      reqdata.Get("of"),
		OVal:       reqdata.Get("ov"),
		IgnoreCams: true,
	}

	return GetFilteredData(filtros, requser)
}

func TicketAbiertosNoAsignados(gid uint) (tickets []FormTicket) {
	sgid := fmt.Sprint(gid)
	Dbl.Table("formd_" + sgid).Select("*, nullif(asignar_a, '') AS asignar_a, '" + sgid + "' AS id_form").
		Where("estatus = 'Abierto' AND (asignar_a = '' OR asignar_a IS NULL)").Find(&tickets)

	return
}

func TicketOne(form FormFull, tid string, requser UserFull) map[string]interface{} {
	fields := FormFieldsList(form, requser, "report")
	campos := "id,id_user,apertura,detalle,id_cliente,asignar_a,estatus"
	excluir := campos + ",created_by,created_when,last_update,active"
	for _, field := range fields {
		if !util.InComaArray(field.Slug, excluir) {
			campos += ", " + field.Slug
		}
	}
	filtros := FilterParams{
		Table:      "formd_" + fmt.Sprint(form.ID),
		Campos:     campos,
		Other:      "id",
		OVal:       tid,
		IgnoreCams: true,
	}
	reg := GetFilteredData(filtros, requser)
	data, ok := reg["data"].([]map[string]interface{})
	if !ok || len(data) == 0 {
		return map[string]interface{}{}
	}
	toret := data[0]
	toret["bitacora"] = TicketLogsList(fmt.Sprint(form.ID), tid)

	return toret
}

func TicketOneBasic(gid, tid string) (reg FormTicket) {
	Dbl.Table("formd_"+gid).Where("id = ?", tid).Find(&reg)

	return
}

func TicketAdd(gid string, requser UserFull, reqdata *forms.Data, form FormFull) Modop {
	var maxid, insid int
	errormsg := "Error al crear ticket"
	fields := "id_user,apertura,estatus,semaforo"
	values := []interface{}{requser.ID, time.Now().In(Local), "Abierto", "verde"}
	excluir := fields + ",cierre"
	form.Fields = FormFieldsList(form, requser, "api")
	for _, campo := range form.Fields {
		campoval := reqdata.Get(campo.Slug)
		if campoval == "" && campo.Required {
			errormsg = "Valor requerido para: " + campo.Name
			goto final
		}
		if campoval != "" && !util.InComaArray(campo.Slug, excluir) {
			fields += "," + campo.Slug
			values = append(values, reqdata.Get(campo.Slug))
			if campo.Slug == "id_cliente" && !isPartOfFormClients(form.IdCampaign, campoval) {
				errormsg = "Valor incorrecto para: " + campo.Name
				goto final
			}
			if campo.Slug == "asignar_a" && !isAssignableToForm(form.IdCampaign, campoval) {
				errormsg = "Valor incorrecto para: " + campo.Name
				goto final
			}
			if campo.Descen == "ddeepp" {
				endfnam := "_dep"
				if campo.Depend != 0 {
					endfnam = "_dep" + fmt.Sprint(campo.Depend)
				}
				relacionados := map[string]interface{}{}
				Dbl.Raw("SELECT * from formd_"+gid+endfnam+" WHERE "+campo.Slug+" = ?", reqdata.Get(campo.Slug)).Take(&relacionados)
				// Si no encuentra el registro en la tabla de dependencias y es requerido, termina con error
				if relacionados[campo.Slug] == nil && campo.Required {
					errormsg = "Valor incorrecto para: " + campo.Name
					goto final
				}
				for key, val := range relacionados {
					if key != campo.Slug && key != "active_system_row" {
						fields += "," + key
						values = append(values, val)
					}
				}
			}
			// Si campo.Values empieza con cat es un campo de catálogo verificar que el valor exista en la tabla _cats
			if strings.HasPrefix(campo.Values, "cat") {
				var catval string
				Dbl.Raw("SELECT val FROM formd_"+gid+"_cats WHERE field = ? AND val = ?", campo.Slug, reqdata.Get(campo.Slug)).Pluck("val", &catval)
				if catval == "" && campo.Required {
					errormsg = "Valor incorrecto para: " + campo.Name
					goto final
				}
				values[len(values)-1] = catval
			}
		}
		excluir += "," + campo.Slug
	}

	Dbl.Raw("SELECT MAX(id) FROM formd_"+gid).Pluck("MAX(id)", &maxid)
	Dbl.Exec("INSERT INTO formd_"+gid+" ("+fields+") VALUES (?)", values)
	Dbl.Raw("SELECT LAST_INSERT_ID()").Scan(&insid)

	if insid > 0 && insid != maxid {
		newreg := FormLogs{
			IdFormd:    uint(insid),
			IdUser:     requser.ID,
			Fecha:      time.Now().In(Local),
			Comentario: "Ticket creado",
			Estatus:    "Abierto",
		}
		Dbl.Table("formd_" + gid + "_crm").Create(&newreg)
		return Modop{Status: 200, Extra: fmt.Sprint(insid), Complx: map[string]int{"Ticket": insid}}
	}

final:
	LogAdd(BagoLog{Evento: "TicketAdd", Data: reqdata.Encode(), Comentario: "mo/ticket.go 225: " + errormsg}, requser.ID)
	return Modop{Status: 400, Complx: map[string]string{"Error": errormsg}}
}

func TicketLogsList(gid, tid string) (bitacora []FormLogs) {
	Dbl.Table("formd_"+gid+"_crm").Where("id_formd = ?", tid).Find(&bitacora)

	return
}

func TicketDel(gid, tid string) bool {
	// verificar que un registro existe en la tabla por id y si es así borrarlo y responder true, si no responder false
	var reg FormTicket
	Dbl.Table("formd_"+gid).Where("id = ?", tid).Find(&reg)
	if reg.ID == 0 {
		return false
	}
	Dbl.Table("formd_"+gid).Where("id = ?", tid).Delete(&FormTicket{})
	Dbl.Table("formd_"+gid+"_crm").Where("id_formd = ?", tid).Delete(&FormLogs{})

	return true
}

func TicketLogsAdd(gid, tid string, requser UserFull, reqdata *forms.Data) bool {
	datos := FormLogs{
		IdFormd:    util.Str2Uint(tid),
		IdUser:     requser.ID,
		Uniqueid:   reqdata.Get("uniqueid"),
		Linkedid:   reqdata.Get("linkedid"),
		Fecha:      time.Now().In(Local),
		Comentario: reqdata.Get("comentario"),
		Estatus:    reqdata.Get("estatus"),
	}
	id_estado := reqdata.Get("id_estado")
	if id_estado != "" {
		Dbl.Raw("SELECT eti FROM formd_"+gid+"_cats WHERE field = 'id_estado' AND val = ?", id_estado).Pluck("eti", &datos.Estatus)
	}
	Dbl.Table("formd_" + gid + "_crm").Create(&datos)
	if datos.Estatus == "Cerrado" {
		Dbl.Table("formd_"+gid).Where("id = ?", tid).Update("estatus", datos.Estatus).Update("cierre", time.Now().In(Local))
	} else {
		Dbl.Table("formd_"+gid).Where("id = ?", tid).Update("estatus", datos.Estatus)
	}
	if datos.ID == 0 {
		return false
	}

	return true
}

func TicketMaxAsignadosXFecha(gid uint) (asignados []FormTicket) {
	Dbl.Raw(`SELECT *, ` + fmt.Sprint(gid) + ` AS id_form, nullif(asignar_a, '') as asignar_a FROM formd_` + fmt.Sprint(gid) +
		` where id in (SELECT max(id) FROM formd_` + fmt.Sprint(gid) + ` WHERE asignar_a IS NOT NULL and asignar_a <> '' group by asignar_a) order by apertura`).Find(&asignados)

	return
}

func TicketAsignaAgente(ticket FormTicket, agente UserFull, grupoid uint) (eachtt []map[string]interface{}) {
	Dbl.Exec("UPDATE formd_"+fmt.Sprint(grupoid)+" SET asignar_a = ? WHERE id = ?", agente.ID, ticket.ID)
	Dbl.Raw("SELECT t.id, f.name, f.id_campaign, t.apertura, t.estatus, '"+fmt.Sprint(grupoid)+
		"' AS 'id_form' FROM formd_"+fmt.Sprint(grupoid)+" t LEFT JOIN form f on f.id = '"+
		fmt.Sprint(grupoid)+"' WHERE t.id = ?", ticket.ID).Find(&eachtt)

	return
}

func TicketAbiertosXAgente(uid string, cams string) (tickets []map[string]interface{}) {
	var forms []Form
	var eachtt []map[string]interface{}
	ucams := util.ComaSep2UintSlice(cams)
	Dbl.Table("form").Where("crm = 1 AND active = 1 AND repstatdet = 2 AND id_campaign IN ?", ucams).Find(&forms)
	for _, form := range forms {
		Dbl.Raw("SELECT t.id, f.name, f.id_campaign, t.apertura, t.estatus, '"+fmt.Sprint(form.ID)+"' AS 'id_form' FROM formd_"+fmt.Sprint(form.ID)+
			" t LEFT JOIN form f on f.id = '"+fmt.Sprint(form.ID)+"' WHERE asignar_a = ? AND estatus = 'Abierto'", uid).Find(&eachtt)
		tickets = append(tickets, eachtt...)
	}

	return
}

func TicketCatsList(gid string) (cats []string) {
	Dbl.Table("formd_"+gid+"_cats").Distinct().Pluck("field", &cats)
	mascats := make([]string, 0)
	Dbl.Raw("SELECT slug FROM form_fields WHERE id_form = ? AND descen = 'ddeepp'", gid).Pluck("slug", &mascats)
	cats = append(cats, mascats...)

	return
}

func TicketCatsEntsList(gid, campo string) (ents []struct {
	ID    string `json:"id"`
	Value string `json:"value"`
}) {
	formfield := FormFields{}
	Dbl.Table("form_fields").Where("id_form = ?", gid).Where("slug = ?", campo).Find(&formfield)
	if formfield.Descen == "ddeepp" {
		endname := "_dep"
		if formfield.Depend != 0 {
			endname = "_dep" + fmt.Sprint(formfield.Depend)
		}
		Dbl.Table("formd_" + gid + endname).Select(campo + " as id, " + campo + " as value").Find(&ents)
	} else {
		Dbl.Table("formd_"+gid+"_cats").Select("val as id, eti as value").
			Where("field = ?", campo).Find(&ents)
	}

	return
}

func isPartOfFormClients(cid uint, id string) bool {
	var clientid uint
	Dbl.Table("client").Where("id = ? AND (id_campaign = ? OR id_campaign IS NULL)", id, cid).Pluck("id", &clientid)

	return clientid > 0
}

func isAssignableToForm(cid uint, id string) bool {
	var userid uint
	Dbl.Table("user_full").
		Where("id = ? AND FIND_IN_SET(?, campanas) AND perfil IN ('agente','supervisor','crm') AND active = 1", id, cid).Pluck("id", &userid)

	return userid > 0
}

// TicketFileAdd agrega un archivo a un ticket
func TicketFileAdd(requser UserFull, reqdata *forms.Data, form FormFull) Modop {
	gid := reqdata.Get("gid")
	id := reqdata.Get("id")
	archivo := reqdata.GetFile("file")
	if archivo == nil {
		return Modop{Status: 400, Sinstr: "No se ha enviado archivo"}
	}
	fmt.Printf("Uploaded File: %+v\n", archivo.Filename)
	fmt.Printf("File Size: %+v\n", archivo.Size)
	fmt.Printf("MIME Header: %+v\n", archivo.Header)
	// Verifico que el archivo no sea mayor a 10MB
	if archivo.Size > 10485760 {
		return Modop{Status: 413, Sinstr: "El archivo es mayor a 10MB"}
	}
	// asigno la extensión del archivo a la variable ext
	extind := strings.LastIndex(archivo.Filename, ".")
	fmt.Printf("extind: %d\n", extind)
	ext := ""
	if extind > 0 {
		ext = strings.ToLower(archivo.Filename[extind+1:])
	}
	fmt.Printf("extind: %s\n", ext)
	// Verifico que el archivo sea de un tipo permitido
	if !util.InComaArray(ext, "jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip,tar,gz,tgz,rar,ods") {
		return Modop{Status: 403, Sinstr: "Tipo de archivo no permitido"}
	}
	// Genero el hash md5 del nombre del archivo
	hash := md5.New().Sum([]byte(archivo.Filename))
	ffilename := gid + "_" + id + "_" + hex.EncodeToString(hash) + "." + ext
	carpeta := os.Getenv("WEBDIR") + "files/tickets/" + gid
	// Verifico que la carpeta exista
	if _, err := os.Stat(carpeta); os.IsNotExist(err) {
		if err := os.Mkdir(carpeta, os.ModePerm); err != nil {
			return Modop{Status: 500, Sinstr: "Error al crear carpeta de archivos"}
		}
	}
	// Verifico que el archivo no exista en la carpeta
	if _, err := os.Stat(carpeta + "/" + ffilename); err == nil {
		// Si existe regreso error notificiando que el archivo ya existe
		return Modop{Status: 406, Sinstr: "El archivo ya existe"}
	}
	// Si no existe lo guardo
	destFile, err := os.Create(carpeta + "/" + ffilename)
	if err != nil {
		return Modop{Status: 500, Sinstr: "Error al crear archivo de destino"}
	}
	defer destFile.Close()
	bytes, err := archivo.Open()
	if err != nil {
		return Modop{Status: 400, Sinstr: "Archivo malformado"}
	}
	defer bytes.Close()
	_, err = io.Copy(destFile, bytes)
	if err != nil {
		return Modop{Status: 500, Sinstr: "Error escribir en archivo final"}
	}
	// y agrego una entrada en la tabla "formd_"+gid+"_files" con la información del archivo
	// y la url relacionada con el formd_id
	formfile := FormFile{
		IdFormd:    util.Str2Uint(id),
		IdUser:     requser.ID,
		Name:       archivo.Filename,
		Filename:   ffilename,
		Comentario: reqdata.Get("comentario"),
	}
	Dbl.Table("formd_" + gid + "_file").Create(&formfile)

	return Modop{Status: 200, Sinstr: "Archivo subido correctamente"}
}

