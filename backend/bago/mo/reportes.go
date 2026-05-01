package mo

import "time"

type RepInbound struct {
	ID                uint      `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdCampana         string    `json:"id_campana" gorm:"type:varchar(11)"`
	IdAgente          string    `json:"id_agente" gorm:"type:varchar(11)"`
	Fecha             time.Time `json:"fecha" gorm:"type:datetime not null"`
	Numero            string    `json:"numero" gorm:"type:varchar(25) not null"`
	Linkedid          string    `json:"linkedid" gorm:"type:varchar(32) not null"`
	Campana           string    `json:"campana" gorm:"type:varchar(150) not null"`
	Agente            string    `json:"agente" gorm:"type:varchar(150) not null"`
	Did               string    `json:"did" gorm:"type:varchar(7) not null"`
	Extension         string    `json:"extension" gorm:"type:varchar(7) not null"`
	Espera            uint      `json:"espera" gorm:"type:mediumint(8) not null"`
	Duracion          uint      `json:"duracion" gorm:"type:mediumint(8) not null"`
	EsperaTotal       uint      `json:"espera_total" gorm:"type:mediumint(8) not null"`
	Grabacion         string    `json:"grabacion" gorm:"type:varchar(100) not null"`
	Estatus           string    `json:"estatus" gorm:"type:varchar(15) not null"`
	Calidad           string    `json:"calidad" gorm:"type:varchar(3) not null"`
	CalidadComentario string    `json:"calidad_comentario" gorm:"type:text not null"`
}

type RepOutbound struct {
	ID                uint      `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdCampaign        string    `json:"id_campaign" gorm:"type:varchar(11)"`
	IdAgente          string    `json:"id_agente" gorm:"type:varchar(11)"`
	Fecha             time.Time `json:"fecha" gorm:"type:datetime not null"`
	Numero            string    `json:"numero" gorm:"type:varchar(25) not null"`
	Linkedid          string    `json:"linkedid" gorm:"type:varchar(32) not null"`
	Campana           string    `json:"campana" gorm:"type:varchar(150) not null"`
	Agente            string    `json:"agente" gorm:"type:varchar(150) not null"`
	Did               string    `json:"did" gorm:"type:varchar(7) not null"`
	Extension         string    `json:"extension" gorm:"type:varchar(7) not null"`
	Duracion          uint      `json:"duracion" gorm:"type:mediumint(8) not null"`
	Hangup            string    `json:"hangup" gorm:"type:varchar(10) not null"`
	Grabacion         string    `json:"grabacion" gorm:"type:varchar(100) not null"`
	Estatus           string    `json:"estatus" gorm:"type:varchar(15) not null"`
	Calidad           string    `json:"calidad" gorm:"type:varchar(3) not null"`
	CalidadComentario string    `json:"calidad_comentario" gorm:"type:text not null"`
}
