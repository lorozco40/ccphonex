package ct

import (
	"encoding/json"
	"log"
	"net/http"
	"strconv"
	"strings"

	"phonex/bago/mo"
	"phonex/bago/util"

	"github.com/gorilla/websocket"
	"github.com/julienschmidt/httprouter"
)

type wsClient struct {
	id     string
	name   string
	perfil string // Perfil
	cams   string // Campañas
	perci  string // Permisos juntos
	pc     bool   // Permiso de chat
	emd    bool   // Enviar mensajes a todos
	ems    bool   // Enviar mensajes a supervisor
	emu    bool   // Enviar mensajes a usuario
	rmu    bool   // Recibir mensajes de usuario
	sid    *websocket.Conn
	from   string // IP de origen
}

var wsClients = map[string]wsClient{}

var upgrader = websocket.Upgrader{
	ReadBufferSize:  1024,
	WriteBufferSize: 1024,
	CheckOrigin: func(r *http.Request) bool {
		return true
	},
}

// WsCon manejador principal de la conexión ws
func WsCon(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
	conn, err := upgrader.Upgrade(w, r, nil)
	util.CheckErr(err)
	uid := util.GeneraToken(998899)
	wsClients[uid] = wsClient{id: uid, name: "Anonimo", sid: conn, from: conn.RemoteAddr().String()}
	// log.Printf("\x1b[36m"+"ws.go, 49: %+v\n"+"\x1b[0m", wsClients)
	reader(conn)
}

func reader(conn *websocket.Conn) {
	for {
		// Read message from browser
		msgType, msg, err := conn.ReadMessage()
		util.CheckErr(err)
		uid := getWsClientId(conn)
		if msgType == -1 {
			// Desconexion
			notifUsrUpdate(uid, false)
			delete(wsClients, uid)
			conn.Close()
			break
		}
		if msgType == 1 {
			// Evento
			logger(uid, msg)
			fin := util.InJSON(msg)
			if fin["acsid"] != nil { // Mensajes de Assertive chat sesión id
				fin["wsid"] = getWsClientId(conn)
				aChat(fin)
			} else if fin["a"] == nil {
				wsEnviar(uid, map[string]interface{}{"a": "error", "msg": "Sin tipo de evento"})
			} else {
				switch fin["a"].(string) {
				case "reg":
					wsRegistrar(conn, fin)
				case "chat":
					wsEnviaChat(uid, util.IfaceToUint(fin["to"]), fin["msg"].(string))
				case "read":
					mo.IntChatMarkRead(util.IfaceToUint(fin["id"]), util.Str2Uint(uid))
				case "conv":
					wsEnviaConv(uid, fin)
				default:
					wsEnviar(uid, map[string]interface{}{"a": "error", "msg": "Tipo de evento desconocido"})
				}
			}
		}
	}
}

// Enviar es para notificar por id a alguno de los clientes conectados, si es que está conectado
func wsEnviar(uid string, waent interface{}) bool {
	if val, ok := wsClients[uid]; ok {
		data, err := json.Marshal(waent)
		util.CheckErr(err)
		err = val.sid.WriteMessage(1, data)
		if err == nil {
			return true
		}
	}

	return false
}

/**
 * wsEnviaChat Envía un mensaje de chat
 * @param uid El id del usuario que envía el mensaje
 * @param para El id del usuario que recibe el mensaje o 0 para todos
 * @param msg El mensaje
 * @return true si el mensaje fue enviado, false si no
 */
func wsEnviaChat(uid string, para uint, msg string) bool {
	de := wsClients[uid]
	if !de.pc { // Si no tiene permiso de chat
		wsEnviar(uid, map[string]interface{}{"a": "error", "msg": "Sin permiso de chat"})
		return false
	}
	if para == 0 && de.emd {
		ent := mo.IntChatSaveMsg(util.Str2Uint(uid), para, msg)
		for key, val := range wsClients {
			if util.ComaArraysX(val.cams, de.cams) {
				wsEnviar(key, map[string]interface{}{"a": "chat", "ents": ent})
			}
		}
		return true
	} else {
		to := mo.IntGetUserFull(para)
		tp := permBooleanos(to.Perci)
		// el usuario origen puede enviar el tipo de mensaje
		p1 := (to.Perfil != "agente" && de.ems) || (to.Perfil == "agente" && de.emu)
		// el usuario destino puede recibir el tipo de mensaje
		p2 := de.perfil != "agente" || tp[4]
		// ambos usuarios coinciden en al menos una campaña
		p3 := util.ComaArraysX(de.cams, to.Campanas)
		if p1 && p2 && p3 {
			ent := mo.IntChatSaveMsg(util.Str2Uint(uid), para, msg)
			wsEnviar(uid, map[string]interface{}{"a": "chat", "ents": ent})
			if _, ok := wsClients[strconv.Itoa(int(para))]; ok {
				wsEnviar(strconv.Itoa(int(para)), map[string]interface{}{"a": "chat", "ents": ent})
				mo.IntChatMarkDelivery(uid, para)
			}
			return true
		}
	}

	return false
}

func getWsClientId(conn *websocket.Conn) (uid string) {
	for key, val := range wsClients {
		if val.sid == conn {
			uid = key
		}
	}

	return
}

func notifUsrUpdate(uid string, online bool) {
	for key, val := range wsClients {
		if key != uid && util.ComaArraysX(val.cams, wsClients[uid].cams) {
			user := map[string]interface{}{"id": uid, "perci": wsClients[uid].perci,
				"nombre": wsClients[uid].name, "perfil": wsClients[uid].perfil, "online": online}
			wsEnviar(key, map[string]interface{}{"a": "updusr", "id": uid, "user": user})
		}
	}
}

func enviaBasicos(uid string) {
	relusers := mo.IntGetRelUsers(uid)
	rel := make(map[uint]interface{}, len(relusers))
	for key, val := range relusers {
		var esteuser = map[string]interface{}{"id": key, "nombre": val.Name + " " + val.Last, "perfil": val.Perfil, "perci": val.Perci, "online": false}
		if _, ok := wsClients[key]; ok {
			esteuser["online"] = true
		}
		rel[val.ID] = esteuser
	}
	wsEnviar(uid, map[string]interface{}{"a": "bas", "perm": wsClients[uid].perci, "rel": rel})
	tents := mo.TicketAbiertosXAgente(uid, wsClients[uid].cams)
	if len(tents) > 0 {
		wsEnviar(uid, map[string]interface{}{"a": "ticket", "ents": tents})
	}
	cents := mo.IntChatGetUnreadMsgs(uid)
	if len(cents) > 0 {
		wsEnviar(uid, map[string]interface{}{"a": "chat", "ents": cents})
	}
	achatpend := mo.GetChatPendientes(uid)
	for _, v := range achatpend {
		if _, ok := wsClients[v.IdWs]; ok {
			wsEnviar(uid, map[string]interface{}{"a": "achat", "b": "previo", "ses": v})
		} else {
			mo.ChatAbandonar(v)
		}
	}
}

// wsRegistrar es para cambiar el id de la conexión ws por el id real del usuario
func wsRegistrar(conn *websocket.Conn, fin map[string]interface{}) {
	if fin["id"] != nil {
		// La conexión con el ws ya esta establecida con un id aleatorio único
		tmpuid := getWsClientId(conn)
		nuid := fin["id"].(string)
		sisuser := mo.IntGetUserFull(nuid)
		p := permBooleanos(sisuser.Perci)
		wsClients[nuid] = wsClient{
			id: nuid, name: sisuser.Name + " " + sisuser.Last, perfil: sisuser.Perfil, cams: sisuser.Campanas, perci: sisuser.Perci,
			pc: p[0], emd: p[1], ems: p[2], emu: p[3], rmu: p[4], sid: conn, from: conn.RemoteAddr().String(),
		}
		// log.Printf("\x1b[36m"+"ws.go, 187: %+v\n"+"\x1b[0m", wsClients)
		delete(wsClients, tmpuid)
		notifUsrUpdate(nuid, true)
		enviaBasicos(nuid)
	}
}

func wsEnviaConv(uid string, fin map[string]interface{}) {
	ents := mo.IntChatGetMsgs(fin["to"], uid, fin["mid"])
	wsEnviar(uid, map[string]interface{}{"a": "chat", "ents": ents})
}

func permBooleanos(p string) (r []bool) {
	i := strings.Split(p, ",")
	for _, val := range i {
		r = append(r, val == "1")
	}

	return
}

func logger(uid string, msg []byte) {
	log.Printf("\x1b[33mws %s: %s\n\x1b[0m", uid, string(msg))
}
