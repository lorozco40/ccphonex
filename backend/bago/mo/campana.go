package mo

import (
	"net/http"
	"strconv"
	"strings"
	"time"

	"phonex/bago/forms"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

// Campaign tabla campañas
type Campaign struct {
	ID          uint      `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	Dids        string    `json:"dids" gorm:"type:varchar(100);not null"`
	Name        string    `json:"name" gorm:"type:varchar(100);not null"`
	Script      string    `json:"script" gorm:"type:text;not null"`
	Tlocal      float32   `json:"-" gorm:"type:decimal(10,2)"`
	Tcell       float32   `json:"-" gorm:"type:decimal(10,2)"`
	Tin         float32   `json:"-" gorm:"type:decimal(10,2)"`
	Outbound    bool      `json:"outbound" gorm:"type:tinyint(1);not null;default:0"`
	Active      bool      `json:"active" gorm:"type:tinyint(1);not null;default:1"`
	CreatedBy   uint      `json:"-" gorm:"type:int(11);not null"`
	CreatedWhen time.Time `json:"-" gorm:"type:datetime;not null;default:CURRENT_TIMESTAMP"`
}

func CampanaLista(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	estado, err := strconv.Atoi(reqdata.Get("estado"))
	util.CheckErr(err)
	if reqdata.Get("tipo") == "ids" {
		camp := IntCampanaListaIds(estado, requser)
		util.RespondJSON(w, 200, camp)
	} else {
		camp := IntCampanaLista(estado, requser)
		util.RespondJSON(w, 200, camp)
	}
}

// intCampanasListaIds devuelve todos los id de las campañas en un string separado por comas
// filtradas por 1 = activas, 2 = inactivas o 0 = todas dependiendo del parametro estado que se reciba
func IntCampanaListaIds(estado int, requser UserFull) string {
	camp := IntCampanaLista(estado, requser)

	var campstr string
	for i := range camp {
		campstr += strconv.Itoa(int(camp[i].ID)) + ","
	}
	campstr = strings.TrimSuffix(campstr, ",")

	return campstr
}

// intCampanasLista devuelve todas las campañas
// filtradas por 1 = activas, 2 = inactivas o 0 = todas dependiendo del parametro estado que se reciba
func IntCampanaLista(estado int, requser UserFull) (camp []Campaign) {
	if requser.Perfil == "admin" || requser.Perfil == "interno" {
		if estado == 1 {
			Dbl.Where("active = ?", true).Find(&camp)
		} else if estado == 2 {
			Dbl.Where("active = ?", false).Find(&camp)
		} else {
			Dbl.Find(&camp)
		}
	} else {
		Dbl.Where("active = ?", true).Where("id IN (?)", requser.Campanas).Find(&camp)
	}

	return
}
