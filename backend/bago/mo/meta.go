package mo

import "time"

type MetaMsgrHook struct {
	ID       uint      `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	Msg      string    `json:"msg" gorm:"type:text not null"`
	Recibido time.Time `json:"recibido" gorm:"type:datetime not null"`
}
