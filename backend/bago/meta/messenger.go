package meta

import (
	"encoding/json"
	"net/http"
	"time"

	"phonex/bago/forms"
	"phonex/bago/mo"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

// MessengerLogin es la ruta para iniciar sesión con facebook messenger
func MessengerLogin(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	val := reqdata.Validator()
	val.Require("hub.mode")
	val.Require("hub.verify_token")
	val.Require("hub.challenge")
	mode := r.URL.Query().Get("hub.mode")
	token := r.URL.Query().Get("hub.verify_token")
	challenge := r.URL.Query().Get("hub.challenge")
	if val.HasErrors() || mode != "subscribe" || token != "11924e1249974ab480c76fac19f9e4dc46556c7b" {
		util.RespondError(w, 401, "UNAUTHORIZED")
		return
	}
	w.Write([]byte(challenge))
}

// MessengerWebhook recibe los mensajes de facebook messenger
func MessengerWebhook(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	// val := reqdata.Validator()
	// val.Require("token")
	// validtoken := reqdata.Get("token") == "11924e1249974ab480c76fac19f9e4dc46556c7b"
	// if val.HasErrors() || !validtoken {
	//     log.Printf("%s", val.ErrorMap())
	//     util.RespondError(w, 401, "UNAUTHORIZED")
	//     return
	// }
	var hook mo.MetaMsgrHook
	json, _ := json.Marshal(reqdata)
	hook.Msg = string(json)
	hook.Recibido = time.Now()
	mo.Dbl.Create(&hook)
	util.RespondJSON(w, 200, "EVENT_RECEIVED")
}
