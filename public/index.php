<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/config/env.php';
require_once __DIR__ . '/../src/middleware/ApiKeyMiddleware.php';
require_once __DIR__ . '/../src/middleware/CorsMiddleware.php';

// ✅ Manejo Global de Errores y Excepciones
set_exception_handler(function ($exception) {
    $respondeCode = 500;
    http_response_code($respondeCode);
    error_log("🚨 Error: " . $exception->getMessage());
    json_response(["error" => "Error interno del servidor", "detalle" => $exception->getMessage()], $respondeCode);
});

setCorsHeaders();
validateApiKey();

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// ✅ Cargar las rutas
$dispatcher = simpleDispatcher(function (RouteCollector $router) {
    $routes = require __DIR__ . '/../src/routes/api.php';
    $routes($router);
});

// ✅ Obtener la URI y el método de la solicitud
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

// ✅ Procesar la Ruta
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

try {
    switch ($routeInfo[0]) {
        case Dispatcher::NOT_FOUND:
            json_response(["error" => "Ruta no encontrada"], 404);
        
        case Dispatcher::METHOD_NOT_ALLOWED:
            json_response(["error" => "Método no permitido"], 405);
        
        case Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];

            // ✅ Si es una función anónima, la ejecutamos directamente
            if (is_callable($handler)) {
                json_response($handler());
            }

            // ✅ Si es un controlador, lo procesamos
            if (is_string($handler) && strpos($handler, '@') !== false) {
                [$controller, $method] = explode('@', $handler);
                $controllerFile = __DIR__ . '/../src/controllers/' . strtolower($controller) . '.php';

                if (!file_exists($controllerFile)) {
                    json_response(["error" => "Controlador no encontrado"], 500);
                }

                require_once $controllerFile;

                if (!class_exists($controller)) {
                    json_response(["error" => "Clase del controlador no encontrada"], 500);
                }

                $instance = new $controller();
                
                if (!method_exists($instance, $method)) {
                    json_response(["error" => "Método no encontrado en el controlador"], 500);
                }

                json_response($instance->$method($vars));
            }

            json_response(["error" => "Handler no válido"], 500);
    }
} catch (\Exception $e) {
    error_log("🚨 Excepción en el enrutador: " . $e->getMessage());
    json_response(["error" => "Error en la ejecución", "detalle" => $e->getMessage()], 500);
}
