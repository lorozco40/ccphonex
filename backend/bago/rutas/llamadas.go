package rutas

import "phonex/bago/mo"

var routesCalls = Routes{
	// Rutas de llamadas
	Route{"GET", "/llamada", mo.LlamadaLista, "LlamadaLista"},
	// Route{"GET", "/llamada/:id", mo.LlamadaUno, "LlamadaUno"},
	// Route{"POST", "/llamada", mo.LlamadaNuevo, "LlamadaNuevo"},
	// Route{"PUT", "/llamada", mo.LlamadaActu, "LlamadaActu"},
	// Route{"DELETE", "/llamada/:id", mo.LlamadaBorra, "LlamadaBorra"},
	// Route{"GET", "/llamada-consola", mo.LlamadaConsola, "LlamadaConsola"},
	// Route{"GET", "/llamada-historia", mo.LlamadaHistoria, "LlamadaHistoria"},
	// // Rutas de llamadas de campañas
	// Route{"GET", "/llamada-campana", mo.LlamadaCampanaLista, "LlamadaCampanaLista"},
	// Route{"GET", "/llamada-campana/:id", mo.LlamadaCampanaUno, "LlamadaCampanaUno"},
	// Route{"POST", "/llamada-campana", mo.LlamadaCampanaNuevo, "LlamadaCampanaNuevo"},
	// Route{"PUT", "/llamada-campana", mo.LlamadaCampanaActu, "LlamadaCampanaActu"},
	// Route{"DELETE", "/llamada-campana/:id", mo.LlamadaCampanaBorra, "LlamadaCampanaBorra"},
	// Route{"GET", "/llamada-campana-consola", mo.LlamadaCampanaConsola, "LlamadaCampanaConsola"},
	// Route{"GET", "/llamada-campana-historia", mo.LlamadaCampanaHistoria, "LlamadaCampanaHistoria"},
}
