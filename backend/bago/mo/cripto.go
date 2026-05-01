package mo

import (
	"net/http"

	"phonex/bago/forms"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

func CriptoEsconde(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	// Encriptar el texto
	encodedResult, err := util.Esconde(reqdata.Get("palabra"), reqdata.Get("sal"))
	if err != nil {
		util.RespondError(w, 500, err.Error())
		return
	}
	// Responder al cliente
	util.RespondJSON(w, 200, map[string]string{"result": encodedResult})
}

func CriptoEncuentra(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	// Desencriptar el texto
	decodedResult, err := util.Encuentra(reqdata.Get("hash"), reqdata.Get("sal"))
	if err != nil {
		util.RespondError(w, 500, err.Error())
		return
	}
	// Responder al cliente
	util.RespondJSON(w, 200, map[string]string{"result": decodedResult})
}
