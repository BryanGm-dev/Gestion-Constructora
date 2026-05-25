<?php
header('Content-Type: application/json');
// Configuración de CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Constructora";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida a la base de datos."]));
}

// Obtener el método de solicitud
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Obtener todas las tareas
    $sql = "SELECT * FROM tareas_proyecto";
    $result = $conn->query($sql);

    $tareas = [];
    while ($row = $result->fetch_assoc()) {
        $tareas[] = $row;
    }

    echo json_encode($tareas);
}

if ($method == 'POST') {
    // Leer y decodificar el cuerpo JSON de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);

    if (
        isset($data['proyecto_id'], $data['nombre'], $data['descripcion'], $data['completada'])
    ) {
        // Preparar la consulta de inserción
        $stmt = $conn->prepare("INSERT INTO tareas_proyecto (proyecto_id, nombre, descripcion, completada) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $data['proyecto_id'], $data['nombre'], $data['descripcion'], $data['completada']);

        // Ejecutar la consulta y verificar si fue exitosa
        if ($stmt->execute()) {
            // ID del proyecto asociado
            $proyecto_id = $data['proyecto_id'];

            // Recalcular el porcentaje de avance
            $sqlTotal = "SELECT COUNT(*) as total FROM tareas_proyecto WHERE proyecto_id = ?";
            $stmtTotal = $conn->prepare($sqlTotal);
            $stmtTotal->bind_param("i", $proyecto_id);
            $stmtTotal->execute();
            $resultTotal = $stmtTotal->get_result();
            $totalTareas = $resultTotal->fetch_assoc()['total'];
            $stmtTotal->close();

            $sqlCompleted = "SELECT COUNT(*) as completed FROM tareas_proyecto WHERE proyecto_id = ? AND completada = 1";
            $stmtCompleted = $conn->prepare($sqlCompleted);
            $stmtCompleted->bind_param("i", $proyecto_id);
            $stmtCompleted->execute();
            $resultCompleted = $stmtCompleted->get_result();
            $tareasCompletadas = $resultCompleted->fetch_assoc()['completed'];
            $stmtCompleted->close();

            // Calcular el porcentaje
            $porcentajeAvance = $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0;

            // Actualizar el porcentaje en la tabla Proyectos
            $stmtUpdate = $conn->prepare("UPDATE Proyectos SET porcentaje_avance = ? WHERE proyecto_id = ?");
            $stmtUpdate->bind_param("ii", $porcentajeAvance, $proyecto_id);
            $stmtUpdate->execute();
            $stmtUpdate->close();

            // Devolver el nuevo porcentaje
            echo json_encode([
                "message" => "Tarea creada exitosamente.",
                "porcentaje_avance" => $porcentajeAvance
            ]);
        } else {
            echo json_encode(["error" => "Error al crear tarea."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos para crear tarea"]);
    }
}




if ($method == 'DELETE') {
    // Leer y decodificar el cuerpo JSON de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);
    $tarea_id = $data['tarea_id'] ?? null;

    if ($tarea_id) {
        // Preparar la consulta de eliminación
        $stmt = $conn->prepare("DELETE FROM tareas_proyecto WHERE tarea_id = ?");
        $stmt->bind_param("i", $tarea_id);

        // Ejecutar la consulta y verificar si fue exitosa
        if ($stmt->execute()) {
            echo json_encode(["message" => "Tarea eliminada correctamente"]);
        } else {
            echo json_encode(["error" => "Error al eliminar tarea"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "ID de tarea no proporcionado"]);
    }
}

// Cerrar la conexión
$conn->close();
?>
