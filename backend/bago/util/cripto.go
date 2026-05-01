package util

import (
	"bytes"
	"crypto/aes"
	"crypto/cipher"
	"crypto/hmac"
	"crypto/md5"
	"crypto/rand"
	"crypto/sha1"
	"crypto/sha256"
	"encoding/base64"
	"fmt"
	"os"
	"time"
)

/**
 * Encripta un texto
 * @param  string quesconde Texto a encriptar
 * @param  string lasal Sal para generar el hash
 * @return string Texto encriptado
 */
func Esconde(quesconde, lasal string) (string, error) {
	// Verificar si se proporciono un valor para 'lasal'
	salt := Tif(lasal == "", os.Getenv("JWTKEY"), lasal)
	key := sha256.Sum256([]byte(salt))
	// Generación de un vector de inicialización aleatorio de 16 bytes
	iv := make([]byte, aes.BlockSize)
	if _, err := rand.Read(iv); err != nil {
		return "", err
	}
	// Convertir la clave a un slice de bytes
	keyBytes := key[:]
	// Convertir el texto a encriptar a un slice de bytes
	plaintext := []byte(quesconde)
	// Crear un nuevo cifrador usando el método y la clave
	block, err := aes.NewCipher(keyBytes)
	if err != nil {
		return "", err
	}
	// Agregar padding al texto si es necesario
	plaintext = pkcs7Pad(plaintext, aes.BlockSize)
	// Crear un modo de operación CBC
	mode := cipher.NewCBCEncrypter(block, iv)
	// Encriptar el texto
	ciphertext := make([]byte, len(plaintext))
	mode.CryptBlocks(ciphertext, plaintext)
	// Crear un HMAC usando SHA-256
	h := hmacSha256(keyBytes, append(ciphertext, iv...))
	// Concatenar IV, HMAC y texto encriptado
	encrypted := append(iv, append(h, ciphertext...)...)
	// Codificar el resultado en base64
	encodedResult := base64.StdEncoding.EncodeToString(encrypted)

	return encodedResult, nil
}

/**
 * Desencripta un texto
 * @param  string queMuestra Texto encriptado
 * @param  string lasal Sal para generar el hash
 * @return string Texto desencriptado
 */
func Encuentra(queMuestra, lasal string) (string, error) {
	// Verificar si se proporciono un valor para 'lasal'
	salt := Tif(lasal == "", os.Getenv("JWTKEY"), lasal)
	key := sha256.Sum256([]byte(salt))
	// Decodificar el texto encriptado de base64
	decodedResult, err := base64.StdEncoding.DecodeString(queMuestra)
	if err != nil {
		return "", err
	}
	// Convertir la clave a un slice de bytes
	keyBytes := key[:]
	// Extraer IV, HMAC y texto encriptado
	iv := decodedResult[:aes.BlockSize]
	h := decodedResult[aes.BlockSize : aes.BlockSize+sha256.Size]
	ciphertext := decodedResult[aes.BlockSize+sha256.Size:]
	// Verificar la integridad del texto encriptado
	if !hmac.Equal(h, hmacSha256(keyBytes, append(ciphertext, iv...))) {
		return "", fmt.Errorf("El texto encriptado ha sido alterado")
	}
	// Crear un nuevo cifrador usando el método y la clave
	block, err := aes.NewCipher(keyBytes)
	if err != nil {
		return "", err
	}
	// Crear un modo de operación CBC
	mode := cipher.NewCBCDecrypter(block, iv)
	// Desencriptar el texto
	plaintext := make([]byte, len(ciphertext))
	mode.CryptBlocks(plaintext, ciphertext)
	// Eliminar el padding del texto
	plaintext = pkcs7Unpad(plaintext)

	return string(plaintext), nil
}

/**
 * Compara un texto plano con un hash
 * @param  string plain Texto plano
 * @param  string hash Hash a comparar
 * @param  string lasal Sal para generar el hash
 * @return bool true si el texto plano coincide con el hash
 */
func Compara(plain, hash, lasal string) bool {
	// Verificar si se proporciono un valor para 'lasal'
	lasal = Tif(lasal == "", os.Getenv("JWTKEY"), lasal)
	quesconde, err := Encuentra(hash, lasal)
	CheckErr(err)

	return plain == quesconde
}

/**
 * Genera un token aleatorio de usuario Assertive
 * @param  int uid ID del usuario
 * @return string Token generado
 */
func GeneraToken(uid uint) string {
	mictim := fmt.Sprintf("%d", time.Now().UnixNano())
	step2 := md5.New().Sum([]byte(mictim))
	token := sha1.Sum(step2)
	tokenString := fmt.Sprintf("%x", token)

	return tokenString
}

func pkcs7Pad(b []byte, blocksize int) []byte {
	pad := blocksize - (len(b) % blocksize)
	padding := bytes.Repeat([]byte{byte(pad)}, pad)
	return append(b, padding...)
}

func pkcs7Unpad(b []byte) []byte {
	l := len(b)
	pad := int(b[l-1])
	if pad > l {
		return nil
	}
	return b[:l-pad]
}

func hmacSha256(key, data []byte) []byte {
	h := hmac.New(sha256.New, key)
	h.Write(data)
	return h.Sum(nil)
}
