package mo

import (
	"encoding/json"
	"log"
	"net/http"
	"strconv"
	"strings"
	"time"

	"phonex/bago/forms"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

// User tabla user
type User struct {
	ID          uint      `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	User        string    `json:"user" gorm:"type:varchar(100) not null;unique"`
	Pass        string    `json:"-" gorm:"type:varchar(100) not null;default ''"`
	Name        string    `json:"name" gorm:"type:varchar(100) not null;default ''"`
	Last        string    `json:"last" gorm:"type:varchar(100) not null;default ''"`
	Active      bool      `json:"active" gorm:"type:tinyint(1) not null;default 1"`
	CreatedBy   uint      `json:"created_by" gorm:"type:int(11)"`
	CreatedWhen time.Time `json:"created_when" gorm:"type:datetime not null;default current_timestamp()"`
}

// UserData tabla user_data
type UserData struct {
	IdUser    uint   `json:"id_user"`
	IdCatalog uint   `json:"id_catalog"`
	Val       string `json:"val"`
}

// UserTrans tabla user_trans, usuarios que se pueden tranformar
type UserTrans struct {
	IdUser uint   `json:"id_user" gorm:"primary_key;type:int(11) not null"`
	Grupo  string `json:"grupo" gorm:"primary_key;type:varchar(30) not null"`
	User   User   `gorm:"foreignKey:IdUser;references:ID"`
}

// TransOpts tabla de grupos de opciones para tranformación de usuarios
type UserTransOpts struct {
	ID    uint   `json:"id" gorm:"primary_key;type:int(11) not null"`
	Grupo string `json:"grupo" gorm:"type:varchar(30) not null"`
	Eti   string `json:"eti" gorm:"type:varchar(50) not null"`
	Des   string `json:"des" gorm:"type:text not null"`
	Busy  string `json:"busy" gorm:"type:varchar(11) not null"`
	Trans string `json:"trans" gorm:"type:varchar(254) not null"`
}

// UserFull vista user_full
type UserFull struct {
	ID        uint   `json:"id"`
	Email     string `json:"email"`
	Name      string `json:"name"`
	Last      string `json:"last"`
	Active    bool   `json:"active"`
	Perfil    string `json:"perfil"`
	Exten     string `json:"exten"`
	Img       string `json:"img"`
	Tel       string `json:"tel"`
	Tema      string `json:"tema"`
	Pagini    string `json:"pagini"`
	Genero    string `json:"genero"`
	Campanas  string `json:"campanas"`
	Pervl     string `json:"pervl"`
	Perci     string `json:"perci"`
	Whatsapp  string `json:"whatsapp"`
	CtasEmail string `json:"ctas_email"`
	Token     string `json:"-"`
	Servask   string `json:"servask"`
	Passask   string `json:"pasask"`
}

// UserConPer user_full con arrays de permisos otorgados agregados
type UserConPer struct {
	UserFull
	Permiso     []string
	PermisoSec  []string
	PermisoRepo []string
	PermisoEsp  []string
}

// UserDataReg Estructura para userData
type UserDataReg struct {
	Cat string `json:"cat"`
	Eti string `json:"eti"`
	Val string `json:"val"`
}

// UserInfo tiene los datos del usuario más digeridos por perfil
type UserInfo struct {
	ID        string
	Perfil    string
	Campanas  string
	Extension string
	Data      UserDatas
}

// UserParaCat para enviar a la vista y usar en catálogos
type UserParaCat struct {
	ID     uint   `json:"id"`
	Name   string `json:"name"`
	Last   string `json:"last"`
	Email  string `json:"email"`
	Tel    string `json:"tel"`
	Perfil string `json:"profile"`
	Active bool   `json:"active"`
}

// UserDatas array de registros con userdata
type UserDatas []UserDataReg

// GetUserData devuelve la data desde la base
func GetUserData(uid string) UserInfo {
	var uinfo UserInfo
	var udata UserDatas
	uinfo.ID = uid
	if uid == "1" {
		uinfo.Perfil = "admin"
	}
	Dbl.Raw("SELECT c.cat, c.val eti, ud.val from catalogs c left join user_data ud on ud.id_catalog = c.id " +
		"where cat in ('permiso','permisoRepo','permisoEsp','permisoSec','userData') and ud.id_user = '" +
		uid + "' order by c.cat, c.val").Scan(&udata)
	for i := range udata {
		if udata[i].Cat == "userData" && udata[i].Eti == "campanas" {
			uinfo.Campanas = udata[i].Val
		} else if udata[i].Cat == "userData" && udata[i].Eti == "perfil" && uinfo.Perfil == "" {
			uinfo.Perfil = udata[i].Val
		} else if udata[i].Cat == "userData" && udata[i].Eti == "userask" {
			uinfo.Extension = udata[i].Val
		}
	}
	if uinfo.Perfil == "admin" {
		row := Dbl.Raw("SELECT group_concat(id) from campaign").Row()
		row.Scan(&uinfo.Campanas)
	} else {
		row := Dbl.Raw("SELECT group_concat(id) from campaign where active = 1 and id in (" + uinfo.Campanas + ")").Row()
		row.Scan(&uinfo.Campanas)
	}
	log.Println(uinfo.Campanas)
	uinfo.Data = udata

	return uinfo
}

func UserList(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
	type ret struct {
		ID     int    `json:"id"`
		Email  string `json:"email"`
		Name   string `json:"name"`
		Last   string `json:"last"`
		Perfil string `json:"role"`
		Exten  string `json:"exten"`
		Active bool   `json:"active"`
	}
	resp := make([]ret, 0)
	Dbl.Raw("SELECT id, email, name, last, perfil, exten, active FROM user_full").Scan(&resp)

	util.RespondJSON(w, 200, resp)
}

func UserMe(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	if requser.ID == 0 {
		util.RespondError(w, 400, "Usuario no encontrado")
		return
	}
	// elimino los campos que no quiero regresar, como perci, pervl, etc
	requser.Pervl = ""
	requser.Perci = ""
	requser.Passask = ""
	requser.Servask = ""
	requser.CtasEmail = ""
	requser.Whatsapp = ""
	util.RespondJSON(w, 200, requser)
}

// IntUserList (interna) devuelve la lista de usuarios
func IntUserList() (users []User) {
	Dbl.Table("user").Scan(&users)
	return users
}

func ValidaCats(uid uint) {
	var basePerm, baseUdata []Catalogs
	Dbl.Where("cat like 'permis%'").Find(&basePerm)
	Dbl.Where("cat = 'userData'").Find(&baseUdata)
	var uPer, uUdata []UserData
	Dbl.Raw("SELECT ud.* FROM user_data ud JOIN catalogs c on c.id = ud.id_catalog "+
		"WHERE c.cat LIKE 'permis%' AND ud.id_user = ?", uid).Scan(&uPer)
	Dbl.Raw("SELECT ud.* FROM user_data ud JOIN catalogs c on c.id = ud.id_catalog "+
		"WHERE c.cat = 'userData' AND ud.id_user = ?", uid).Scan(&uUdata)
	if len(uPer) < len(basePerm) {
		creaUdatasFaltantes(uPer, basePerm, uid, "0")
	}
	if len(uUdata) < len(baseUdata) {
		creaUdatasFaltantes(uUdata, baseUdata, uid, "")
	}
}

func creaUdatasFaltantes(anter []UserData, sigui []Catalogs, uid uint, val string) {
	for i := range sigui {
		var found bool
		for j := range anter {
			if sigui[i].ID == anter[j].IdCatalog {
				found = true
				break
			}
		}
		if !found {
			Dbl.Create(&UserData{IdUser: uid, IdCatalog: sigui[i].ID, Val: val})
		}
	}
}

func UserDataSave(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	if reqdata.Get("uid") == "" || reqdata.Get("eti") == "" {
		util.RespondError(w, 400, "Datos incorrectos o incompletos")
		return
	}
	uid, err := strconv.ParseUint(reqdata.Get("uid"), 10, 64)
	if err != nil {
		util.RespondError(w, 400, "UID inválido")
		return
	}
	var user User
	Dbl.First(&user, uid)
	if user.ID == 0 {
		util.RespondError(w, 400, "Usuario no encontrado")
		return
	}
	var udata UserData
	Dbl.Joins("JOIN catalogs c on c.id = user_data.id_catalog").
		Where("id_user = ? AND c.cat = 'userData' AND c.val = ?", reqdata.Get("uid"), reqdata.Get("eti")).First(&udata)
	if udata.IdUser != 0 {
		udata.Val = reqdata.Get("val")
		Dbl.Model(&UserData{}).Where("id_user = ? and id_catalog = ?", udata.IdUser, udata.IdCatalog).Update("val", reqdata.Get("val"))
	} else {
		var cat Catalogs
		Dbl.Where("cat = 'userData' AND val = ?", reqdata.Get("eti")).First(&cat)
		if cat.ID == 0 {
			util.RespondError(w, 400, "Valor no encontrado")
			return
		}
		error := Dbl.Create(&UserData{IdUser: uint(uid), IdCatalog: cat.ID, Val: reqdata.Get("val")}).Error
		if error != nil {
			util.RespondError(w, 500, error.Error())
			return
		}
	}

	util.RespondJSON(w, 200, user.Name+" "+user.Last+" "+reqdata.Get("eti")+" "+reqdata.Get("val"))
}

func GetPermisos(retuser *UserConPer) {
	Dbl.Raw("SELECT c.val from catalogs c left join user_data ud on ud.id_catalog = c.id "+
		"where c.cat = 'permiso' and ud.id_user = ? AND ud.val = 1 order by c.val", retuser.ID).Pluck("val", &retuser.Permiso)
	Dbl.Raw("SELECT c.val from catalogs c left join user_data ud on ud.id_catalog = c.id "+
		"where c.cat = 'permisoRepo' and ud.id_user = ? AND ud.val = 1 order by c.val", retuser.ID).Pluck("val", &retuser.PermisoRepo)
	Dbl.Raw("SELECT c.val from catalogs c left join user_data ud on ud.id_catalog = c.id "+
		"where c.cat = 'permisoEsp' and ud.id_user = ? AND ud.val = 1 order by c.val", retuser.ID).Pluck("val", &retuser.PermisoEsp)
	Dbl.Raw("SELECT c.val from catalogs c left join user_data ud on ud.id_catalog = c.id "+
		"where c.cat = 'permisoSec' and ud.id_user = ? AND ud.val = 1 order by c.val", retuser.ID).Pluck("val", &retuser.PermisoSec)
}

func IntGetUserFull(uid interface{}) (retuser UserFull) {
	Dbl.First(&retuser, uid)

	return
}

func IntGetUserDataReg(uid, cual string) (resp UserDataReg) {
	Dbl.Raw("SELECT c.cat, c.val eti, ud.val FROM user_data ud JOIN catalogs c on c.id = ud.id_catalog "+
		"WHERE c.cat = 'userData' AND ud.id_user = ? AND c.val = ?", uid, cual).Scan(&resp)

	return
}

// IntGetRelUsers (interna) devuelve los usuarios relacionados por campañas cruzadas
func IntGetRelUsers(uid interface{}) (retuser map[string]UserFull) {
	esteuser := IntGetUserFull(uid)
	var users []UserFull
	Dbl.Raw("SELECT * FROM user_full WHERE id <> ? AND active = 1", uid).Scan(&users)
	retuser = make(map[string]UserFull)
	for i := range users {
		if util.ComaArraysX(esteuser.Campanas, users[i].Campanas) {
			retuser[strconv.Itoa(int(users[i].ID))] = users[i]
		}
	}

	return
}

func IntGetUserFromJSON(jsonStr string) (ret UserFull) {
	json.Unmarshal([]byte(jsonStr), &ret)

	return
}

// UsuariosConCampana devuelve los usuarios que tienen la campaña con id cid asignada
// se puede filtrar por perfil y por activos (triactive 0 inactivos, 1, activos default, 2 todos)
func UsuariosConCampana(cid uint, perfil string, triactive string) []UserParaCat {
	users := make([]UserParaCat, 0)
	if triactive == "2" {
		triactive = ""
	} else if triactive == "0" {
		triactive = "AND active = 0"
	} else {
		triactive = "AND active = 1"
	}
	query := Dbl.Table("user_full").Select("id, name, last, email, tel, perfil, active")
	query = query.Where("FIND_IN_SET(?, campanas) "+triactive, cid)
	if perfil != "" {
		perfil = strings.ReplaceAll(perfil, " ", "")
		query = query.Where("perfil IN ?", strings.Split(perfil, ","))
	}
	query.Scan(&users)

	return users
}

// Agentes y usuarios en línea que pertenecen a una campaña específica con id cid
func UsuariosEnLineaDeCampana(cid uint) (users []UserFull) {
	Dbl.Raw(`SELECT uf.* FROM user_full uf LEFT JOIN ses_ab s ON s.uid = uf.id
      WHERE FIND_IN_SET(?, campanas) AND s.uid IS NOT NULL AND
      (uf.perfil = 'agente' OR uf.perfil = 'supervisor')`, cid).Scan(&users)

	return users
}

func GetUserFullByIdOrEmail(uid uint, email string) (ret UserFull) {
	if uid != 0 {
		Dbl.First(&ret, uid)
	} else {
		Dbl.Where("email = ?", email).First(&ret)
	}

	return
}
