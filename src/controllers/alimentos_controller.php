<?php 

require_once __DIR__ . '/../config/firebase.php';
require_once __DIR__ . '/../helpers.php';

class alimentos_controller
{
    private $database;

    public function __construct() {
        global $database;
        if (!$database) {
            json_response(["error" => "Firestore no está disponible"], 500);
        }
        $this->database = $database;
    }

    // ✅ Obtener todos los alimentos
    public function index() {
        try {
            $collection = $this->database->collection('alimentos');
            $documents = $collection->documents();

            $data = [];
            foreach ($documents as $document) {
                $data[] = array_merge(["id" => $document->id()], $document->data());
            }

            json_response(["success" => true, "alimentos" => $data]);
        } catch (\Exception $e) {
            json_response(["error" => "Error al obtener alimentos", "detalle" => $e->getMessage()], 500);
        }
    }

    // ✅ Crear un nuevo alimento
    public function store() {
        try {
            $input = file_get_contents('php://input');

            if (!is_json($input)) {
                json_response(["error" => "El cuerpo de la petición no es JSON válido"], 400);
            }

            $data = json_decode($input, true);
            if (!$data) {
                json_response(["error" => "Datos inválidos"], 400);
            }

            $collection = $this->database->collection('alimentos');
            $document = $collection->add($data);

            json_response(["success" => true, "message" => "Alimento agregado", "id" => $document->id()], 201);
        } catch (\Exception $e) {
            json_response(["error" => "Error al agregar alimento", "detalle" => $e->getMessage()], 500);
        }
    }

    // ✅ Obtener un alimento por ID
    public function show($vars) {
        try {
            $id = clean_input($vars['id'] ?? '');
            if (empty($id)) {
                json_response(["error" => "ID no proporcionado"], 400);
            }

            $document = $this->database->collection('alimentos')->document($id)->snapshot();

            if (!$document->exists()) {
                json_response(["error" => "Alimento no encontrado"], 404);
            }

            json_response(["success" => true, "alimento" => array_merge(["id" => $id], $document->data())]);
        } catch (\Exception $e) {
            json_response(["error" => "Error al obtener el alimento", "detalle" => $e->getMessage()], 500);
        }
    }

    // ✅ Actualizar un alimento
    public function update($vars) {
        try {
            $id = clean_input($vars['id'] ?? '');
            if (empty($id)) {
                json_response(["error" => "ID no proporcionado"], 400);
            }

            $input = file_get_contents('php://input');
            if (!is_json($input)) {
                json_response(["error" => "El cuerpo de la petición no es JSON válido"], 400);
            }

            $data = json_decode($input, true);
            if (!$data) {
                json_response(["error" => "Datos inválidos"], 400);
            }

            $document = $this->database->collection('alimentos')->document($id);
            $document->set($data, ['merge' => true]);

            json_response(["success" => true, "message" => "Alimento actualizado"]);
        } catch (\Exception $e) {
            json_response(["error" => "Error al actualizar el alimento", "detalle" => $e->getMessage()], 500);
        }
    }

    // ✅ Eliminar un alimento
    public function destroy($vars) {
        try {
            $id = clean_input($vars['id'] ?? '');
            if (empty($id)) {
                json_response(["error" => "ID no proporcionado"], 400);
            }

            $document = $this->database->collection('alimentos')->document($id);
            $snapshot = $document->snapshot();

            if (!$snapshot->exists()) {
                json_response(["error" => "Alimento no encontrado"], 404);
            }

            // 🔥 Guardar en logs la IP y el alimento eliminado antes de borrarlo
            $deletedData = $snapshot->data();
            $ip = get_client_ip();
            error_log("Usuario con IP $ip eliminó el alimento ID: $id. Datos: " . json_encode($deletedData));

            $document->delete();
            json_response(["success" => true, "message" => "Alimento eliminado"]);
        } catch (\Exception $e) {
            json_response(["error" => "Error al eliminar el alimento", "detalle" => $e->getMessage()], 500);
        }
    }
}
