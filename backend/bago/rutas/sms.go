package rutas

import "phonex/bago/ct"

var routesSMS = Routes{
	// Rutas de SMS
	Route{"GET", "/sms", ct.SmsLista, "SmsLista"},
	Route{"GET", "/sms/:id", ct.SmsDetalle, "SmsDetalle"},
	Route{"POST", "/sms", ct.SmsEnviar, "SmsEnviar"},
	Route{"POST", "/sms/texto", ct.SmsTexto, "SmsTexto"},
}
