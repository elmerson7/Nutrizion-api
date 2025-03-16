<?php

use FastRoute\RouteCollector;

return function (RouteCollector $router) {
    // Ruta de prueba
    $router->addRoute('GET', '/test', function() {
        return ["message" => "Ruta funcionando"];
    });

    // Rutas de alimentos
    $router->addRoute('GET', '/alimentos', 'alimentos_controller@index');
    $router->addRoute('POST', '/alimentos', 'alimentos_controller@store');
    $router->addRoute('GET', '/alimentos/{id}', 'alimentos_controller@show');
    $router->addRoute('PUT', '/alimentos/{id}', 'alimentos_controller@update');
    $router->addRoute('DELETE', '/alimentos/{id}', 'alimentos_controller@destroy');
};
