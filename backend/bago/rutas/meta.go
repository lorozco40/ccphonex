package rutas

import "phonex/bago/meta"

var routesMeta = Routes{
	// Rutas Messenger
	Route{"POST", "/messenger/webhook", meta.MessengerWebhook, "MessengerWebhook"},
	Route{"GET", "/messenger/webhook", meta.MessengerLogin, "MessengerLogin"},
	// Rutas Instagram
	// Rutas Whatsapp
}
