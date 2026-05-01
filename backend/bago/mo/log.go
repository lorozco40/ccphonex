package mo

import (
	"time"
)

type BagoLog struct {
	ID         uint      `json:"-" gorm:"primary_key;type:int(11);auto_increment"`
	IdUser     *uint     `json:"id_user" gorm:"type:int(11)"`
	IP         string    `json:"ip" gorm:"type:varchar(15);not null;default:''"`
	Evento     string    `json:"evento" gorm:"type:varchar(50);not null;default:''"`
	Data       string    `json:"data" gorm:"type:text;not null;default:''"`
	Fecha      time.Time `json:"fecha" gorm:"type:datetime;not null;default:current_timestamp()"`
	Comentario string    `json:"comentario" gorm:"type:text;not null:default:''"`
	Usuario    User      `json:"-" gorm:"ForeignKey:IdUser"`
}

func LogAdd(log BagoLog, uid uint) {
	if log.Fecha.IsZero() {
		log.Fecha = time.Now().In(Local)
	}
	if uid > 0 {
		log.IdUser = &uid
	}
	Dbl.Create(&log)
}
