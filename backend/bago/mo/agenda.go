package mo

import (
	"fmt"
	"phonex/bago/forms"
	"time"
)

type Client struct {
	ID          uint      `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdUser      uint      `json:"-" gorm:"type:int(11)"`
	IdCampaign  uint      `json:"-" gorm:"type:int(11)"`
	Name        string    `json:"nombres" gorm:"type:varchar(100);not null;default:''"`
	Last        string    `json:"appelidos" gorm:"type:varchar(100);not null;default:''"`
	Phone       string    `json:"telefono" gorm:"type:varchar(20);not null;default:''"`
	Email       string    `json:"email" gorm:"type:varchar(100);not null;default:''"`
	Active      bool      `json:"-" gorm:"type:tinyint(1);not null;default:1"`
	Available   bool      `json:"-" gorm:"type:tinyint(1);not null;default:1"`
	Calle       string    `json:"calle" gorm:"type:varchar(100);not null;default:''"`
	Exterior    string    `json:"exterior" gorm:"type:varchar(10);not null;default:''"`
	Interior    string    `json:"interior" gorm:"type:varchar(10);not null;default:''"`
	Colonia     string    `json:"colonia" gorm:"type:varchar(100);not null;default:''"`
	Municipio   string    `json:"municipio" gorm:"type:varchar(100);not null;default:''"`
	Ciudad      string    `json:"ciudad" gorm:"type:varchar(100);not null;default:''"`
	Estado      string    `json:"estado" gorm:"type:varchar(100);not null;default:''"`
	Cp          string    `json:"cp" gorm:"type:char(5);not null;default:''"`
	Pais        string    `json:"pais" gorm:"type:varchar(100);not null;default:''"`
	Facebook    string    `json:"-" gorm:"type:varchar(100);not null;default:''"`
	Twitter     string    `json:"-" gorm:"type:varchar(100);not null;default:''"`
	Instagram   string    `json:"-" gorm:"type:varchar(100);not null;default:''"`
	Linkedin    string    `json:"-" gorm:"type:varchar(100);not null;default:''"`
	Web         string    `json:"-" gorm:"type:varchar(100);not null;default:''"`
	CreatedBy   uint      `json:"-" gorm:"type:int(11);not null"`
	CreatedWhen time.Time `json:"-" gorm:"type:datetime;not null;default:current_timestamp()"`
	User        User      `json:"-" gorm:"ForeignKey:IdUser"`
	Campaign    Campaign  `json:"-" gorm:"ForeignKey:IdCampaign"`
}

func AgendaClientes(cid uint, requser UserFull, reqdata *forms.Data) map[string]interface{} {
	filtros := FilterParams{
		Model:  &Client{},
		Target: []Client{},
		Page:   reqdata.Get("pag"),
		Rpp:    reqdata.Get("rpp"),
		Other:  reqdata.Get("of"),
		OVal:   reqdata.Get("ov"),
		Cid:    fmt.Sprint(cid),
		Order:  "last,name",
	}

	return GetFilteredData(filtros, requser)
}
