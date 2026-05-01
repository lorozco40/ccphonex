package rutas

import "phonex/bago/ct"

var routesTicket = Routes{
	// Rutas de tickets
	Route{"GET", "/ticket", ct.TicketGroupList, "TicketGroupList"},                     // ToDo: quitar, ruta mejorada abajo
	Route{"POST", "/ticket", ct.TicketGroupAdd, "TicketGroupAdd"},                      // ToDo: quitar, ruta mejorada abajo
	Route{"PUT", "/ticket/:gid", ct.TicketGroupUpd, "TicketGroupUpd"},                  // ToDo: quitar, ruta mejorada abajo
	Route{"GET", "/ticket/:gid", ct.TicketGroupOne, "TicketGroupOne"},                  // ToDo: quitar, ruta mejorada abajo
	Route{"DELETE", "/ticket/:gid", ct.TicketGroupDel, "TicketGroupDel"},               // ToDo: quitar, ruta mejorada abajo
	Route{"GET", "/ticket/:gid/lista", ct.TicketList, "TicketList"},                    // ToDo: quitar, ruta mejorada abajo
	Route{"POST", "/ticket/:gid/nuevo", ct.TicketAdd, "TicketAdd"},                     // ToDo: quitar, ruta mejorada abajo
	Route{"PUT", "/ticket/:gid/detalle/:tid", ct.TicketUpd, "TicketUpd"},               // ToDo: quitar, ruta mejorada abajo
	Route{"GET", "/ticket/:gid/detalle/:tid", ct.TicketOne, "TicketOne"},               // ToDo: quitar, ruta mejorada abajo
	Route{"DELETE", "/ticket/:gid/borrar/:tid", ct.TicketDel, "TicketDel"},             // ToDo: quitar, ruta mejorada abajo
	Route{"POST", "/ticket/:gid/evento/:tid", ct.TicketLogs, "TicketLogs"},             // ToDo: quitar, ruta mejorada abajo
	Route{"GET", "/ticket/:gid/asignables", ct.TicketAssignables, "TicketAssignables"}, // ToDo: quitar, ruta mejorada abajo
	Route{"GET", "/ticket/:gid/clientes", ct.TicketClients, "TicketClients"},           // ToDo: quitar, ruta mejorada abajo
	Route{"GET", "/ticket/:gid/cat", ct.TicketCatsList, "TicketCatsList"},              // ToDo: quitar, ruta mejorada abajo
	Route{"GET", "/ticket/:gid/cat/:cid", ct.TicketCatsEntsList, "TicketCatsEntsList"}, // ToDo: quitar, ruta mejorada abajo
        Route{"POST", "/ticket/:gid/archivo/:tid", ct.TicketFileAdd, "TicketFileAdd"},      // ToDo: quitar, ruta mejorada abajo

	// Route{"GET", "/ticket/grupo", ct.TicketGroupList, "TicketGroupList"},
	// Route{"POST", "/ticket/grupo", ct.TicketGroupAdd, "TicketGroupAdd"},
	// Route{"PUT", "/ticket/grupo", ct.TicketGroupUpd, "TicketGroupUpd"},
	// Route{"GET", "/ticket/grupo/:gid", ct.TicketGroupOne, "TicketGroupOne"},
	// Route{"DELETE", "/ticket/grupo/:gid", ct.TicketGroupDel, "TicketGroupDel"},

	// Route{"GET", "/ticket/entrada/:gid", ct.TicketList, "TicketList"},
	// Route{"POST", "/ticket/entrada", ct.TicketAdd, "TicketAdd"},
	// Route{"PUT", "/ticket/entrada", ct.TicketUpd, "TicketUpd"},
	// Route{"GET", "/ticket/entrada/:gid/:id", ct.TicketOne, "TicketOne"},
	// Route{"DELETE", "/ticket/entrada/:gid/:id", ct.TicketDel, "TicketDel"},

	// Route{"GET", "/ticket/evento/:gid/:eid", ct.TicketLogs, "TicketLogs"},
	// Route{"POST", "/ticket/evento", ct.TicketLogAdd, "TicketLogAdd"},
	// Route{"PUT", "/ticket/evento", ct.TicketLogUpd, "TicketLogUpd"},
	// Route{"DELETE", "/ticket/evento/:gid/:eid", ct.TicketLogDel, "TicketLogDel"},

	// Route{"GET", "/ticket/asignable/:gid", ct.TicketAssignableList, "TicketAssignableList"},
	// Route{"POST", "/ticket/asignable", ct.TicketAssignableAdd, "TicketAssignableAdd"},
	// Route{"PUT", "/ticket/asignable", ct.TicketAssignableUpd, "TicketAssignableUpd"},
	// Route{"GET", "/ticket/asignable/:gid/:aid", ct.TicketAssignableOne, "TicketAssignableOne"},
	// Route{"DELETE", "/ticket/asignable/:gid/:aid", ct.TicketAssignableDel, "TicketAssignableDel"},

	// Route{"GET", "/ticket/cliente/:gid", ct.TicketClients, "TicketClients"},
	// Route{"POST", "/ticket/cliente", ct.TicketClientAdd, "TicketClientAdd"},
	// Route{"PUT", "/ticket/cliente", ct.TicketClientUpd, "TicketClientUpd"},
	// Route{"GET", "/ticket/cliente/:gid/:cid", ct.TicketClientOne, "TicketClientOne"},
	// Route{"DELETE", "/ticket/cliente/:gid/:cid", ct.TicketClientDel, "TicketClientDel"},

	// Route{"GET", "/ticket/catalogo/:gid", ct.TicketCatsList, "TicketCatsList"},
	// Route{"POST", "/ticket/catalogo", ct.TicketCatAdd, "TicketCatAdd"},
	// Route{"PUT", "/ticket/catalogo", ct.TicketCatUpd, "TicketCatUpd"},
	// Route{"GET", "/ticket/catalogo/:gid/:cat", ct.TicketCatsEntsList, "TicketCatsEntsList"},
	// Route{"DELETE", "/ticket/catalogo/:gid/:eid", ct.TicketCatsEntsList, "TicketCatsEntsList"},
}
