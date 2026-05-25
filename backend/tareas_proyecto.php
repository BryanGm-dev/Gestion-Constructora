<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Constructora";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida a la base de datos."]));
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && isset($_GET['proyecto_id'])) {
    $proyecto_id = $_GET['proyecto_id'];

    $stmt = $conn->prepare("SELECT * FROM tareas_proyecto WHERE proyecto_id = ?");
    $stmt->bind_param("i", $proyecto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tareas = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($tareas);
    $stmt->close();
} elseif ($method === 'POST') {
    // Actualizar una tarea específica
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['tarea_id'], $data['nombre'], $data['descripcion'], $data['completada'])) {
        $tarea_id = $data['tarea_id'];
        $nombre = $data['nombre'];
        $descripcion = $data['descripcion'];
        $completada = $data['completada'] ? 1 : 0;

        $query = "UPDATE tareas_proyecto SET nombre = ?, descripcion = ?, completada = ? WHERE tarea_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssii", $nombre, $descripcion, $completada, $tarea_id);

        if ($stmt->execute()) {
            // Obtener el proyecto asociado a la tarea
            $proyecto_id_query = "SELECT proyecto_id FROM tareas_proyecto WHERE tarea_id = ?";
            $stmt_proyecto = $conn->prepare($proyecto_id_query);
            $stmt_proyecto->bind_param("i", $tarea_id);
            $stmt_proyecto->execute();
            $result_proyecto = $stmt_proyecto->get_result();
            $proyecto_id = $result_proyecto->fetch_assoc()['proyecto_id'];
            $stmt_proyecto->close();

            if ($proyecto_id) {
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

                $porcentajeAvance = $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0;

                // Actualizar el porcentaje de avance en la tabla Proyectos
                $stmtUpdate = $conn->prepare("UPDATE Proyectos SET porcentaje_avance = ? WHERE proyecto_id = ?");
                $stmtUpdate->bind_param("ii", $porcentajeAvance, $proyecto_id);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            }

            echo json_encode([
                "success" => true,
                "message" => "Tarea actualizada correctamente.",
                "porcentaje_avance" => $porcentajeAvance
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar la tarea."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Datos incompletos para actualizar la tarea."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}

$conn->close();
?>
