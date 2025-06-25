package main

import (
	"encoding/json"
	"math/rand"
	"net/http"
	"time"
)

func main() {
	proxies := []string{
		"http://proxy1.example.com:8000",
		"http://proxy2.example.com:8000",
		"http://proxy3.example.com:8000",
	}

	// Random seed
	rand.Seed(time.Now().UnixNano())

	http.HandleFunc("/get-proxy", func(w http.ResponseWriter, r *http.Request) {
		proxy := proxies[rand.Intn(len(proxies))]

		w.Header().Set("Content-Type", "application/json")
		json.NewEncoder(w).Encode(map[string]string{
			"proxy": proxy,
		})
	})

	println("✅ Proxy service running on http://localhost:8080")
	http.ListenAndServe(":8080", nil)
}
