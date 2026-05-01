package ct

import (
	"time"

	"phonex/bago/mo"
)

// Cron es el proceso de tareas programadas
// Se ejecuta cada 20 segundos y tiene un contador de tercios
// para emular el cron por minuto de linux
func Cron() {
	tercio := 0
	for {
		if tercio == 3 {
			tercio = 0
		}
		// Aquí las funciones de cada 20 segundos, fuera de tercio 0
		if hayconectados() {
			chatAsignar()
			asignarTicketsAbiertos()
		}
		if tercio == 1 {
			// Aquí las funciones de cada minuto iniciando a 20s desde el arranque del servidor
			// En la condición se limita a correr esas funciones sólo en un horario específico
			// if entreHoras("23:00", "18:59") {
			// log.Println("Cron corrió entre horas")
			// }
			mo.IntLimpiaLogged()
			mo.UnbanIps()
		}
		tercio++
		time.Sleep(20 * time.Second)
	}
}

// func entreHoras(ini, fin string) bool {
//     hora := time.Now().Format("15:04")
//     if hora >= ini && hora <= fin {
//         log.Println("Si es entre horas")
//         return true
//     }
//     log.Println("No es entre horas")
//     return false
// }

// función que recorre wsClients y regresa true si hay alguno conectado
func hayconectados() bool {
	for _, cliente := range wsClients {
		if cliente.perfil == "agente" || cliente.perfil == "supervisor" {
			return true
		}
	}
	return false
}
