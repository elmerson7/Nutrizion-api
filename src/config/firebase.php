<?php

use Kreait\Firebase\Factory;

// ✅ Obtener credenciales desde el .env
$firebaseCredentials = getenv('FIREBASE_CREDENTIALS');

// ✅ Convertir la ruta relativa a absoluta si es necesario
if ($firebaseCredentials && !file_exists($firebaseCredentials)) {
    $firebaseCredentials = __DIR__ . '/../../' . ltrim($firebaseCredentials, '/');
}

// ✅ Verificar si el archivo de credenciales existe
if (!$firebaseCredentials || !file_exists($firebaseCredentials)) {
    error_log("🚨 Archivo de credenciales Firebase no encontrado en: $firebaseCredentials");
    json_response(["error" => "Archivo de credenciales Firebase no encontrado"], 500);
}

// ✅ Intentar conectar con Firestore
try {
    $factory = (new Factory)
        ->withServiceAccount($firebaseCredentials)
        ->withDatabaseUri(getenv('FIREBASE_DATABASE_URL'));

    $firestore = $factory->createFirestore();
    $database = $firestore->database();
} catch (\Exception $e) {
    error_log("🚨 Error al conectar con Firestore: " . $e->getMessage());
    json_response(["error" => "Error al conectar con Firestore", "detalle" => $e->getMessage()], 500);
}
