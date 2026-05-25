<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

class Database {
    private $host = "localhost";
    private $db_name = "constructora";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo json_encode(["message" => "Error de conexión: " . $exception->getMessage()]);
        }
        return $this->conn;
    }
}

// Obtener conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Leer el método HTTP
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Decodificar JSON en caso de recibir datos
$data = json_decode(file_get_contents("php://input"));

// Definir el comportamiento basado en el método HTTP
switch ($method) {
    case 'POST':
        // CREATE - Crear un nuevo registro
        if (!empty($data->nombre) && !empty($data->estado)) {
            $query = "INSERT INTO maquinaria (nombre, descripcion, estado, fecha_adquisicion, costo, tipo) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            if ($stmt->execute([$data->nombre, $data->descripcion, $data->estado, $data->fecha_adquisicion, $data->costo, $data->tipo])) {
                echo json_encode(["message" => "Registro creado correctamente."]);
            } else {
                echo json_encode(["message" => "Error al crear el registro."]);
            }
        } else {
            echo json_encode(["message" => "Faltan datos obligatorios."]);
        }
        break;

    case 'GET':
        // READ - Obtener todos los registros, uno específico, o el conteo total
        if ($action == 'count') {
            // Obtener el conteo total de registros en maquinaria
            $query = "SELECT COUNT(*) as total FROM maquinaria";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($result ? $result : ["message" => "Error al contar registros."]);
        } elseif (isset($_GET['maquinaria_id'])) {
            // Obtener un registro específico
            $query = "SELECT * FROM maquinaria WHERE maquinaria_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$_GET['maquinaria_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($result ? $result : ["message" => "Registro no encontrado."]);
        } else {
            // Obtener todos los registros
            $query = "SELECT * FROM maquinaria";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result);
        }
        break;

    case 'PUT':
        // UPDATE - Actualizar un registro existente
        if (!empty($data->maquinaria_id) && !empty($data->nombre) && !empty($data->estado)) {
            $query = "UPDATE maquinaria SET nombre = ?, descripcion = ?, estado = ?, fecha_adquisicion = ?, costo = ?, tipo = ? WHERE maquinaria_id = ?";
            $stmt = $db->prepare($query);
            if ($stmt->execute([$data->nombre, $data->descripcion, $data->estado, $data->fecha_adquisicion, $data->costo, $data->tipo, $data->maquinaria_id])) {
                echo json_encode(["message" => "Registro actualizado correctamente."]);
            } else {
                echo json_encode(["message" => "Error al actualizar el registro."]);
            }
        } else {
            echo json_encode(["message" => "Faltan datos obligatorios."]);
        }
        break;

    case 'DELETE':
        // DELETE - Eliminar un registro
        if (!empty($data->maquinaria_id)) {
            $query = "DELETE FROM maquinaria WHERE maquinaria_id = ?";
            $stmt = $db->prepare($query);
            if ($stmt->execute([$data->maquinaria_id])) {
                echo json_encode(["message" => "Registro eliminado correctamente."]);
            } else {
                echo json_encode(["message" => "Error al eliminar el registro."]);
            }
        } else {
            echo json_encode(["message" => "ID de maquinaria requerido."]);
        }
        break;

    default:
        // Método no soportado
        echo json_encode(["message" => "Método no permitido."]);
        break;
}
?>
