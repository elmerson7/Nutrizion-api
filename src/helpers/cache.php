<?php

define('CACHE_FILE', __DIR__ . '/../../storage/cache/alimentos.json');

// ✅ Leer alimentos desde el cache
function get_cache() {
    if (!file_exists(CACHE_FILE)) {
        return null; // 🔥 No hay cache, se debe consultar Firestore
    }

    $jsonData = file_get_contents(CACHE_FILE);
    $data = json_decode($jsonData, true);

    // 🔥 Si el JSON es inválido o está corrupto, eliminarlo y devolver `null`
    if (json_last_error() !== JSON_ERROR_NONE) {
        unlink(CACHE_FILE);
        return null;
    }

    return $data;
}

// ✅ Guardar la lista de alimentos en el cache
function update_cache($alimentos) {
    file_put_contents(CACHE_FILE . '.tmp', json_encode($alimentos, JSON_PRETTY_PRINT));
    rename(CACHE_FILE . '.tmp', CACHE_FILE); // 🔥 Evita corrupción del archivo
}

// ✅ Eliminar el cache cuando se haga `POST`, `PUT` o `DELETE`
function clear_cache() {
    if (file_exists(CACHE_FILE)) {
        unlink(CACHE_FILE);
    }
}
