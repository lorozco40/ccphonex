package mo

import (
	"encoding/json"
	"strings"
	"time"

	"phonex/bago/util"
)

type SmsEntry struct {
	ID           uint      `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdUser       uint      `json:"-" gorm:"not null"`
	IdCampaign   uint      `json:"-" gorm:"type:int(11);not null"`
	Phone        string    `json:"phone" gorm:"type:varchar(20);not null"`
	Msg          string    `json:"msg" gorm:"type:varchar(250);not null"`
	Operator     string    `json:"operator" gorm:"type:varchar(100);not null"`
	DatetimeInit time.Time `json:"date" gorm:"type:datetime;not null"`
	Resp         string    `json:"-" gorm:"type:text;not null"`
	Json         string    `json:"-" gorm:"type:text;not null"`
	Uid          string    `json:"-" gorm:"type:varchar(40);not null"`
	Status       string    `json:"-" gorm:"type:varchar(10);not null"`
	StatusDesc   string    `json:"status_desc" gorm:"type:varchar(100);not null"`
	Type         string    `json:"type" gorm:"type:varchar(10);not null"`
	User         User      `json:"-" gorm:"foreignkey:IdUser"`
	Campaign     Campaign  `json:"-" gorm:"foreignkey:IdCampaign"`
}

type UnMsgIpcom struct {
	Para  string `json:"para"`
	Texto string `json:"texto"`
}

func SmsGetArrayTelefonos(vals ...string) (numeros []string) {
	// vals[0] es el string de números, vals[1] es el prefijo de país
	if !strings.Contains(vals[0], "[") {
		vals[0] = `["` + util.NumericOnly(vals[0]) + `"]`
	}
	var prenumeros []string
	err := json.Unmarshal([]byte(vals[0]), &prenumeros)
	util.CheckErr(err)
	if len(vals) > 1 {
		for _, v := range prenumeros {
			estenum := util.NumericOnly(v)
			if estenum[:len(vals[1])] != vals[1] {
				estenum = vals[1] + estenum
			}
			numeros = append(numeros, estenum)
		}
	} else {
		for _, v := range prenumeros {
			estenum := v
			numeros = append(numeros, util.NumericOnly(estenum))
		}
	}

	return
}
