<?php
// ✅ Manejo de CORS con seguridad mejorada
function setCorsHeaders() {
    header("Access-Control-Allow-Origin: *"); // 🔥 Permitir cualquier dominio (se recomienda limitar)
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: X-API-KEY, Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");

    // 🔥 Manejar pre-flight requests de CORS (evita bloqueos en navegadores)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        json_response(["message" => "Pre-flight OK"], 204);
    }
}