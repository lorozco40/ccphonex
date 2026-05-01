package rutas

import (
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"
	"os"
	"strings"
	"time"

	"phonex/bago/ct"
	"phonex/bago/mo"
	"phonex/bago/util"

	"github.com/julienschmidt/httprouter"
)

// Route es la estructura básica de una ruta
type Route struct {
	Method      string
	Path        string
	HandlerFunc httprouter.Handle
	Name        string
}

// Routes es un array de Route
type Routes []Route

var routesBas = Routes{
	// Rutas de páginas estáticas
	Route{"GET", "/", ct.Index, "Index"},
	Route{"GET", "/ws", ct.WsCon, "WsCon"},
	Route{"GET", "/hola/:nombre", ct.Hola, "Hola"},
	Route{"POST", "/bounce", ct.Bounce, "Bounce"},
	// Rutas de sesión (modelo: acceso.go)
	Route{"POST", "/login", ct.AccesoLogin, "AccesoLogin"},
	Route{"GET", "/logout", ct.AccesoLogout, "AccesoLogout"},
	Route{"POST", "/logout", ct.AccesoLogoutAlt, "AccesoLogoutAlt"},
	Route{"GET", "/logged", ct.AccesoLogged, "AccesoLogged"},
	Route{"GET", "/endpoint", mo.EndpointList, "EndpointList"},
	// Rutas de licencia
	Route{"GET", "/licencia", ct.LicenciaShow, "LicenciaShow"},
	Route{"POST", "/licencia", ct.RecibeLicencia, "RecibeLicencia"},
	// Rutas Campañas
	Route{"GET", "/campana", mo.CampanaLista, "CampLista"},
	// Rutas de Breaks
	Route{"GET", "/descanso", mo.BreakList, "BreakList"},
	Route{"GET", "/descanso/:id", mo.BreakOne, "BreakOne"},
	// Rutas de Formularios
	Route{"GET", "/formulario", ct.FormList, "FormList"},
	// Rutas Wabox
	Route{"POST", "/wabox", ct.Wabox, "Wabox"},
	Route{"POST", "/wabox-serve", ct.WaboxServe, "WaboxServe"},
	// Route{"POST", "/wabox_file", mo.WaboxFile},
	Route{"POST", "/callfile", ct.CallFile, "CallFile"},
	// Crypto rutas (para uso interno)
	Route{"POST", "/cripto/esconde", mo.CriptoEsconde, "CriptoEsconde"},
	Route{"POST", "/cripto/encuentra", mo.CriptoEncuentra, "CriptoEncuentra"},
	// Videoconferencias
	Route{"GET", "/vidconf/token", mo.GeneraToken, "GeneraToken"},
	// JWT
	Route{"POST", "/jwt/genera", ct.GeneraJWT, "GeneraJWT"},
	Route{"POST", "/jwt/valida", ct.ValidaJWT, "ValidaJWT"},
}
var routeFiles = [][]Route{
	routesBas, routesEmail, routesMeta, routesSMS, routesUser, routesCalls, routesDesp, routesChat, routesTicket,
}
var routes = util.SlicesJoin(routeFiles)

// NewRouter es la estructura básica del router
func NewRouter() *httprouter.Router {
	router := httprouter.New()
	router.GlobalOPTIONS = http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		if r.Header.Get("Access-Control-Request-Method") != "" {
			// Set CORS headers
			header := w.Header()
			header.Set("Access-Control-Allow-Origin", "*")
			header.Set("Access-Control-Allow-Methods", "POST, GET, OPTIONS, PUT, DELETE")
			header.Set("Access-Control-Allow-Headers", "Origin")
		}

		// Adjust status code to 204
		w.WriteHeader(http.StatusNoContent)
	})
	router.NotFound = http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		util.RespondError(w, 404, "Recurso no encontrado")
	})
	for _, route := range routes {
		var handle httprouter.Handle

		handle = route.HandlerFunc
		handle = Logger(handle, route.Name)

		router.Handle(route.Method, route.Path, handle)
	}

	return router
}

// Logger imprime por consola el handle
func Logger(fn func(w http.ResponseWriter, r *http.Request, param httprouter.Params), rname string) func(w http.ResponseWriter, r *http.Request, param httprouter.Params) {
	return func(w http.ResponseWriter, r *http.Request, param httprouter.Params) {
		start := time.Now()
		// Asegurando que las peticiones vienen del mismo servidor o de la intranet
		laipentrante := util.ReadUserIP(r)
		if os.Getenv("ENV") == "dev" {
			log.Printf("---------------->")
			log.Printf("Desde %s (%s %s)", laipentrante, r.Method, r.URL.Path)
		}
		var reptest ct.Licencia
		var token string
		var ru mo.UserFull
		ruta := mo.IntGetEndpoint(rname)
		if ruta.ID == 0 {
			log.Printf("Ruta %s no registrada", rname)
			ruta.Name = rname
			ruta.Level = 1     // Nivel de seguridad para rutas no registradas, con permiso
			ruta.Active = true // Rutas no registradas siempre activas en caso de existir alguien con permiso de uso
		}
		if !ruta.Active {
			util.RespondError(w, 423, "Recurso no disponible")
			goto final
		}
		token = getKeyFromWherever(r, param)
		// Setea el valor de la variable requser
		ru = mo.CheckToken(token)
		// 3 = Público, 2 = Requiere token, 1 = Requiere token y permiso, 0 = Sólo admin
		if ruta.Level == 3 {
			// No requiere token ni estar loggeado
			// Cuidado !!! Validar request directo en cada una de estas rutas
			log.Printf("No requiere token lvl 3")
			if ru.ID == 0 {
				ru.ID = 999999
				ru.Perfil = "externo"
			}
			// ToDo La siguiente condición se debe quitar cuando se pase totalmente a React o Vue
			// el token vacío tiene una razón de ser, pero no es la mejor
			// modificar el frontend para que envíe el token en todas las peticiones
		} else if token == "" && (laipentrante == os.Getenv("SIP") || laipentrante == "127.0.0.1" ||
			laipentrante == "[::1]" || strings.HasPrefix(laipentrante, "172.17.")) {
			// Solicitud local, tampoco requiere token pero sólo es para llamadas desde el mismo servidor
			if os.Getenv("ENV") == "dev" {
				log.Println("Solicitud local")
			}
			param = append(param, httprouter.Param{Key: "localr", Value: "local"})
			if ru.ID == 0 {
				ru.ID = 999998
				ru.Perfil = "interno"
				ru.Campanas = mo.IntCampanaListaIds(0, ru)
			}
		} else if ru.ID == 0 {
			log.Printf("Request de %s sin token válido", laipentrante)
			util.RespondError(w, 401, "Usuario desconocido")
			goto final
		} else {
			if ruta.Level < 2 {
				permiso := mo.CheckPermiso(ruta, ru)
				if !permiso {
					log.Printf("%s %s %s no tiene permiso a %s %s", ru.Perfil, ru.Name, ru.Last, r.Method, r.URL.Path)
					util.RespondError(w, 403, "No autorizado")
					goto final
				}
			}
		}
		// ToDo, pasar a archivo de logs borrable a un día
		if os.Getenv("ENV") == "dev" {
			log.Printf("Request de %s %s %s un %s", fmt.Sprint(ru.ID), ru.Name, ru.Last, ru.Perfil)
			log.Printf("Campanas %s", ru.Campanas)
		}
		// ToDo validar licencias multiples y aplicar a Assertive
		reptest = ct.ValidaLicencia()
		if reptest.Error != "" {
			util.RespondError(w, 402, reptest.Error)
			goto final
		} else {
			rum, _ := json.Marshal(ru)
			param = append(param, httprouter.Param{Key: "ru", Value: string(rum)})
			fn(w, r, param)
		}

	final:
		log.Printf("%s Hecho en %v (%s %s)", laipentrante, time.Since(start), r.Method, r.URL.Path)
		if os.Getenv("ENV") == "dev" {
			log.Println("<----------------")
		}
	}
}

func getKeyFromWherever(r *http.Request, param httprouter.Params) string {
	if r.Header.Get("key") != "" {
		return r.Header.Get("key") // from header
	}
	if r.FormValue("key") != "" {
		return r.FormValue("key") // from form values
	}
	if param.ByName("key") != "" {
		return param.ByName("key") // from url param
	}
	// io.ReadAll(r.Body) no se puede leer dos veces elimina el body, volver a escribir
	jsonBody, _ := io.ReadAll(r.Body) // from json body
	var data struct{ Key string }
	json.Unmarshal(jsonBody, &data)
	// volver a escribir r.Body con el jsonBody original !importante
	r.Body = io.NopCloser(strings.NewReader(string(jsonBody)))
	if data.Key != "" {
		return data.Key
	}

	return ""
}
