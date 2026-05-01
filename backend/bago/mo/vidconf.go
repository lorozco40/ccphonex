package mo

import (
	"net/http"
	"os"
	"strconv"
	"time"

	"phonex/bago/forms"
	"phonex/bago/util"

	"github.com/golang-jwt/jwt/v5"
	"github.com/julienschmidt/httprouter"
)

func GeneraToken(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	// Duración en horas
	dur, _ := strconv.Atoi(util.Tif(reqdata.Get("dur") != "", reqdata.Get("dur"), "1"))
	dur = util.TifEmpty(dur, 1)
	defroom := util.Tif(requser.Perfil == "admin", "*", "salageneral")
	token, err := util.GeneraJWT(reqdata.Get("pk"), jwt.MapClaims{
		"context": map[string]interface{}{
			"user": map[string]string{
				"name":  util.TifEmpty(reqdata.Get("name"), requser.Name+" "+requser.Last),
				"email": util.TifEmpty(reqdata.Get("email"), requser.Email),
			},
		},
		"room": util.TifEmpty(reqdata.Get("room"), defroom),
		"iss":  util.TifEmpty(reqdata.Get("iss"), os.Getenv("VCAPPID")),
		"sub":  util.TifEmpty(reqdata.Get("sub"), os.Getenv("VCDOMAIN")),
		"aud":  util.TifEmpty(reqdata.Get("aud"), "Assertive"),
		"exp":  time.Now().Add(time.Hour * time.Duration(dur)).Unix(),
	})
	if err != nil {
		util.RespondError(w, 500, err.Error())
		return
	}

	util.RespondJSON(w, 200, token)
}
