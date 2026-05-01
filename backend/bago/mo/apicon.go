package mo

import "time"

type ApiConEp struct {
	ID          uint      `json:"id" gorm:"primary_key"`
	IdExtapi    uint      `json:"id_extapi" gorm:"type:int(11);not null"`
	Proto       string    `json:"proto" gorm:"type:varchar(6);not null;default:'GET'"`
	Endpoint    string    `json:"endpoint" gorm:"type:varchar(127);not null;default:''"`
	MapOutData  string    `json:"map_out_data" gorm:"type:text;not null;default:''"`
	MapInData   string    `json:"map_in_data" gorm:"type:text;not null;default:''"`
	Extra       string    `json:"extra" gorm:"type:text;not null;default:''"`
	Log         bool      `json:"log" gorm:"type:tinyint(1);not null;default:1"`
	Active      bool      `json:"active" gorm:"type:tinyint(1);not null;default:1"`
	CreatedBy   uint      `json:"created_by" gorm:"type:int(11);not null"`
	CreatedWhen time.Time `json:"created_when" gorm:"type:datetime;not null;default:current_timestamp()"`
	Extapi      Extapi    `json:"-" gorm:"foreignkey:IdExtapi"`
	Creador     User      `json:"-" gorm:"-"`
}
