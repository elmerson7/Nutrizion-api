<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

foreach ($_ENV as $key => $value) {
    putenv("$key=$value"); // 🔥 Forzar carga manual en el entorno
}