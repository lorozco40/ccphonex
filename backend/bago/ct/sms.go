package ct

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"strings"
	"time"

	"phonex/bago/forms"
	"phonex/bago/mo"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

// SmsLista obtiene la lista de SMS, permite parametros de filtro: rango de fechas, página, cantidad de registros por página
func SmsLista(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	var target []mo.SmsEntry
	filtros := mo.FilterParams{
		Uid:        reqdata.Get("uid"),
		Cid:        reqdata.Get("cid"),
		Model:      &mo.SmsEntry{},
		Target:     target,
		CampoFecha: "datetime_init",
		DateFr:     reqdata.Get("desde"),
		DateTo:     reqdata.Get("hasta"),
		Page:       reqdata.Get("pag"),
		Rpp:        reqdata.Get("rpp"),
		Other:      reqdata.Get("of"),
		OVal:       reqdata.Get("ov"),
	}

	util.RespondJSON(w, 200, mo.GetFilteredData(filtros, requser))
}

// SmsDetalle obtiene el detalle de un SMS
func SmsDetalle(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	id := p.ByName("id")
	var sms mo.SmsEntry
	mo.Dbl.First(&sms, id)
	if util.InComaArray(fmt.Sprint(sms.IdCampaign), requser.Campanas) {
		util.RespondJSON(w, 200, sms)
	} else {
		util.RespondError(w, 403, "No autorizado")
	}
}

// SmsEnviar envía un SMS
func SmsEnviar(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := mo.IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	// Si requser tiene una sola campaña se usa esa, si no, se usa la campaña que se envía y si no hay se solicita cid
	cid := requser.Campanas
	if strings.Contains(cid, ",") {
		cid = reqdata.Get("cid")
		if cid == "" {
			util.RespondError(w, 400, "Usuario multicampaña, por favor envía parámetro cid (id campaña)")
			return
		}
	}
	if !util.IsNumeric(cid) || !util.InComaArray(cid, requser.Campanas) {
		util.RespondError(w, 400, "Campaña incorrecta")
		return
	}
	proveedor := "Directo"
	tipo := "sms"
	if reqdata.Get("sl") == "l" {
		proveedor = "Ipcom"
		tipo = "gsm"
	}
	medio := mo.IntGetExtapi(proveedor, cid)
	if medio.ID == 0 {
		util.RespondError(w, 503, "Temporalmente no disponible, consulta con tu ejecutivo")
	}
	numeros := mo.SmsGetArrayTelefonos(reqdata.Get("cel"), "52")
	from := util.Tif(reqdata.Get("de") != "", reqdata.Get("de"), "sms")
	msg := util.Slugify(reqdata.Get("msg"), tipo)
	entrada := mo.SmsEntry{
		IdUser:     uint(requser.ID),
		IdCampaign: util.Str2Uint(cid),
		Msg:        msg,
		Operator:   from,
		StatusDesc: "Enviado",
		Type:       "Saliente",
	}
	if len(msg) < 1 || len(msg) > 160 {
		for _, v := range numeros {
			entrada.Phone = v
			entrada.StatusDesc = "Error"
			entrada.DatetimeInit = time.Now()
			mo.Dbl.Create(&entrada)
		}
		util.RespondError(w, 422, "Mensaje incorrecto, 1 a 160 caracteres válidos")
		return
	}
	client := &http.Client{}
	req := &http.Request{}
	if len(numeros) < 1 || len(numeros) > 50 {
		util.RespondError(w, 400, "Datos incorrectos")
		return
	} else {
		if proveedor == "Directo" {
			type dirres struct {
				MessageId   string `json:"message_id"`
				MessageText string `json:"message_text"`
			}
			for _, v := range numeros {
				entrada.ID = 0
				entrada.Phone = v
				if len(v) == 12 {
					requestData := "from=" + from + "&to=" + v + "&message=" + msg
					req, err := http.NewRequest("POST", medio.Url, strings.NewReader(requestData))
					util.PrintJson(bytes.NewBufferString(requestData))
					util.CheckErr(err)
					req.Header.Add("Authorization", "Bearer "+medio.Token)
					req.Header.Add("Content-Type", "application/x-www-form-urlencoded")
					res, err := client.Do(req)
					util.CheckErr(err)
					defer res.Body.Close()
					body, err := io.ReadAll(res.Body)
					util.PrintJson(string(body))
					util.CheckErr(err)
					respu := dirres{}
					err = json.Unmarshal(body, &respu)
					util.CheckErr(err)
					entrada.DatetimeInit = time.Now()
					entrada.Resp = string(body)
					entrada.Uid = respu.MessageId
					entrada.Status = fmt.Sprint(res.StatusCode)
					mo.Dbl.Create(&entrada)
				} else {
					entrada.Status = "400"
					entrada.StatusDesc = "Error"
					entrada.Uid = ""
					entrada.Resp = "{\"error_message\":\"Número incorrecto\"}"
					entrada.DatetimeInit = time.Now()
					mo.Dbl.Create(&entrada)
				}
			}
		} else {
			msgAct := mo.UnMsgIpcom{Texto: msg}
			entrada.StatusDesc = "Entregado"
			entrada.Json = "largo"
			type dirres struct {
				MessageId   string `json:"message_id"`
				MessageText string `json:"message_text"`
				Code        struct {
					ID     string `json:"id"`
					Nombre string `json:"nombre"`
				}
			}
			for _, v := range numeros {
				entrada.ID = 0
				entrada.Phone = v
				if len(v) == 12 {
					msgAct.Para = v
					requestData, err := json.Marshal(msgAct)
					util.CheckErr(err)
					req, err = http.NewRequest("POST", medio.Url, bytes.NewBuffer(requestData))
					util.CheckErr(err)
					req.Header.Add("Authorization", medio.Token)
					req.Header.Add("Content-Type", "application/json")
					res, err := client.Do(req)
					util.CheckErr(err)
					defer res.Body.Close()
					body, err := io.ReadAll(res.Body)
					util.CheckErr(err)
					respu := dirres{}
					err = json.Unmarshal(body, &respu)
					util.CheckErr(err)
					entrada.DatetimeInit = time.Now()
					entrada.Resp = string(body)
					entrada.Uid = respu.MessageId
					entrada.Status = fmt.Sprint(res.StatusCode)
					mo.Dbl.Create(&entrada)
				} else {
					entrada.Status = "400"
					entrada.StatusDesc = "Error"
					entrada.Uid = ""
					entrada.Resp = "{\"error_message\":\"Número incorrecto\"}"
					entrada.DatetimeInit = time.Now()
					mo.Dbl.Create(&entrada)
				}
			}
		}
	}
	if len(numeros) == 1 {
		util.RespondJSON(w, 200, map[string]interface{}{"id": fmt.Sprint(entrada.ID), "estatus": "procesando"})
	} else {
		util.RespondJSON(w, 200, map[string]interface{}{"id": "varios", "estatus": "procesando"})
	}
}

func SmsTexto(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	reqdata, _ := forms.Parse(r)
	sl := reqdata.Get("sl")
	e := reqdata.Get("e")
	msg := reqdata.Get("msg")
	tipo := util.Tif(reqdata.Get("sl") == "l", "gsm", "sms")
	if e == "" {
		msg = util.Slugify(msg, tipo)
	} else {
		msg = util.Slugify(msg, sl, e)
	}

	util.RespondJSON(w, 200, map[string]interface{}{"msg": msg, "lng": len(msg)})
}
