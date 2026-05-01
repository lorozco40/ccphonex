package ct

import (
	"fmt"
	"net/http"
	"phonex/bago/mo"
	"phonex/bago/util"
	"strconv"
	"time"

	"github.com/julienschmidt/httprouter"
)

func UserLoginfo(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	type Ret struct {
		Since  time.Duration
		SinceH string
		For    int
		Van    time.Duration
		VanH   string
	}
	var ret []Ret
	for _, v := range mo.LoggedUsers {
		var et Ret
		et.Since = time.Since(v.Ini)
		et.SinceH = fmt.Sprintf("duration: %s", et.Since)
		et.For = v.For
		et.Van = time.Hour * time.Duration(v.For)
		et.VanH = fmt.Sprintf("duration: %s", et.Van)
		ret = append(ret, et)
	}

	util.RespondJSON(w, 200, ret)
}

func UserUnlog(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	uiid, _ := strconv.ParseUint(p.ByName("id"), 10, 32)
	uid := uint(uiid)
	if _, ok := mo.LoggedUsers[uid]; ok {
		delete(mo.LoggedUsers, uid)
		util.RespondJSON(w, 200, "Sesión cerrada")
		return
	}

	util.RespondError(w, 404, "Usuario no encontrado")
}
