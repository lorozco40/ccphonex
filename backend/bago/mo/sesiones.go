package mo

import (
	"log"
	"time"
)

// Tabla de IPs baneadas
type BannedIps struct {
	ID    uint      `json:"id" gorm:"primaryKey;autoIncrement;type:int(11)"`
	IP    string    `json:"ip" gorm:"type:varchar(15);not null"`
	Razon string    `json:"razon" gorm:"type:varchar(255);not null"`
	Fecha time.Time `json:"fecha" gorm:"type:datetime;not null;default:current_timestamp()"`
}

func CheckToken(token string) (requser UserFull) {
	if token != "" {
		Dbl.Raw("SELECT * FROM user_full WHERE token = ? Limit 1", token).Scan(&requser)
	}
	if requser.ID == 0 {
		for k, v := range LoggedUsers {
			if v.SesKey == token {
				Dbl.Raw("SELECT * FROM user_full WHERE id = ? Limit 1", k).Scan(&requser)
				v.LastReq = time.Now().In(Local)
				LoggedUsers[k] = v
			}
		}
	}
	if requser.ID == 1 || requser.Perfil == "admin" {
		requser.Perfil = "admin"
		Dbl.Raw("SELECT GROUP_CONCAT(c.id) FROM campaign c").Pluck("GROUP_CONCAT(c.id)", &requser.Campanas)
	}

	return
}

// IntLimpiaLogged limpia las sesiones de usuario al terminar el tiempo establecido
// cada usuario tiene su hora y fecha de inicio de sesión y su tiempo de duración máxima de sesión
// ésta función es exclusiva para los clientes logueados por API. NO para los usuarios de la web
func IntLimpiaLogged() {
	for k, v := range LoggedUsers {
		if time.Since(v.Ini) >= (time.Hour * time.Duration(v.For)) {
			log.Println("Borrando sesión de usuario", k)
			delete(LoggedUsers, k)
		}
	}
}

// UnbanIps limpia las IPs baneadas después de 30 minutos
func UnbanIps() {
	Dbl.Exec("DELETE FROM banned_ips WHERE fecha < now() - INTERVAL 30 MINUTE")
}

// private funciones que empiezan con minúscula y no se pueden llamar desde fuera del package
func SaveToken(tokenString string, uid uint) {
	Dbl.Exec("UPDATE user_data ud SET ud.val = ? "+
		"JOIN catalogs c ON c.id = ud.id_catalog "+
		"WHERE ud.id_user = ? and c.cat = 'userData' AND c.val = 'token'",
		tokenString, uid)
}
