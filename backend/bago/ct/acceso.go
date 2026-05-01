package ct

import (
	"net/http"
	"strconv"
	"time"

	"phonex/bago/forms"
	"phonex/bago/mo"
	"phonex/bago/util"

	"github.com/golang-jwt/jwt/v5"
	"github.com/julienschmidt/httprouter"
)

type userLogedInfo struct {
	ID      uint
	Email   string
	Name    string
	Last    string
	LastReq time.Time
	IP      string
	Ini     time.Time
}

// AccesoLogin inicia la sesión del usuario
func AccesoLogin(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	var valuser mo.User
	mo.Dbl.Where("user = ?", reqdata.Get("email")).Find(&valuser)
	if _, ok := mo.LoggedUsers[valuser.ID]; ok {
		util.RespondError(w, 403, "Usuario previamente autenticado")
		return
	}
	if valuser.Active && util.Compara(reqdata.Get("pass"), valuser.Pass, valuser.User) {
		var retuser mo.UserConPer
		mo.Dbl.Raw("SELECT * FROM user_full WHERE id = ?", valuser.ID).Scan(&retuser.UserFull)
		if retuser.Token == "" {
			retuser.Token = util.GeneraToken(retuser.ID)
			mo.SaveToken(retuser.Token, retuser.ID)
		}
		mo.ValidaCats(retuser.ID)
		seskey, e := util.Esconde(retuser.Token, valuser.User)
		util.CheckErr(e)
		if e != nil {
			util.RespondError(w, 500, "Error interno 3N")
			return
		}
		mo.GetPermisos(&retuser)
		mo.LoggedUsers[retuser.ID] = mo.LogedUser{
			UserConPer: retuser,
			SesKey:     seskey,
			LastReq:    time.Now().In(mo.Local),
			IP:         util.ReadUserIP(r),
			Ini:        time.Now().In(mo.Local),
			For:        12,
		}

		util.RespondJSON(w, 200, seskey)
		return
	}
	util.RespondError(w, 401, "Credenciales incorrectas")
}

// AccesoLogout cierra la sesión del usuario
func AccesoLogout(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	if _, ok := mo.LoggedUsers[requser.ID]; !ok {
		util.RespondError(w, 401, "Usuario desconocido")
		return
	}
	delete(mo.LoggedUsers, requser.ID)
	util.RespondJSON(w, 200, "Sesión cerrada")
}

func AccesoLogoutAlt(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	var valuser mo.User
	mo.Dbl.Where("user = ?", reqdata.Get("email")).Find(&valuser)
	if _, ok := mo.LoggedUsers[valuser.ID]; ok {
		if valuser.Active && util.Compara(reqdata.Get("pass"), valuser.Pass, valuser.User) {
			delete(mo.LoggedUsers, valuser.ID)
			util.RespondJSON(w, 200, "Sesión cerrada")
			return
		}
	}
	util.RespondError(w, 404, "Recurso no encontrado")
}

// AccesoLoged devuelve los usuarios logeados
func AccesoLogged(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
	ret := make(map[uint]userLogedInfo)
	for k, v := range mo.LoggedUsers {
		ret[k] = userLogedInfo{v.ID, v.Email, v.Name, v.Last, v.LastReq, v.IP, v.Ini}
	}
	util.RespondJSON(w, 200, mo.LoggedUsers)
}

func GeneraJWT(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	// Duración en horas, si no se especifica, 1 hora
	exp, _ := strconv.Atoi(util.Tif(reqdata.Get("exp") != "", reqdata.Get("exp"), "1"))
	exp = util.Tif(exp != 0, exp, 1)
	tokVals := jwt.MapClaims{
		"iss": util.Tif(reqdata.Get("iss") != "", reqdata.Get("iss"), "assertivebusiness.com"),
		"sub": util.Tif(reqdata.Get("sub") != "", reqdata.Get("sub"), "Assertive API's"),
		"aud": util.Tif(reqdata.Get("aud") != "", reqdata.Get("aud"), "Assertive licensed users"),
		"exp": time.Now().Add(time.Hour * time.Duration(exp)).Unix(),
	}
	for ind, val := range reqdata.Values {
		if !util.InComaArray(ind, "pk,iss,sub,aud,exp") {
			tokVals[ind] = val[0]
		}
	}
	token, err := util.GeneraJWT(reqdata.Get("pk"), tokVals)
	if err != nil {
		util.RespondError(w, 500, err.Error())
		return
	}

	util.RespondJSON(w, 200, token)
}

func ValidaJWT(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	avalidar := reqdata.Get("jwt")
	if avalidar == "" {
		util.RespondError(w, 400, "Falló la validación")
		return
	}
	_, err := util.ObtenerClaims(reqdata.Get("pk"), avalidar)
	if err != nil {
		util.RespondError(w, 400, "Falló la validación")
		return
	}
	util.RespondJSON(w, 200, "ok")
}
