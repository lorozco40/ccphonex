package rutas

import (
	"phonex/bago/ct"
	"phonex/bago/mo"
)

var routesUser = Routes{
	// Rutas de usuarios
	Route{"GET", "/usuario", mo.UserList, "UserList"},
	Route{"GET", "/usuario/yo", mo.UserMe, "UserMe"},
	// Route{"GET", "/usuario/:id", mo.UserOne, "UserOne"},
	// Route{"POST", "/usuario", mo.UserAdd, "UserAdd"},
	// Route{"PUT", "/usuario/:id", mo.UserUpd, "UserUpd"},
	// Route{"DELETE", "/usuario/:id", mo.UserDel, "UserDel"},
	Route{"GET", "/permiso", mo.PermList, "PermList"},
	Route{"POST", "/permiso", mo.PermAdd, "PermAdd"},
	Route{"DELETE", "/permiso", mo.PermDel, "PermDel"},
	Route{"POST", "/userdata", mo.UserDataSave, "UserDataSave"},
	Route{"GET", "/usuario/sesion", ct.UserLoginfo, "UserLoginfo"},
	Route{"GET", "/usuario/desloguear/:id", ct.UserUnlog, "UserUnlog"},
}
