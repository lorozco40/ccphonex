package rutas

import "phonex/bago/mo"

var routesDesp = Routes{
	// Rutas de despachadores
	Route{"GET", "/despachador", mo.DispList, "DispList"}, // ToDo, Filtrar por el cliente concectado
	Route{"GET", "/despachador/:id", mo.DispOne, "DispOne"},
	Route{"GET", "/despachador/:id/llamadas", mo.DispCalls, "DispCalls"},
	Route{"GET", "/despachador/:id/lead", mo.DispLeadList, "DispLeadList"},
	Route{"GET", "/despachador/:id/lead/:lid", mo.DispLeadOne, "DispLeadOne"},
	Route{"POST", "/despachador/:id/lead", mo.DispLeadNew, "DispLeadNew"},
}
