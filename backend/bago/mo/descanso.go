package mo

import (
	"net/http"

	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

// Break modelo gorm de la tabla break
type Break struct {
	ID          int    `json:"id"`
	Name        string `json:"name"`
	Description string `json:"description"`
	Status      bool   `json:"status"`
}

// Breaks es un array de Break
type Breaks []Break

// BreakList trae todos los registro de break
func BreakList(w http.ResponseWriter, r *http.Request, _ httprouter.Params) {
	var losbreaks Breaks
	Dbl.Find(&losbreaks)
	util.RespondJSON(w, 200, losbreaks)
}

// BreakOne trae un registro de break por su id
func BreakOne(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	id := p.ByName("id")
	var lebreak Break
	Dbl.First(&lebreak, id)

	util.RespondJSON(w, 200, lebreak)
}
