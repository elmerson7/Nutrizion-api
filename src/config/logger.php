<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

function getLogger() {
    static $logger = null;

    if ($logger === null) {
        $logger = new Logger('api');

        // 🔥 Carpeta de logs organizada por año y mes
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $logDirectory = __DIR__ . "/../../storage/logs/$year/$month";
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0777, true);
        }

        $logFile = "$logDirectory/$day.log";

        // 🔥 Formato similar a error_log() → [Fecha] [Nivel] Mensaje
        $format = "[%datetime%] %level_name%: %message% %context%\n";
        $formatter = new LineFormatter($format, "Y-m-d H:i:s", true, true);

        $handler = new StreamHandler($logFile, Logger::DEBUG);
        $handler->setFormatter($formatter);

        $logger->pushHandler($handler);
    }

    return $logger;
}
