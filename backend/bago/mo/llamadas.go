package mo

import (
	"net/http"
	"time"

	"phonex/bago/forms"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

type CallEntry struct {
	ID               uint       `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdUser           *uint      `json:"id_user" gorm:"type:int(11)"`
	IdCampaign       *uint      `json:"id_campaign" gorm:"type:int(11)"`
	Queue            string     `json:"queue" gorm:"type:varchar(7);not null default ''"`
	Did              string     `json:"did" gorm:"type:varchar(7);not null default ''"`
	CidName          string     `json:"cid_name" gorm:"type:varchar(32);not null default ''"`
	CidNum           string     `json:"cid_num" gorm:"type:varchar(25);not null default ''"`
	DatetimeReceived *time.Time `json:"datetime_received" gorm:"type:datetime"`
	DatetimeQueued   *time.Time `json:"datetime_queued" gorm:"type:datetime"`
	DatetimeInit     *time.Time `json:"datetime_init" gorm:"type:datetime"`
	DatetimeEnd      *time.Time `json:"datetime_end" gorm:"type:datetime"`
	Duration         *uint      `json:"duration" gorm:"type:smallint(5)"`
	DurationWait     *uint      `json:"duration_wait" gorm:"type:smallint(5)"`
	Status           string     `json:"status" gorm:"type:varchar(20);not null default ''"`
	Grabacion        string     `json:"grabacion" gorm:"type:varchar(100);not null default ''"`
	Uniqueid         string     `json:"uniqueid" gorm:"type:varchar(32);not null default ''"`
	Type             string     `json:"type" gorm:"type:varchar(10);not null default ''"`
	Hangup           string     `json:"hangup" gorm:"type:varchar(10);not null default ''"`
	Extra            string     `json:"extra" gorm:"type:varchar(255);not null default ''"`
	Rate             float32    `json:"rate" gorm:"type:decimal(10,2)"`
	RateType         string     `json:"rate_type" gorm:"type:varchar(10);not null default ''"`
	User             User       `json:"-" gorm:"foreignkey:IdUser"`
	Campaign         Campaign   `json:"-" gorm:"foreignkey:IdCampaign"`
}

func LlamadaLista(w http.ResponseWriter, r *http.Request, p httprouter.Params) {
	requser := IntGetUserFromJSON(p.ByName("ru"))
	reqdata, _ := forms.Parse(r)
	var response []struct {
		ID               uint       `json:"id"`
		IdUser           *uint      `json:"id_user"`
		User             string     `json:"user"`
		IdCampaign       *uint      `json:"id_campaign"`
		Campaign         string     `json:"campaign"`
		CidName          string     `json:"cid_name"`
		CidNum           string     `json:"cid_num"`
		Did              string     `json:"did"`
		Queue            string     `json:"queue"`
		Type             string     `json:"type"`
		Uniqueid         string     `json:"uniqueid"`
		DatetimeReceived *time.Time `json:"datetime_received"`
		DatetimeQueued   *time.Time `json:"datetime_queued"`
		DatetimeInit     *time.Time `json:"datetime_init"`
		DatetimeEnd      *time.Time `json:"datetime_end"`
		DurationWait     *uint      `json:"duration_wait"`
		Duration         *uint      `json:"duration"`
		Grabacion        string     `json:"recording"`
		Hangup           string     `json:"hangup"`
		RateType         string     `json:"rate_type"`
		Rate             float32    `json:"rate"`
		Status           string     `json:"status"`
	}
	filtros := FilterParams{
		Uid:        reqdata.Get("uid"),
		Cid:        reqdata.Get("cid"),
		Model:      &CallEntry{},
		Target:     response,
		Campos:     "call_entry.*, CONCAT(user.name,' ',user.last) AS 'user', campaign.name AS 'campaign'",
		Joins:      []string{"LEFT JOIN user ON user.id = call_entry.id_user", "LEFT JOIN campaign ON campaign.id = call_entry.id_campaign"},
		CampoFecha: "datetime_received",
		DateFr:     reqdata.Get("desde"),
		DateTo:     reqdata.Get("hasta"),
		Page:       reqdata.Get("pag"),
		Rpp:        reqdata.Get("rpp"),
		Other:      reqdata.Get("of"),
		OVal:       reqdata.Get("ov"),
		Ofa:        reqdata.Get("ofa"),
		Ova:        reqdata.Get("ova"),
		Ofb:        reqdata.Get("ofb"),
		Ovb:        reqdata.Get("ovb"),
		Ofc:        reqdata.Get("ofc"),
		Ovc:        reqdata.Get("ovc"),
		Ofd:        reqdata.Get("ofd"),
		Ovd:        reqdata.Get("ovd"),
		Ofe:        reqdata.Get("ofe"),
		Ove:        reqdata.Get("ove"),
		Order:      reqdata.Get("ord"),
	}

	util.RespondJSON(w, 200, GetFilteredData(filtros, requser))
}
