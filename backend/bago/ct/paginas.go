package ct

import (
	"fmt"
	"html/template"
	"net/http"

	"phonex/bago/forms"
	"phonex/bago/mo"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

// Index es la página inicial principal
func Index(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
	t, err := template.ParseFiles("vistas/restdocu.html")
	if err != nil {
		fmt.Fprintf(w, "Unable to load template")
	}
	reqData, _ := forms.Parse(r)
	requid := reqData.Get("uid")
	uris := mo.IntGetEndpoints(requid)
	resp := map[string]interface{}{"Uris": uris}

	w.Header().Set("Content-Type", "text/html; charset=utf-8")
	t.Execute(w, resp)
}

// Hola solo prueba el envío de parametros httprouter
func Hola(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	nombre := p.ByName("nombre")
	respuesta := "Hola " + nombre
	util.RespondJSON(w, 200, respuesta)
}

// Bounce es un endpoint de prueba que guarda el json recibido en la base de datos y lo devuelve
func Bounce(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	reqData, _ := forms.Parse(r)
	laip := util.ReadUserIP(r)
	mo.LogAdd(mo.BagoLog{IP: laip, Evento: "bounce", Data: reqData.Encode()}, requser.ID)
	util.RespondJSON(w, 200, reqData.Values)
}
