package mo

import (
	"net/http"
	"strconv"

	"phonex/bago/forms"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

// Endpoints es la estructura de la tabla para almacenar los endpoints de la aplicación
type Endpoint struct {
	ID          uint      `gorm:"primary_key;type:int(11)"`
	Name        string    `json:"nombre" gorm:"uniqueIndex;type:varchar(50);not null"`
	Des         string    `json:"des" gorm:"type:text;not null"`
	Method      string    `json:"metodo" gorm:"type:varchar(10);not null"`
	Route       string    `json:"ruta" gorm:"type:varchar(50);not null"`
	Section     string    `json:"seccion" gorm:"type:varchar(50);not null"`
	Level       int       `json:"nivel" gorm:"type:tinyint(1);not null;default:2"` // 0: Sólo admin, 1: Con permiso, 2: Logueado, 3: Público
	Resp        string    `json:"resp" gorm:"type:text;not null"`
	Active      bool      `json:"activo" gorm:"type:tinyint(1);not null;default:1"`
	Seq         int       `json:"seq" gorm:"type:tinyint(2);not null;default:0"`
	CreatedBy   uint      `json:"-" gorm:"type:int(11);not null"`
	CreatedWhen string    `json:"-" gorm:"type:datetime;not null;default:current_timestamp()"`
	Params      []EpParam `json:"parametros" gorm:"foreignkey:IdEndpoint"`
}

// Param es la estructura de la tabla para almacenar los parámetros de los endpoints
type EpParam struct {
	ID         uint   `gorm:"primary_key;type:int(11)"`
	IdEndpoint uint   `json:"-" gorm:"type:int(11);not null"`
	Name       string `json:"nombre" gorm:"type:varchar(50);not null"`
	Des        string `json:"des" gorm:"type:text;not null"`
	Type       string `json:"tipo" gorm:"type:varchar(50);not null"`
	Req        bool   `json:"req" gorm:"type:tinyint(1) not null default 1"`
}

// Permission es la estructura de la tabla para almacenar permisos de usuarios por endpoint
type Permission struct {
	ID     uint   `gorm:"primary_key;type:int(11)"`
	IdUser uint   `json:"-" gorm:"type:int(11);not null;unique_index:id_user_perm"`
	Perm   string `json:"perm" gorm:"type:varchar(50);not null;unique_index:id_user_perm"`
	User   User   `json:"-" gorm:"foreignkey:IdUser"`
}

func CheckPermiso(ruta Endpoint, requser UserFull) (permiso bool) {
	// Si el usuario es admin, no se requiere permiso
	if requser.Perfil == "admin" {
		return true
	}
	var cuenta int64
	Dbl.Raw("SELECT count(id) FROM permission WHERE id_user = ? AND perm = ?", requser.ID, ruta.Name).Count(&cuenta)
	if cuenta > 0 && ruta.Level > 0 {
		return true
	}

	return
}

// RutaInactiva verifica si la ruta está activa
func RutaInactiva(ruta string) (estado bool) {
	var prueba Endpoint
	Dbl.Where("name = ?", ruta).First(&prueba)
	util.PrintJson(prueba)
	if prueba.ID != 0 {
		return !prueba.Active
	}

	return false
}

// GetEndpoints regresa la lista de endpoints
// func GetEndpoints(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
//     endpoints := IntGetEndpoints()
//     util.RespondJSON(w, http.StatusOK, endpoints)
// }

func PermList(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	var response []struct {
		IdUser uint   `json:"id_user"`
		User   string `json:"user"`
		Perm   string `json:"perm"`
	}
	filtros := FilterParams{
		Uid:        reqdata.Get("uid"),
		Model:      &Permission{},
		Campos:     "permission.id_user, concat(user.name,' ',user.last) AS user, permission.perm",
		Joins:      []string{"LEFT JOIN user on user.id = permission.id_user"},
		Target:     response,
		Page:       reqdata.Get("pag"),
		Rpp:        reqdata.Get("rpp"),
		IgnoreCams: true,
	}

	util.RespondJSON(w, 200, GetFilteredData(filtros, requser))
}

func PermAdd(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	var perm Permission
	Dbl.Where("id_user = ? AND perm = ?", reqdata.Get("uid"), reqdata.Get("perm")).First(&perm)
	if perm.ID != 0 {
		util.RespondError(w, 409, "Permiso ya otorgado")
		return
	}
	uid, _ := strconv.Atoi(reqdata.Get("uid"))
	perm.IdUser = uint(uid)
	perm.Perm = reqdata.Get("perm")
	ep := IntGetEndpoint(perm.Perm)
	if ep.ID == 0 || !ep.Active {
		util.RespondError(w, 412, "Permiso no alcanzable")
		return
	}
	err := Dbl.Save(&perm).Error
	if err != nil {
		util.RespondError(w, 500, err.Error())
		return
	}
	util.RespondJSON(w, 201, perm)
}

func PermDel(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	var perm Permission
	Dbl.Where("id_user = ? AND perm = ?", reqdata.Get("uid"), reqdata.Get("perm")).First(&perm)
	if perm.ID == 0 {
		util.RespondError(w, 406, "Permiso no encontrado")
		return
	}
	err := Dbl.Delete(&perm).Error
	if err != nil {
		util.RespondError(w, 500, err.Error())
		return
	}

	util.RespondJSON(w, 200, nil)
}

func EndpointList(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
	endpoints := IntGetEndpoints("1")

	util.RespondJSON(w, 200, endpoints)
}

// IntGetEndpoints regresa la lista de endpoints
func IntGetEndpoints(uid string) (endpoints []Endpoint) {
	if uid == "1" {
		Dbl.Preload("Params").Order("section").Order("id").Find(&endpoints)
	} else {
		Dbl.Where("name in (SELECT perm FROM permission WHERE id_user = ?)", uid).
			Or("id <= 20").Order("section").Order("id").
			Preload("Params").Find(&endpoints)
	}
	return
}

// IntGetEndpoint regresa un endpoint
func IntGetEndpoint(data ...string) (endpoint Endpoint) {
	if len(data) > 1 && data[1] == "full" {
		Dbl.Preload("Params").Where("name = ?", data[0]).Find(&endpoint)
	} else if len(data) > 0 {
		Dbl.Where("name = ?", data[0]).Find(&endpoint)
	}

	return
}

// IntAddEndpoint agrega un endpoint
func IntAddEndpoint(endpoint Endpoint) (err error) {
	err = Dbl.Create(&endpoint).Error
	return
}

// IntUpdateEndpoint actualiza un endpoint
func IntUpdateEndpoint(endpoint Endpoint) (err error) {
	err = Dbl.Save(&endpoint).Error
	return
}

// IntDeleteEndpoint elimina un endpoint
func IntDeleteEndpoint(id int) (err error) {
	err = Dbl.Delete(&Endpoint{}, id).Error
	return
}

// IntAddParam agrega un parámetro a un endpoint
func IntAddParam(param EpParam) (err error) {
	err = Dbl.Create(&param).Error
	return
}

// IntUpdateParam actualiza un parámetro de un endpoint
func IntUpdateParam(param EpParam) (err error) {
	err = Dbl.Save(&param).Error
	return
}

// IntDeleteParam elimina un parámetro de un endpoint
func IntDeleteParam(id int) (err error) {
	err = Dbl.Delete(&EpParam{}, id).Error
	return
}
