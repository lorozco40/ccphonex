package util

import (
	"errors"
	"os"
	"time"

	"github.com/golang-jwt/jwt/v5"
)

func GeneraJWT(pk string, claimsVals jwt.MapClaims) (string, error) {
	privk := Tif(pk != "", pk, os.Getenv("JWTKEY"))
	if _, ok := claimsVals["aud"]; !ok {
		// Pues quien más !?
		claimsVals["aud"] = "Assertive"
	}
	if _, ok := claimsVals["exp"]; !ok {
		// 1 hora
		claimsVals["exp"] = time.Now().Add(time.Hour * 1).Unix()
	}
	claims := jwt.NewWithClaims(jwt.SigningMethodHS256, claimsVals)

	return claims.SignedString([]byte(privk))
}

/**
 * Valida firma y obtiene los claims del token
 * @param  string tokenStr Token a verificar
 * @return map[string]interface{}   claims del token
 */
func ObtenerClaims(pk string, tokenStr string) (interface{}, error) {
	privk := Tif(pk != "", pk, os.Getenv("JWTKEY"))
	token, err := jwt.Parse(tokenStr, func(token *jwt.Token) (interface{}, error) {
		if _, ok := token.Method.(*jwt.SigningMethodHMAC); !ok {
			return nil, errors.New("Algoritmo de firma incorrecto")
		}
		return []byte(privk), nil
	})
	CheckErr(err)
	if err != nil {
		return nil, errors.New("Token no válido")
	}
	claims, ok := token.Claims.(jwt.MapClaims)
	if ok && token.Valid {
		return claims, nil
	}

	return nil, errors.New("Token no válido")
}
