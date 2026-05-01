package util

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"
	"net/url"
	"os"
	"time"
)

type ReqData struct {
	URL    string
	Method string // GET, POST, PUT, DELETE
	Type   string // application/json, application/x-www-form-urlencoded
	Body   []byte
	Port   int
	Heads  map[string]interface{}
}

// ToDo: Falta mucho por hacer, esperar el tipo, poner el default, etc
func DoReq(data ReqData) (int, []byte) {
	if data.Port != 80 && data.Port != 443 {
		data.URL = fmt.Sprintf(data.URL+"%d", data.Port)
	}
	var err error
	var req *http.Request
	if data.Method == "GET" && len(data.Body) > 0 {
		result := make(map[string]interface{})
		json.Unmarshal(data.Body, &result)
		q := url.Values{}
		for k, v := range result {
			q.Add(k, fmt.Sprint(v))
		}
		data.URL = fmt.Sprintf("%s?%s", data.URL, q.Encode())
		req, err = http.NewRequest(data.Method, data.URL, nil)
	} else {
		req, err = http.NewRequest(data.Method, data.URL, bytes.NewReader(data.Body))
	}
	if err != nil {
		log.Printf("client: could not create request: %s\n", err)
		os.Exit(1)
	}
	for k, v := range data.Heads {
		req.Header.Set(k, fmt.Sprint(v))
	}
	client := http.Client{
		Timeout: 10 * time.Second,
	}
	res, err := client.Do(req)
	if err != nil {
		log.Printf("client: error making http request: %s\n", err)
		os.Exit(1)
	}

	log.Printf("client: got response!\n")
	log.Printf("client: status code: %d\n", res.StatusCode)

	resBody, err := io.ReadAll(res.Body)
	if err != nil {
		log.Printf("client: could not read response body: %s\n", err)
		os.Exit(1)
	}
	log.Printf("client: response body: %s\n", resBody)

	return res.StatusCode, resBody
}
