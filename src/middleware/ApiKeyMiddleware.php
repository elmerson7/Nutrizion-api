<?php

// ✅ Validar API Key con mejor manejo de errores y logs
function validateApiKey() {
    $useApiKey = getenv('USE_API_KEY') === 'true';
    if (!$useApiKey) {
        return; // 🔥 No validar API Key si está deshabilitado
    }

    $headers = getallheaders();
    $providedKey = $headers['X-API-KEY'] ?? $headers['X-Api-Key'] ?? null;
    $validKey = getenv('API_KEY');

    if (!$providedKey || $providedKey !== $validKey) {
        $respondeCode = 401;
        http_response_code($respondeCode);
        error_log("🚨 Acceso no autorizado desde IP: " . get_client_ip());
        json_response(["error" => "Acceso no autorizado"], $respondeCode);
    }
}