package mo

import (
	"time"
)

type Extapi struct {
	ID          uint      `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdCampaign  uint      `json:"id_campaign" gorm:"type:int(11)"`
	Name        string    `json:"name" gorm:"type:varchar(30);not null;unique"`
	Url         string    `json:"url" gorm:"type:varchar(100);not null"`
	ValidCrt    bool      `json:"valid_crt" gorm:"type:tinyint(1);not null;default:1"`
	Logloc      int       `json:"logloc" gorm:"type:tinyint(1);not null;default:0;comment:0=headers, 1=body, 2=auth"`
	Sign        string    `json:"sign" gorm:"type:varchar(50);not null"`
	User        string    `json:"user" gorm:"type:varchar(100);not null"`
	Pass        string    `json:"pass" gorm:"type:varchar(100);not null"`
	Token       string    `json:"token" gorm:"type:text;not null"`
	Xhash       string    `json:"xhash" gorm:"type:varchar(65);not null"`
	ValidTo     time.Time `json:"valid_to" gorm:"type:datetime;not null;default:'2150-12-31 23:59:59'"`
	GetTkEp     string    `json:"get_tk_ep" gorm:"type:varchar(100);not null"`
	Info        string    `json:"info" gorm:"type:text;not null"`
	Active      int       `json:"active" gorm:"type:tinyint(1);not null;default:1"`
	CreatedBy   uint      `json:"-" gorm:"type:int(11);not null"`
	CreatedWhen time.Time `json:"-" gorm:"type:datetime;not null;default:CURRENT_TIMESTAMP"`
}

func IntGetExtapi(name, validcams string) (ret Extapi) {
	maswhere := ""
	if validcams != "" {
		maswhere = " OR id_campaign in (" + validcams + ")"
	}
	Dbl.Where("name = ? AND active = '1' AND (id_campaign is null"+maswhere+")", name).First(&ret)

	return
}
