package ct

import (
	"net/http"
	"phonex/bago/mo"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

// FormList listado de formularios
func FormList(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	losforms := mo.FormList(mo.IntGetUserFromJSON(p.ByName("ru")), 0)

	util.RespondJSON(w, 200, losforms)
}
