package ct

import (
	"fmt"
	"log"
	"net/http"
	"phonex/bago/forms"
	"phonex/bago/mo"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

func TicketGroupList(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	util.RespondJSON(w, 200, mo.FormList(requser, 1))
}

func TicketGroupAdd(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	form := mo.FormAdd(reqdata, requser)
	// ToDo: Convertir el formulario a crm y agregar campos y tablas
	if form.ID == 0 {
		util.RespondError(w, 400, "Datos incorrectos")
	} else {
		util.RespondJSON(w, 200, form.ID)
	}
}

func TicketGroupOne(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	form := mo.FormOne(p.ByName("gid"), requser)
	if form.ID == 0 {
		util.RespondError(w, 401, "Sin permiso")
		return
	}
	form.Fields = mo.FormFieldsList(form, mo.IntGetUserFromJSON(p.ByName("ru")), "api")
	util.RespondJSON(w, 200, form)
}

func TicketGroupUpd(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	log.Println(p.ByName("ru"))
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	actualizado := mo.FormUpd(p.ByName("gid"), reqdata, requser)
	if actualizado {
		util.RespondJSON(w, 200, "ok")
	} else {
		util.RespondError(w, 400, "Datos incorrectos")
	}
}

// ToDo: Administradores sólamente, eliminar un formlario
func TicketGroupDel(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	gid := p.ByName("gid")
	mo.FormDel(gid)
	util.RespondJSON(w, 200, "ok")
}

func TicketClients(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	form := mo.FormOne(p.ByName("gid"), requser)
	if form.ID == 0 {
		util.RespondError(w, 401, "Sin permiso")
		return
	}

	util.RespondJSON(w, 200, mo.AgendaClientes(form.IdCampaign, requser, reqdata))
}

func TicketAssignables(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	form := mo.FormOne(p.ByName("gid"), requser)
	if form.ID == 0 {
		util.RespondError(w, 401, "Sin permiso")
		return
	}

	util.RespondJSON(w, 200, mo.UsuariosConCampana(form.IdCampaign, "crm,agente,supervisor", "1"))
}

func TicketCatsList(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	form := mo.FormOne(p.ByName("gid"), requser)
	if form.ID == 0 {
		util.RespondError(w, 401, "Sin permiso")
		return
	}

	util.RespondJSON(w, 200, mo.TicketCatsList(fmt.Sprint(form.ID)))
}

func TicketCatsEntsList(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	form := mo.FormOne(p.ByName("gid"), requser)
	if form.ID == 0 {
		util.RespondError(w, 401, "Sin permiso")
		return
	}

	util.RespondJSON(w, 200, mo.TicketCatsEntsList(fmt.Sprint(form.ID), p.ByName("cid")))
}

func TicketList(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	form := mo.FormOne(p.ByName("gid"), requser)
	if form.ID == 0 {
		util.RespondError(w, 401, "Sin permiso")
		return
	}
	reqdata, error := forms.Parse(r)
	util.CheckErr(error)

	util.RespondJSON(w, 200, mo.TicketList(form, requser, reqdata))
}

func TicketAdd(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	gid := p.ByName("gid")
	form := mo.FormOne(gid, requser)
	if form.ID == 0 {
		util.RespondError(w, 401, "Sin permiso")
		return
	}
	reqdata, _ := forms.Parse(r)
	res := mo.TicketAdd(gid, requser, reqdata, form)
	util.RespondJSON(w, res.Status, res.Complx)
        if res.Status == 200 {
		// Esto es para informar la creación del ticket a los que corresponde
		//var tinyuf struct {
		//	ID    string
		//	Token string
		//}
		//mo.Dbl.Raw("SELECT id, token FROM user_full WHERE token <> ''").Scan(&tinyuf)
		//data := util.ReqData{
		//	URL:    os.Getenv("BFURL") + "api/db1ee2xfiri",
		//	Method: "GET",
		//	Type:   "application/x-www-form-urlencoded",
		//	Port:   443,
		//	Body:   []byte(`{"fid":"` + gid + `","id":"` + res.Extra + `"}`),
		//	Heads:  map[string]interface{}{"u": tinyuf.ID, "token": tinyuf.Token},
		//}
		//util.DoReq(data)
		asignarTicketsAbiertos()
                mo.Dbl.Exec("INSERT INTO ticket_eventos (id_ticket, id_grupo) VALUES (?, ?)", res.Extra, gid)
	}
}

func TicketOne(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	form := mo.FormOne(p.ByName("gid"), requser)
	if form.ID == 0 {
		util.RespondError(w, 401, "Sin permiso")
		return
	}

	util.RespondJSON(w, 200, mo.TicketOne(form, p.ByName("tid"), requser))
}

func TicketUpd(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	// requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	// reqdata, _ := forms.Parse(r)
	// gid := p.ByName("gid")
	// form := mo.FormOne(gid)
	// if requser.Perfil == "admin" || (util.CampanaValida(int(form.IdCampaign), requser.Campanas) && form.Active) {
	// 	util.RespondJSON(w, 200, mo.TicketList(gid, requser, reqdata))
	// } else {
	// 	util.RespondError(w, 401, "Sin permiso")
	// }
}

func TicketDel(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	gid := p.ByName("gid")
	form := mo.FormOne(gid, requser)
	if form.ID == 0 {
		util.RespondError(w, 401, "Sin permiso")
		return
	}
	if mo.TicketDel(gid, p.ByName("tid")) {
		util.RespondJSON(w, 200, "ok")
		return
	}
	util.RespondError(w, 400, "Datos incorrectos")
}

func TicketLogs(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	gid := p.ByName("gid")
	tid := p.ByName("tid")
	form := mo.FormOne(gid, requser)
	ticket := mo.TicketOneBasic(gid, tid)
	if form.ID == 0 || ticket.ID == 0 || reqdata.Get("comentario") == "" || (reqdata.Get("estatus") == "" && reqdata.Get("id_estado") == "") {
		util.RespondError(w, 400, "Datos incorrectos")
		return
	}
	if ticket.Estatus == "Cerrado" || ticket.Estatus == "Cancelado" {
		util.RespondError(w, 406, "Ticket terminado")
		return
	}
	if mo.TicketLogsAdd(gid, tid, requser, reqdata) {
		util.RespondJSON(w, 200, "ok")
		return
	}

	util.RespondError(w, 400, "Datos incorrectos")
}

func asignarTicketsAbiertos() {
	grupos := mo.TicketGroupAssignableList()
	// Para cada grupo asignable encontrar todos los tickets con estatus abierto y asignar uno a cada agente
	// que esté en línea únicamente
	for _, grupo := range grupos {
		agentes := mo.UsuariosEnLineaDeCampana(grupo.IdCampaign)
		if len(agentes) == 0 {
			continue
		}
		tickets := mo.TicketAbiertosNoAsignados(grupo.ID)
		if len(tickets) == 0 {
			continue
		}
		asignados := mo.TicketMaxAsignadosXFecha(grupo.ID)
		cola := []mo.UserFull{}
		for _, asig := range asignados {
			for _, agente := range agentes {
				if agente.ID == *asig.AsignarA {
					cola = append(cola, agente)
				}
			}
		}
		for _, agente := range agentes {
			agregar := true
			for _, agteEnCola := range cola {
				if agente.ID == agteEnCola.ID {
					agregar = false
				}
			}
			if agregar {
				cola = append([]mo.UserFull{agente}, cola...)
			}
		}
		for _, ticket := range tickets {
			if len(cola) < 1 {
				break
			}
			ticket := mo.TicketAsignaAgente(ticket, cola[0], grupo.ID)
			wsEnviar(fmt.Sprint(cola[0].ID), map[string]interface{}{"a": "ticket", "ents": ticket})
			// Asigno un sólo registro por agente en éste minuto quitándolo de la cola
			cola = cola[1:]
		}
	}

}


// Función para recibir archivos para los tickets
func TicketFileAdd(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	gid := p.ByName("gid")
	reqdata.Set("gid", gid)
	reqdata.Set("id", p.ByName("tid"))
	form := mo.FormOne(gid, requser)
	if form.ID == 0 {
		util.RespondError(w, 401, "Sin permiso")
		return
	}
	res := mo.TicketFileAdd(requser, reqdata, form)
	util.RespondJSON(w, res.Status, res.Sinstr)
}
