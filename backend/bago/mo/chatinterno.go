package mo

import (
	"time"
)

type ChatinternoEntry struct {
	ID              uint       `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	IdUsuarioEmite  uint       `json:"id_usuario_emite" gorm:"type:int(11);not null"`
	IdUsuarioRecibe uint       `json:"id_usuario_recibe" gorm:"type:int(11);not null"`
	Mensaje         string     `json:"mensaje" gorm:"type:text;not null"`
	FechaEnvio      time.Time  `json:"fecha_envio" gorm:"type:datetime;not null;default:current_timestamp"`
	FechaRecepcion  *time.Time `json:"fecha_recepcion" gorm:"type:datetime"`
	FechaLectura    *time.Time `json:"fecha_lectura" gorm:"type:datetime"`
	Estatus         string     `json:"estatus" gorm:"type:varchar(10);not null;default:''"`
}

/**
 * IntChatSaveMsg Guarda un mensaje en la base de datos
 * @param from El id del usuario que envía el mensaje
 * @param to El id del usuario que recibe el mensaje
 * @param msg El mensaje
 * @return Un arreglo con el mensaje guardado
 */
func IntChatSaveMsg(from, to uint, msg string) []ChatinternoEntry {
	// Guarda el mensaje en la base de datos
	ahora := time.Now().In(Local)
	entrada := ChatinternoEntry{
		IdUsuarioEmite:  from,
		IdUsuarioRecibe: to,
		Mensaje:         msg,
		FechaEnvio:      ahora,
		Estatus:         "Enviado",
	}
	if to == 0 {
		entrada.FechaRecepcion = &ahora
		entrada.FechaLectura = &ahora
		entrada.Estatus = "Leido"
	}
	Dbl.Create(&entrada)

	return []ChatinternoEntry{entrada}
}

/**
 * IntChatGetUnreadMsgs Obtiene los mensajes sin leer de un usuario
 * @param uid El id del usuario
 * @return Un arreglo con los mensajes sin leer
 */
func IntChatGetUnreadMsgs(uid interface{}) []ChatinternoEntry {
	// Obtiene los mensajes sin leer de la base de datos
	var entradas []ChatinternoEntry
	Dbl.Where("id_usuario_recibe = ? AND estatus <> ?", uid, "Leido").Order("id_usuario_emite ASC, fecha_envio DESC").Find(&entradas)
	// Actualizar registros que están en estatus Enviado a Entregado y fecha de recepción
	ahora := time.Now().In(Local)
	Dbl.Model(&ChatinternoEntry{}).Where("id_usuario_recibe = ? AND estatus = ?", uid, "Enviado").
		Updates(ChatinternoEntry{Estatus: "Entregado", FechaRecepcion: &ahora})

	return entradas
}

/**
 * IntChatGetMsgs Obtiene los mensajes de un chat interno
 * @param from El id del usuario origen
 * @param to El id del usuario destino
 * @param desde El id del mensaje desde el cual se quieren obtener los mensajes
 * @return Un arreglo con los mensajes del chat
 */
func IntChatGetMsgs(from, to, desde interface{}) []ChatinternoEntry {
	// Obtiene los mensajes de la base de datos
	var entradas []ChatinternoEntry
	query := Dbl.Where("(id_usuario_emite = ? AND id_usuario_recibe = ?) OR (id_usuario_emite = ? AND id_usuario_recibe = ?)", from, to, to, from)
	if desde != nil && desde != "" && desde != "0" {
		query = query.Where("id < ?", desde)
	}
	query.Order("fecha_envio DESC").Limit(6).Find(&entradas)

	return entradas
}

/**
 * IntChatMarkDelivery Marca una conversación como Entregada
 * @param from El id del usuario que emite
 * @param to El id del usuario que recibe
 */
func IntChatMarkDelivery(from, to interface{}) {
	ahora := time.Now().In(Local)
	Dbl.Model(&ChatinternoEntry{}).Where("id_usuario_recibe = ? AND id_usuario_emite = ? AND estatus = 'Enviado'", to, from).
		Updates(ChatinternoEntry{Estatus: "Entregado", FechaRecepcion: &ahora})
}

/**
 * IntChatMarkRead Marca una conversación como leída
 * @param from El id del usuario que emite
 * @param to El id del usuario que recibe
 */
func IntChatMarkRead(from, to interface{}) {
	ahora := time.Now().In(Local)
	Dbl.Model(&ChatinternoEntry{}).Where("id_usuario_recibe = ? AND id_usuario_emite = ? AND estatus = 'Entregado'", to, from).
		Updates(ChatinternoEntry{Estatus: "Leido", FechaLectura: &ahora})
}
