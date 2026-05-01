package util

import (
	"crypto/sha256"
	"database/sql"
	"encoding/hex"
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"reflect"
	"runtime"
	"strconv"
	"strings"
	"time"
)

// ToNullInt32 convierte un string a int32 o null
func ToNullInt32(s string) sql.NullInt32 {
	i, err := strconv.Atoi(s)
	return sql.NullInt32{Int32: int32(i), Valid: err == nil}
}

// CheckErr imprime errores en std out
func CheckErr(err error) {
	if err != nil {
		log.Printf("!Error: %+v\n", err)
	}
}

func CheckErrFatal(err error) {
	if err != nil {
		log.Fatalf("!Error: %+v\n", err)
	}
}

// RespondJSON makes the response with payload as json format
func RespondJSON(w http.ResponseWriter, status int, payload interface{}) (ret bool) {
	response, err := json.Marshal(payload)
	if err != nil {
		w.WriteHeader(http.StatusInternalServerError)
		w.Write([]byte(err.Error()))
		return
	}
	w.Header().Set("Access-Control-Allow-Origin", "*")
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(status)
	w.Write([]byte(response))

	return
}

// RespondError makes the error response with payload as json format
func RespondError(w http.ResponseWriter, code int, message string) bool {
	return RespondJSON(w, code, map[string]string{"error": message})
}

// InArray busca un valor en un array, devuelve true si lo encuentra
func InArray[T comparable](val T, array []T) (ok bool) {
	for _, v := range array {
		if ok = v == val; ok {
			return
		}
	}

	return
}

// InJSON convierte un texto a json {msg: texto} o devuelve el mismo json entrante
func InJSON(s []byte) (js map[string]interface{}) {
	if json.Unmarshal(s, &js) != nil {
		s = []byte(`{"a": "texto", "msg": ` + string(s) + `}`)
		json.Unmarshal(s, &js)
	}

	return js
}

// InComaArray busca un valor string en un comaArray
// No debe tener espacios entre las comas y los valores
func InComaArray(aBus string, busEn string) bool {
	aBus = "," + aBus + ","
	busEn = "," + busEn + ","

	return (len(aBus) > 2 && len(busEn) > 2 && strings.Contains(busEn, aBus))
}

// ComaArraysX devuelve true si hay al menos una coincidencia entre los dos comaArrays
func ComaArraysX(uno, dos string) (res bool) {
	unos := strings.Split(uno, ",")
	doss := strings.Split(dos, ",")
	for _, u := range unos {
		for _, d := range doss {
			if u == d {
				return true
			}
		}
	}

	return
}

// MakeTimestamp devuelve
func MakeTimestamp() string {
	data := time.Now().UnixNano()

	return strconv.Itoa(int(data))
}

// Transforma request data en tercer parámetro
func ReqParTo(par, def string) (int, string) {
	finStr := def
	if par != "" {
		finStr = par
	}
	finInt, _ := strconv.Atoi(finStr)

	return finInt, finStr
}

// Permite debuguear variables del tipo array
func PrintJson(v interface{}) string {
	// Convierte el valor en un slice de bytes JSON
	bytes, err := json.Marshal(v)
	if err != nil {
		log.Print(err)
	} else {
		// Obtiene información sobre la llamada de pila actual
		_, file, line, _ := runtime.Caller(1)
		// Imprime el slice de bytes JSON como una cadena de texto
		log.Println("\x1b[46m" + string(bytes) + "\x1b[0m")
		log.Printf("\x1b[36m"+"Archivo: %s Linea: %d Tipo: %T\n", file, line, v)
		log.Println("\x1b[0m")
	}

	return string(bytes)
}

// Convierte un struct en un map[string]interface{} permitiendo editar el map
func StrucToMap[T any](s T) map[string]interface{} {
	var ret = make(map[string]interface{})
	bytes, err := json.Marshal(s)
	CheckErr(err)
	err = json.Unmarshal(bytes, &ret)
	CheckErr(err)

	return ret
}

func Tif[T any](cond bool, vtrue, vfalse T) T {
	if cond {
		return vtrue
	}

	return vfalse
}

// devuelve noempty, pero si esta vacío devuelve siempty
func TifEmpty[T any](noempty, siempty T) T {
	if reflect.DeepEqual(noempty, reflect.Zero(reflect.TypeOf(noempty)).Interface()) {
		return siempty
	}

	return noempty
}

func GenHash(texto string, n int) (string, error) {
	hasher := sha256.New()
	_, err := hasher.Write([]byte(texto))
	if err != nil {
		return "", err
	}
	hashBytes := hasher.Sum(nil)
	hash := hex.EncodeToString(hashBytes)
	if n > len(hash) {
		return "", fmt.Errorf("n es mayor que el tamaño del hash")
	}

	return hash[:n], nil
}

func SlicesJoin[T any](slices [][]T) []T {
	var totalLen int
	for _, s := range slices {
		totalLen += len(s)
	}
	result := make([]T, totalLen)
	var i int
	for _, s := range slices {
		i += copy(result[i:], s)
	}

	return result
}

func ReadUserIP(r *http.Request) string {
	IPAddress := Tif(r.Header.Get("X-Real-Ip") != "", r.Header.Get("X-Real-Ip"), r.Header.Get("X-Forwarded-For"))
	IPAddress = Tif(IPAddress != "", IPAddress, r.RemoteAddr)
	if strings.Contains(IPAddress, ":") {
		pedazos := strings.Split(IPAddress, ":")
		IPAddress = strings.Join(pedazos[:len(pedazos)-1], ":")
	}

	return IPAddress
}

func CampanaValida(camp int, campanas string) bool {
	campana := strconv.Itoa(camp)

	return InComaArray(campana, campanas)
}

func IsNumeric(s string) bool {
	_, err := strconv.ParseFloat(s, 64)

	return err == nil
}

/**
 * Slugify(s, e, v string) convierte un string en un slug, no requiere e y v usa valores default "slg"
 * @param s string a convertir es requerido
 * @param e string a reemplazar los espacios default "_", para dejar espacios " ", o cualquier otro
 * @param v string conjunto de caracteres a utilizar, puede ser "gsm", "sms" o "slg"
 * @return string
 */
func Slugify(vals ...string) string {
	var s, v, e, a string
	if len(vals) == 0 {
		return ""
	}
	s = vals[0]
	s = strings.Trim(s, " ")
	if len(vals) > 1 {
		v = vals[1]
	}
	if len(vals) > 2 {
		e = vals[2]
	}
	e, v, a = getEva(e, v)
	s = quitaTildes(s, a)
	if a == "slg" {
		// convertir a minúsculas y si inicia con un número agregar una a inicial
		s = strings.ToLower(s)
		if s[0] >= '0' && s[0] <= '9' && e != " " {
			s = "a" + s
		}
	}
	// Quitando caracteres no válidos
	if a != "fre" {
		s = strings.Map(func(r rune) rune {
			if strings.ContainsRune(v, r) {
				return r
			}
			return -1
		}, s)
	}
	// Reduciendo espacios más lárgos de uno
	s = strings.Join(strings.Fields(s), " ")
	// Reemplazando espacios
	if e != " " {
		s = strings.ReplaceAll(s, " ", e)
	}

	return s
}

/**
 * getEva devuelve parámetros para convertir un string en un slug, no requiere e y v usa valores default
 * @param e string a reemplazar los espacios default "-", para dejar espacios " ", o cualquier otro
 * @param v nombre del conjunto de caracteres a utilizar, puede ser "gsm", "sms" o "slg"
 * @return string, string, string
 */
func getEva(e, v string) (er, vr, a string) {
	var vgsm = `@!\"#%&'()*=,-./0123456789:;<=>¿?¡ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzüÜñÑ `
	var vsms = `@!\"#%&'()*=,-./0123456789:;<=>?ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz `
	var vslg = `abcdefghijklmnopqrstuvwxyz0123456789_ `
	ope := map[string]string{"slg": "_", "gsm": " ", "sms": " "}
	opv := map[string]string{"slg": vslg, "gsm": vgsm, "sms": vsms}
	a = Tif(v == "", "slg", v)
	vr = opv[a]
	er = TifEmpty(e, ope[a])
	if er != " " && !strings.Contains(vr, er) {
		vr += er
	}

	return
}

/**
 * quitaTildes remueve acentos, diéresis y ñ de un string
 * @param s string a limpiar
 * @param tipo string puede ser "gsm", "sms" o "slg
 * @return string
 */
func quitaTildes(s, tipo string) string {
	s = strings.ReplaceAll(s, "á", "a")
	s = strings.ReplaceAll(s, "é", "e")
	s = strings.ReplaceAll(s, "í", "i")
	s = strings.ReplaceAll(s, "ó", "o")
	s = strings.ReplaceAll(s, "ú", "u")
	s = strings.ReplaceAll(s, "Á", "A")
	s = strings.ReplaceAll(s, "É", "E")
	s = strings.ReplaceAll(s, "Í", "I")
	s = strings.ReplaceAll(s, "Ó", "O")
	s = strings.ReplaceAll(s, "Ú", "U")
	if tipo != "gsm" {
		s = strings.ReplaceAll(s, "ü", "u")
		s = strings.ReplaceAll(s, "Ü", "U")
		s = strings.ReplaceAll(s, "ñ", "n")
		s = strings.ReplaceAll(s, "Ñ", "N")
	}

	return s
}

// RecortaStruc recorta un struct quitando uno o varios de sus campos separados por coma
// y devuelve un map[string]interface{}
func RecortaStruc[T any](s T, campos string) map[string]interface{} {
	var ret = make(map[string]interface{})
	val := reflect.ValueOf(s)
	typ := reflect.TypeOf(s)
	for i := 0; i < val.NumField(); i++ {
		nom := typ.Field(i).Name
		if !InComaArray(nom, campos) {
			ret[nom] = val.Field(i).Interface()
		}
	}

	return ret
}

// NumericOnly devuelve un string con solo los caracteres numéricos
func NumericOnly(s string) string {
	var ret string
	for _, r := range s {
		if r >= '0' && r <= '9' {
			ret += string(r)
		}
	}

	return ret
}

func Clear(v interface{}) {
	p := reflect.ValueOf(v).Elem()
	p.Set(reflect.Zero(p.Type()))
}
