package ct

import (
	"log"
	"net/http"
	"os"

	"phonex/bago/forms"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

// CallFile POST ruta para crear archivo de llamada saliente
func CallFile(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
	reqData, err := forms.Parse(r)
	util.CheckErr(err)
	val := reqData.Validator()
	val.Require("token")
	val.Require("exten")
	val.Require("cola")
	val.Require("idpred")
	val.Require("dialer")
	val.Require("numfon")
	val.Require("ip")
	if val.HasErrors() {
		log.Println(val.Messages())
		util.RespondError(w, 400, "Datos incorrectos o incompletos")
		return
	}
	token := reqData.Get("token")
	exten := reqData.Get("exten")
	cola := reqData.Get("cola")
	idpred := reqData.Get("idpred")
	dialer := reqData.Get("dialer")
	numfon := reqData.Get("numfon")
	ip := reqData.Get("ip")
	if token != "K1n0n3537r3y" {
		log.Println("CallFile con token inválido")
		util.RespondError(w, 403, "No autorizado")
		return
	}
	fname := util.MakeTimestamp()
	// f, err := os.Create("/tmp/" + fname)
	// util.CheckErr(err)
	// defer f.Close()
	// _, err = f.WriteString("Channel: SIP/" + dialer + "/" + numfon + "\nCallerid: 'Corporativo' <5553750000>\nContext: predictivo\nExtension: " +
	// 	exten + "\nSet: COLA=" + cola + "\nSet: IDPRED=" + idpred + "\nSet: NUMFON=" + numfon + "\nSet: PBX=SIP/Ask" + ip + "/\n")
	cont := []byte("Channel: SIP/" + dialer + "/" + numfon + "\nCallerid: 'Corporativo' <5553750000>\nContext: predictivo\nExtension: " +
		exten + "\nSet: COLA=" + cola + "\nSet: IDPRED=" + idpred + "\nSet: NUMFON=" + numfon + "\nSet: PBX=SIP/Ask" + ip + "/\n")
	err = os.WriteFile("/tmp/"+fname, cont, 0666)
	util.CheckErr(err)
	os.Chmod("/tmp/"+fname, 0666)
	// f.Sync()
	err = os.Rename("/tmp/"+fname, "/var/spool/asterisk/outgoing/"+fname)
	util.CheckErr(err)

	util.RespondJSON(w, 200, map[string]string{"message": "Datos recibidos"})
}
