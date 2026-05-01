package ct

import (
	"fmt"
	"log"
	"net/http"
	"phonex/bago/forms"
	"phonex/bago/mo"
	"phonex/bago/util"
	"time"

	"github.com/julienschmidt/httprouter"
)

// aChat Proceso de peticiones desde websocket
func aChat(data map[string]interface{}) {
	wsid := data["wsid"].(string)
	switch data["a"].(string) {
	case "chatreg": // cid, chat instance id requerido
		res := map[string]interface{}{"a": "error", "msg": "Temporalemente fuera de servicio!"}
		ins := mo.GetChatInstance(data["ins"].(string))
		if ins.ID == "" {
			wsEnviar(wsid, res)
			return
		}
		sesion := mo.GetChatSesion(data, ins)
		if sesion.ID == 0 {
			return
		}
		res["ses"] = sesion
		delete(res, "msg")
		if data["acsid"].(float64) > 0 {
			res["a"] = "updses"
		} else {
			res["a"] = "bas"
			res["dfs"] = mo.GetChatInstanceDefs(ins)
		}
		wsEnviar(wsid, res)
		if sesion.IdUser != nil {
			chatSesEnvAgente(sesion, *sesion.IdUser)
		}
	case "iniciar":
		mo.ChatAgenteInicia(data["acsid"].(float64))
	case "chat":
		if data["acsid"].(float64) == 0 {
			ins := mo.GetChatInstance(data["ins"].(string))
			ses := mo.GetChatSesion(data, ins)
			data["acsid"] = ses.ID
			wsEnviar(wsid, map[string]interface{}{"a": "updses", "ses": ses})
			if ses.IdUser != nil {
				chatSesEnvAgente(ses, *ses.IdUser)
			}
		}
		to := mo.ChatAddMsg(data)
		wsEnviar(to, map[string]interface{}{"a": "achat", "b": "chat", "msg": data["msg"], "acsid": data["acsid"]})
	case "terminar":
		ses := mo.ChatTerminar(data)
		log.Println("ChatTerminar", data["msg"])
		if data["msg"] == "0" {
			wsEnviar(ses.IdWs, map[string]interface{}{"a": "achat", "b": "fin", "acsid": data["acsid"]})
		} else {
			ses := mo.ChatTransferir(ses, util.Str2Uint(data["msg"].(string)))
			wsEnviar(ses.IdWs, map[string]interface{}{"a": "updses", "ses": ses})
		}
		wsEnviar(fmt.Sprint(*ses.IdUser), map[string]interface{}{"a": "achat", "b": "fin", "acsid": data["acsid"]})
	default:
		wsEnviar(wsid, map[string]interface{}{"a": "error", "msg": "Error desconocido"})
	}
}

// chatAsignar Asigna chats en Cola a los agentes, proceso por cron
func chatAsignar() {
	esperando := mo.GetChatEsperando()
out:
	for _, ses := range esperando {
		if _, ok := wsClients[ses.IdWs]; ok {
			ins := mo.GetChatInstance(ses.IdChatInstance)
			uid := mo.IntGetAgentePara("chat", fmt.Sprint(ins.IdCampaign))
			if uid > 0 {
				chatSesEnvAgente(ses, uint(uid))
				wsEnviar(ses.IdWs, map[string]interface{}{"a": "updses", "ses": ses})
			} else {
				// No hay agentes disponibles
				break out
			}
		} else {
			// El cliente se desconectó
			mo.ChatAbandonar(ses)
		}
	}
}

// IntChatAsignar Envía asignación de chat a un agente y lo pone en Asignado
func chatSesEnvAgente(ses mo.ChatSession, uid uint) {
	if ses.Status == "Cola" {
		ahorita := time.Now()
		ses.Assign = &ahorita
		ses.IdUser = &uid
		ses.Status = "Asignado"
		mo.Dbl.Save(&ses)
	}
	wsEnviar(fmt.Sprint(uid), map[string]interface{}{"a": "achat", "b": "ses", "ses": ses})
}

/* --- Funciones API --- */

// ChatList Lista de chats
func ChatList(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
}

// ChatAdd Agrega un registro a ChatInstance
func ChatAdd(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	ins := mo.AddChatInstance(reqdata, p.ByName("ru"))
	if ins.ID == "" {
		util.RespondError(w, 400, "Error al crear ChatInstance")
		return
	}
	util.RespondJSON(w, 200, ins)
}
