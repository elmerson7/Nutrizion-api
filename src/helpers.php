<?php

require_once __DIR__ . '/config/logger.php';

// ✅ Depurar y detener ejecución
if (!function_exists('dd')) {
    function dd($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
        die(); // 🔥 Detiene la ejecución
    }
}

// ✅ Depurar sin detener ejecución
if (!function_exists('dump')) {
    function dump($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }
}

// ✅ Obtener variable del .env con un valor por defecto
if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}

// ✅ Responder con JSON y código de estado HTTP
if (!function_exists('json_response')) {
    function json_response($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);

        // 🔥 Si es un error (400 o más), registrar en los logs
        if ($status >= 400 && function_exists('log_message')) {
            log_message('error', $data['error'] ?? 'Error desconocido', [
                'ip' => get_client_ip(),
                'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
                'url' => $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
                'status_code' => $status,
                'detalle' => $data['detalle'] ?? null
            ]);
        }

        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

// ✅ Validar si un string es JSON válido
if (!function_exists('is_json')) {
    function is_json($string) {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}

// ✅ Limpiar datos de entrada (Evita ataques XSS)
if (!function_exists('clean_input')) {
    function clean_input($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}

// ✅ Generar un UUID único
if (!function_exists('generate_uuid')) {
    function generate_uuid() {
        return bin2hex(random_bytes(16));
    }
}

// ✅ Obtener la IP del cliente
if (!function_exists('get_client_ip')) {
    function get_client_ip() {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

// ✅ Generar una clave aleatoria segura
if (!function_exists('generate_token')) {
    function generate_token($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
}

// ✅ Obtener el tiempo actual en formato legible
if (!function_exists('current_time')) {
    function current_time($format = 'Y-m-d H:i:s') {
        return date($format);
    }
}

// ✅ Validar un email
if (!function_exists('is_valid_email')) {
    function is_valid_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}

// ✅ Redireccionar a otra URL
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

// ✅ Función para registrar logs en Monolog
if (!function_exists('log_message')) {
    function log_message($level, $message, $context = []) {
        $logger = getLogger();

        switch ($level) {
            case 'debug':
                $logger->debug($message, $context);
                break;
            case 'info':
                $logger->info($message, $context);
                break;
            case 'warning':
                $logger->warning($message, $context);
                break;
            case 'error':
                $logger->error($message, $context);
                break;
            default:
                $logger->info($message, $context);
                break;
        }
    }
}

