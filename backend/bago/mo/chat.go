package mo

import (
	"fmt"
	"log"
	"phonex/bago/forms"
	"phonex/bago/util"
	"time"
)

type ChatInstance struct {
	ID          string    `json:"id" gorm:"primary_key;type:varchar(10)"`
	IdCampaign  uint      `json:"id_campaign" gorm:"type:int(11);not null"`
	Active      bool      `json:"active" gorm:"type:tinyint(1);not null;default:0"`
	CreatedBy   uint      `json:"-" gorm:"type:int(11);not null"`
	CreatedWhen time.Time `json:"-" gorm:"type:datetime;not null;default:current_timestamp()"`
	Campaign    Campaign  `json:"-" gorm:"foreignKey:IdCampaign"`
}

type ChatInstanceDefs struct {
	ID             uint         `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdChatInstance string       `json:"id_chat_instance" gorm:"type:varchar(10);not null"`
	Name           string       `json:"name" gorm:"type:varchar(100);not null"`
	Value          string       `json:"value" gorm:"type:text;not null;default:''"`
	Extra          string       `json:"extra" gorm:"type:varchar(254);not null;default:''"`
	CreatedBy      uint         `json:"-" gorm:"type:int(11);not null"`
	CreatedWhen    time.Time    `json:"-" gorm:"type:datetime;not null;default:current_timestamp()"`
	Instance       ChatInstance `json:"_" gorm:"foreignKey:IdChatInstance"`
}

type ChatSession struct {
	ID             uint         `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdWs           string       `json:"id_ws" gorm:"type:varchar(64);not null"`
	IdUser         *uint        `json:"id_user" gorm:"type:int(11)"`
	IdChatInstance string       `json:"id_chat_instance" gorm:"type:varchar(10);not null"`
	Start          time.Time    `json:"start" gorm:"type:datetime;not null;default:current_timestamp"`
	Assign         *time.Time   `json:"assign" gorm:"type:datetime"`
	Answer         *time.Time   `json:"answer" gorm:"type:datetime"`
	Finish         *time.Time   `json:"finish" gorm:"type:datetime"`
	Wait           int          `json:"wait" gorm:"type:int(11);not null;default:0"`
	Duration       int          `json:"duration" gorm:"type:mediumint(8);unsigned;not null;default:0"`
	Transfer       uint         `json:"transfer" gorm:"type:int(11);unsigned;not null;default:0"`
	Status         string       `json:"status" gorm:"type:varchar(20);not null;default:'Cola'"`
	User           User         `json:"-" gorm:"foreignKey:IdUser"`
	ChatInstance   ChatInstance `json:"-" gorm:"foreignKey:IdChatInstance"`
	Entries        []ChatEntry  `json:"entries" gorm:"foreignKey:IdChatSession"`
}

type ChatEntry struct {
	ID            uint      `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdChatSession uint      `json:"id_chat_session" gorm:"type:int(11)"`
	Message       string    `json:"message" gorm:"type:text;not null;default:''"`
	Fecha         time.Time `json:"fecha" gorm:"type:datetime;not null;default:current_timestamp"`
	Extra         string    `json:"extra" gorm:"type:varchar(254);not null;default:''"`
	Type          string    `json:"type" gorm:"type:varchar(20);not null:default:''"`
}

func GetChatInstance(id string) (ins ChatInstance) {
	Dbl.Where("id = ?", id).First(&ins)
	return
}

func GetChatInstanceDefs(ins ChatInstance) (defs []ChatInstanceDefs) {
	Dbl.Where("id_chat_instance = ?", ins.ID).Find(&defs)
	return
}

func GetChatSesion(data map[string]interface{}, ins ChatInstance) (sesion ChatSession) {
	if data["acsid"].(float64) > 0 {
		Dbl.Where("id = ?", data["acsid"]).First(&sesion)
	} else {
		ahorita := time.Now().In(Local)
		sesion.Start = ahorita
		sesion.IdChatInstance = ins.ID
		var agente uint
		if val, ok := data["agente"]; ok {
			agente = uint(val.(float64))
		} else {
			agente = uint(IntGetAgentePara("chat", fmt.Sprint(ins.IdCampaign)))
		}
		if agente != 0 {
			sesion.IdUser = &agente
			sesion.Assign = &ahorita
			sesion.Status = "Asignado"
		}
		Dbl.Create(&sesion)
	}
	if sesion.ID != 0 {
		sesion.IdWs = data["wsid"].(string)
		Dbl.Save(&sesion)
		Dbl.Preload("User").First(&sesion)
	}

	return
}

func GetChatCamData(cam uint) (camdata []ChatInstanceDefs) {
	Dbl.Where("id_chat_instance = ?", cam).Find(&camdata)
	return
}

// GetChatEsperando Obtiene los chats en espera, no había agentes disponibles
func GetChatEsperando() (esperando []ChatSession) {
	Dbl.Where("status = 'Cola'").Find(&esperando)
	return
}

// getAchatPendientes Obtiene los chats pendientes de un agente, no terminados
func GetChatPendientes(uid string) (chats []ChatSession) {
	Dbl.Preload("Entries").
		Where("id_user = ? AND status <> ? AND status <> ?", uid, "Terminado", "Abandono").
		Find(&chats)

	return
}

func AddChatInstance(data *forms.Data, ru string) (ins ChatInstance) {
	requser := IntGetUserFromJSON(ru)
	ins.IdCampaign = util.Str2Uint(data.Get("cid"))
	ins.CreatedBy = requser.ID
	// asigno un nuevo ID generado con la funcion util.RandStringBytesMaskImpr
	// luego checo que no exista en la base de datos, si es así lo vuelvo a generar
	// hasta que sea único
	largoid := 5
	largoenbd := 0
	Dbl.Raw("SELECT MAX(LENGTH(id)) from chat_instance").Row().Scan(&largoenbd)
	if largoenbd > largoid {
		largoid = largoenbd
	}
	malos := 0
	for {
		if malos == 3 && largoid < 10 {
			largoid++
		}
		ins.ID = util.RandStringBytesMaskImpr(largoid)
		if GetChatInstance(ins.ID).ID == "" {
			break
		}
		malos++
	}
	Dbl.Create(&ins)

	return
}

// ChatAddMsg Agrega un mensaje a un chat
func ChatAddMsg(data map[string]interface{}) (to string) {
	var sesion ChatSession
	Dbl.First(&sesion, data["acsid"])
	if sesion.ID == 0 {
		ins := GetChatInstance(data["ins"].(string))
		sesion = GetChatSesion(data, ins)
	}
	entry := ChatEntry{
		IdChatSession: sesion.ID,
		Message:       data["msg"].(string),
		Fecha:         time.Now().In(Local),
	}
	if data["dir"] == "o" {
		entry.Type = "Saliente"
		to = sesion.IdWs
	} else {
		entry.Type = "Entrante"
		to = fmt.Sprint(*sesion.IdUser)
	}
	Dbl.Create(&entry)

	return
}

func ChatTransferir(ses ChatSession, para uint) (sesion ChatSession) {
	ahorita := time.Now().In(Local)
	sesion.Start = ahorita
	sesion.IdUser = &para
	sesion.IdChatInstance = ses.IdChatInstance
	sesion.IdWs = ses.IdWs
	sesion.Assign = &ahorita
	sesion.Status = "Asignado"
	sesion.Transfer = ses.ID
	Dbl.Create(&sesion)
	return
}

func ChatTerminar(data map[string]interface{}) (sesion ChatSession) {
	Dbl.First(&sesion, data["acsid"])
	if sesion.ID != 0 {
		ahorita := time.Now().In(Local)
		sesion.Finish = &ahorita
		sesion.Status = "Terminado"
		Dbl.Save(&sesion)
	}

	return
}

// chatAbandonar Abandona un chat
func ChatAbandonar(ses ChatSession) {
	ahorita := time.Now().In(Local)
	ses.Status = "Abandono"
	ses.Finish = &ahorita
	Dbl.Save(&ses)
}

// ChatAgenteInicia Inicia un chat marcando la hora de inicio si es que es null
func ChatAgenteInicia(sid float64) {
	var sesion ChatSession
	Dbl.First(&sesion, sid)
	log.Printf("ChatAgenteInicia: %v", sesion)
	if sesion.Answer == nil {
		ahorita := time.Now().In(Local)
		sesion.Answer = &ahorita
		sesion.Status = "Curso"
		Dbl.Save(&sesion)
	}
}
