package ct

import (
	"crypto/tls"
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"
	"strconv"
	"time"

	"phonex/bago/forms"
	"phonex/bago/mo"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

type responsedata struct {
	Success bool   `json:"success"`
	Message string `json:"message"`
}

type evack struct {
	Event, Token, UID, MuID, CuID, Ack string
}

type evcontact struct {
	UID  string `json:"uid"`
	Name string `json:"name"`
	Type string `json:"type"`
}
type nomsgev struct {
	Event   string `json:"event"`
	Token   string `json:"token"`
	UID     string `json:"uid"`
	Contact evcontact
}
type nobodymsg struct {
	Dtm  string `json:"dtm"`
	UID  string `json:"uid"`
	Cuid string `json:"cuid"`
	Dir  string `json:"dir"`
	Type string `json:"type"`
	Ack  string `json:"ack"`
}
type evchatbody struct {
	Text string `json:"text"`
}
type evlocbody struct {
	Name  string `json:"name"`
	Lng   string `json:"lng"`
	Lat   string `json:"lat"`
	Thumb string `json:"thumb"`
	URL   string `json:"url"`
}
type evaudiobody struct {
	Caption  string `json:"caption"`
	Mimetype string `json:"mimetype"`
	Size     string `json:"size"`
	Duration string `json:"duration"`
	URL      string `json:"url"`
}
type evfilebody struct {
	Caption  string `json:"caption"`
	Mimetype string `json:"mimetype"`
	Size     string `json:"size"`
	Thumb    string `json:"thumb"`
	URL      string `json:"url"`
}
type evvideobody struct {
	Caption  string `json:"caption"`
	Mimetype string `json:"mimetype"`
	Size     string `json:"size"`
	Duration string `json:"duration"`
	Thumb    string `json:"thumb"`
	URL      string `json:"url"`
}
type evvcardbody struct {
	Contact string `json:"contact"`
	Vcard   string `json:"vcard"`
}
type evchatmsg struct {
	nobodymsg
	Body evchatbody
}
type evlocmsg struct {
	nobodymsg
	Body evlocbody
}
type evaudiomsg struct {
	nobodymsg
	Body evaudiobody
}
type evfilemsg struct {
	nobodymsg
	Body evfilebody
}
type evvideomsg struct {
	nobodymsg
	Body evvideobody
}
type evvcardmsg struct {
	nobodymsg
	Body evvcardbody
}
type evChat struct {
	nomsgev
	Message evchatmsg
}
type evLocation struct {
	nomsgev
	Message evlocmsg
}
type evAudio struct {
	nomsgev
	Message evaudiomsg
}
type evFile struct {
	nomsgev
	Message evfilemsg
}
type evVideo struct {
	nomsgev
	Message evvideomsg
}
type evVcard struct {
	nomsgev
	Message evvcardmsg
}

// Wabox POST ruta primaria para recibir whatsapps desde wabox
func Wabox(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqData, err := forms.Parse(r)
	util.CheckErr(err)
	val := reqData.Validator()
	val.Require("token")
	if val.HasErrors() {
		log.Println(val.Messages())
		util.RespondError(w, 400, "Datos incorrectos o incompletos")
		return
	}
	trytoken := reqData.Get("token")
	var token []string
	mo.Dbl.Raw("SELECT val FROM catalogs WHERE cat='whatsapp' and eti='token'").Pluck("val", &token)
	if trytoken != token[0] {
		util.RespondError(w, 401, "Token no válido")
		return
	}
	respuesta := responsedata{Success: true, Message: "Datos recibidos"}
	evev := reqData.Get("event")
	var wacta mo.WhatsappCuentas
	var wacon mo.WhatsappContact
	var waent mo.WhatsappEntry
	var finaljson []byte
	if evev == "ack" {
		final := evack{
			Token: trytoken,
			Event: evev,
			UID:   reqData.Get("uid"),
			MuID:  reqData.Get("muid"),
			CuID:  reqData.Get("cuid"),
			Ack:   reqData.Get("ack"),
		}
		finaljson, err = json.Marshal(final)
		util.CheckErr(err)
	} else {
		var evento nomsgev
		evento.Event = evev
		evento.Token = trytoken
		evento.UID = reqData.Get("uid")
		evento.Contact.Name = reqData.Get("contact[name]")
		evento.Contact.Type = reqData.Get("contact[type]")
		evento.Contact.UID = reqData.Get("contact[uid]")
		mo.Dbl.Where("cuenta = ?", evento.UID).Find(&wacta)
		if wacta.ID == 0 {
			finaljson, err = json.Marshal(reqData.Values)
			util.CheckErr(err)
			guardabox(finaljson)
			util.RespondError(w, 400, "Datos incorrectos o incompletos")
			return
		}
		mo.Dbl.Where("account = ?", evento.Contact.UID).Find(&wacon)
		if wacon.ID == 0 {
			wacon.Account = evento.Contact.UID
			wacon.DatetimeRegister = time.Now().In(mo.Local)
			wacon.IdWacta = wacta.ID
			wacon.Name = evento.Contact.Name
			if wacon.Name == "" {
				wacon.Name = evento.Contact.UID
			}
			wacon.LastAsignedTo = nil
			mo.Dbl.Save(&wacon)
		}
		waent.DatetimeReceived = time.Now().In(mo.Local)
		waent.IdContact = wacon.ID
		waent.IdWacta = wacta.ID
		var mensaje nobodymsg
		mensaje.Dtm = reqData.Get("message[dtm]")
		mensaje.UID = reqData.Get("message[uid]")
		mensaje.Cuid = reqData.Get("message[cuid]")
		mensaje.Dir = reqData.Get("message[dir]")
		mensaje.Ack = reqData.Get("message[ack]")
		mensaje.Type = reqData.Get("message[type]")
		waent.Type = "Entrante"
		waent.Status = "Recibido"
		waent.Watype = mensaje.Type
		switch mensaje.Type {
		case "location":
			var body evlocbody
			body.Name = reqData.Get("message[body][name]")
			body.Lng = reqData.Get("message[body][lng]")
			body.Lat = reqData.Get("message[body][lat]")
			body.Thumb = reqData.Get("message[body][thumb]")
			body.URL = reqData.Get("message[body][url]")
			waent.Caption = body.Name
			waent.Lng, _ = strconv.ParseFloat(body.Lng, 64)
			waent.Lat, _ = strconv.ParseFloat(body.Lat, 64)
			waent.Thumb = body.Thumb
			waent.URL = body.URL
			msgfull := evlocmsg{
				nobodymsg: mensaje,
				Body:      body,
			}
			waent.JSON, _ = json.Marshal(msgfull)
			final := evLocation{
				nomsgev: evento,
				Message: msgfull,
			}
			finaljson, err = json.Marshal(final)
			util.CheckErr(err)
		case "image", "document":
			var body evfilebody
			body.Caption = reqData.Get("message[body][caption]")
			body.Mimetype = reqData.Get("message[body][mimetype]")
			body.Size = reqData.Get("message[body][size]")
			body.Thumb = reqData.Get("message[body][thumb]")
			body.URL = reqData.Get("message[body][url]")
			waent.Caption = body.Caption
			waent.Mimetype = body.Mimetype
			waent.Size, _ = strconv.Atoi(body.Size)
			waent.Thumb = body.Thumb
			waent.URL = body.URL
			msgfull := evfilemsg{
				nobodymsg: mensaje,
				Body:      body,
			}
			waent.JSON, _ = json.Marshal(msgfull)
			final := evFile{
				nomsgev: evento,
				Message: msgfull,
			}
			finaljson, err = json.Marshal(final)
			util.CheckErr(err)
		case "audio", "ptt":
			var body evaudiobody
			body.Caption = reqData.Get("message[body][caption]")
			body.Mimetype = reqData.Get("message[body][mimetype]")
			body.Size = reqData.Get("message[body][size]")
			body.Duration = reqData.Get("message[body][duration]")
			body.URL = reqData.Get("message[body][url]")
			waent.Caption = body.Caption
			waent.Mimetype = body.Mimetype
			waent.Size, _ = strconv.Atoi(body.Size)
			waent.Duration, _ = strconv.Atoi(body.Duration)
			waent.URL = body.URL
			msgfull := evaudiomsg{
				nobodymsg: mensaje,
				Body:      body,
			}
			waent.JSON, _ = json.Marshal(msgfull)
			final := evAudio{
				nomsgev: evento,
				Message: msgfull,
			}
			finaljson, err = json.Marshal(final)
			util.CheckErr(err)
		case "video":
			var body evvideobody
			body.Caption = reqData.Get("message[body][caption]")
			body.Mimetype = reqData.Get("message[body][mimetype]")
			body.Size = reqData.Get("message[body][size]")
			body.Thumb = reqData.Get("message[body][thumb]")
			body.Duration = reqData.Get("message[body][duration]")
			body.URL = reqData.Get("message[body][url]")
			waent.Caption = body.Caption
			waent.Mimetype = body.Mimetype
			waent.Size, _ = strconv.Atoi(body.Size)
			waent.Thumb = body.Thumb
			waent.Duration, _ = strconv.Atoi(body.Duration)
			waent.URL = body.URL
			msgfull := evvideomsg{
				nobodymsg: mensaje,
				Body:      body,
			}
			waent.JSON, _ = json.Marshal(msgfull)
			final := evVideo{
				nomsgev: evento,
				Message: msgfull,
			}
			finaljson, err = json.Marshal(final)
			util.CheckErr(err)
		case "vcard":
			var body evvcardbody
			body.Contact = reqData.Get("message[body][contact]")
			body.Vcard = reqData.Get("message[body][vcard]")
			waent.Caption = body.Contact
			waent.Message = body.Vcard
			msgfull := evvcardmsg{
				nobodymsg: mensaje,
				Body:      body,
			}
			waent.JSON, _ = json.Marshal(msgfull)
			final := evVcard{
				nomsgev: evento,
				Message: msgfull,
			}
			finaljson, err = json.Marshal(final)
			util.CheckErr(err)
		case "chat":
			var body evchatbody
			body.Text = reqData.Get("message[body][text]")
			waent.Message = body.Text
			msgfull := evchatmsg{
				nobodymsg: mensaje,
				Body:      body,
			}
			waent.JSON, _ = json.Marshal(msgfull)
			final := evChat{
				nomsgev: evento,
				Message: msgfull,
			}
			finaljson, err = json.Marshal(final)
			util.CheckErr(err)
		default:
			log.Println("Tipo desconocido de mensaje")
			finaljson, err = json.Marshal(reqData.Values)
			util.CheckErr(err)
			waent.JSON = finaljson
			respuesta.Success = false
		}
		if mensaje.Dir == "o" {
			waent.Type = "Saliente"
			waent.Status = "Confirma"
		}
		mo.Dbl.Save(&waent)
		if waent.Type == "Entrante" {
			ses := mo.GetWaSesByContact(waent.IdContact, wacta.ID)
			if ses.ID > 0 {
				idcon := fmt.Sprint(ses.IdContact)
				wsEnviar(idcon, waent)
			}
		}
	}
	guardabox(finaljson)
	util.RespondJSON(w, 201, &respuesta)
}

// WaboxServe POST ruta para re-transmitir wabox en caso de ser necesario
func WaboxServe(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqData, err := forms.Parse(r)
	util.CheckErr(err)
	val := reqData.Validator()
	val.Require("token")
	if val.HasErrors() {
		log.Println(val.Messages())
		util.RespondError(w, 400, "Datos incorrectos o incompletos")
		return
	}
	trytoken := reqData.Get("token")
	var token []string
	mo.Dbl.Raw("SELECT val FROM catalogs WHERE cat='whatsapp' and eti='token'").Pluck("val", &token)
	if trytoken != token[0] {
		util.RespondError(w, 401, "Token no válido")
		return
	}
	respuesta := responsedata{Success: true, Message: "Datos recibidos"}
	finaljson, err := json.Marshal(reqData.Values)
	util.CheckErr(err)
	var destino mo.WhatsappServe
	mo.Dbl.Where("numero = ?", reqData.Get("uid")).Find(&destino)
	var gatewaydata mo.WhatsappGateway
	if destino.IP != "" {
		url := "https://" + destino.IP + ":8443/wabox"
		transCfg := &http.Transport{
			TLSClientConfig: &tls.Config{InsecureSkipVerify: true}, // ignore expired or self signed SSL certificates
		}
		cliente := &http.Client{Transport: transCfg}
		resp, err := cliente.PostForm(url, reqData.Values)
		util.CheckErr(err)
		data, err := io.ReadAll(resp.Body)
		util.CheckErr(err)
		gatewaydata.DestIP = destino.IP
		gatewaydata.DestResp = string(data)
		gatewaydata.DestTel = destino.Numero
	}
	gatewaydata.Queviene = finaljson
	gatewaydata.Hora = time.Now().In(mo.Local)
	mo.Dbl.Save(&gatewaydata)
	util.RespondJSON(w, 201, &respuesta)
}

func guardabox(d []byte) bool {
	var elpost mo.WhatsappHooks
	elpost.DatetimeReceived = time.Now().In(mo.Local)
	elpost.JSON = d
	mo.Dbl.Save(&elpost)
	return true
}
