<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permite el acceso desde cualquier origen
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Constructora";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida a la base de datos."]));
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method == 'GET') {
    // Obtener todas las materias primas
    $sql = "SELECT * FROM materia_prima";
    $result = $conn->query($sql);

    $materias_primas = [];
    while ($row = $result->fetch_assoc()) {
        // Asegúrate de que costo_unitario sea un número
        $row['costo_unitario'] = $row['costo_unitario'] !== null ? (float)$row['costo_unitario'] : 0.0;
        $materias_primas[] = $row;
    }

    echo json_encode($materias_primas);
}

if ($method == 'POST' && $action === 'add_material') {
    // Agregar nueva materia prima
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['nombre'], $data['descripcion'], $data['costo_unitario'], $data['cantidad_en_stock'])) {
        $stmt = $conn->prepare("INSERT INTO materia_prima (nombre, descripcion, costo_unitario, cantidad_en_stock) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $data['nombre'], $data['descripcion'], $data['costo_unitario'], $data['cantidad_en_stock']);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Materia prima creada exitosamente."]);
        } else {
            echo json_encode(["error" => "Error al crear materia prima."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos para crear materia prima"]);
    }
}

if ($method == 'PUT') {
    // Actualizar materia prima existente
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['materia_prima_id'], $data['nombre'], $data['descripcion'], $data['costo_unitario'], $data['cantidad_en_stock'])) {
        $stmt = $conn->prepare("UPDATE materia_prima SET nombre=?, descripcion=?, costo_unitario=?, cantidad_en_stock=? WHERE materia_prima_id=?");
        $stmt->bind_param("ssdii", $data['nombre'], $data['descripcion'], $data['costo_unitario'], $data['cantidad_en_stock'], $data['materia_prima_id']);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Materia prima actualizada exitosamente."]);
        } else {
            echo json_encode(["error" => "Error al actualizar materia prima."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos para actualizar materia prima"]);
    }
}

if ($method == 'DELETE') {
    // Eliminar materia prima
    $data = json_decode(file_get_contents("php://input"), true);
    $materia_prima_id = $data['materia_prima_id'] ?? null;

    if ($materia_prima_id) {
        $stmt = $conn->prepare("DELETE FROM materia_prima WHERE materia_prima_id = ?");
        $stmt->bind_param("i", $materia_prima_id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Materia prima eliminada correctamente"]);
        } else {
            echo json_encode(["error" => "Error al eliminar materia prima"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "ID de materia prima no proporcionado"]);
    }
}

$conn->close();
?>
