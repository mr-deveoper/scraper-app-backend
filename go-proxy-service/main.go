package main

import (
	"encoding/json"
	"math/rand"
	"net/http"
	"time"
)

func main() {
	proxies := []string{
		"http://185.217.143.123:3128",
		"http://91.214.31.234:8080",
	}

	rand.Seed(time.Now().UnixNano())

	http.HandleFunc("/get-proxy", func(w http.ResponseWriter, r *http.Request) {
		proxy := proxies[rand.Intn(len(proxies))]
		json.NewEncoder(w).Encode(map[string]string{"proxy": proxy})
	})

	println("✅ Go proxy server on http://localhost:8080")
	http.ListenAndServe(":8080", nil)
}
