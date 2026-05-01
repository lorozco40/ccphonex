package rutas

import (
	"phonex/bago/ct"
	"phonex/bago/mo"
)

var routesEmail = Routes{
	Route{"GET", "/email/cuenta", mo.EmailCtaLista, "EmailCtaLista"},
	Route{"POST", "/email/cuenta", mo.EmailCtaNueva, "EmailCtaNueva"},
	Route{"PUT", "/email/cuenta", mo.EmailCtaActu, "EmailCtaActu"},
	Route{"GET", "/email/cuenta/:id", mo.EmailCtaUna, "EmailCtaUna"},
	Route{"DELETE", "/email/cuenta/:id", ct.EmailCtaBorra, "EmailCtaBorra"},

	Route{"GET", "/email/consola", mo.EmailConsola, "EmailConsola"},

	Route{"GET", "/email/agente/:cid", mo.EmailAgentes, "EmailAgentes"}, // Agentes x cuenta

	Route{"GET", "/email/entrada/:cid", mo.EmailLista, "EmailLista"}, // cuenta id
	Route{"POST", "/email/entrada", mo.EmailNuevo, "EmailNuevo"},
	Route{"PUT", "/email/entrada", mo.EmailActu, "EmailActu"},
	Route{"GET", "/email/entrada/:cid/:id", mo.EmailUno, "EmailUno"},
	Route{"DELETE", "/email/entrada/:cid/:id", mo.EmailBorra, "EmailBorra"},

	Route{"GET", "/email/historia/:eid", mo.EmailHistoria, "EmailHistoria"}, // entrada id
}
