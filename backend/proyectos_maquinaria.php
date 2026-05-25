<?php
header('Content-Type: application/json');
// Configuración de CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "constructora";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida a la base de datos: " . $conn->connect_error]));
}

// Obtener el método de solicitud
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Obtener maquinaria asignada a un proyecto específico
    $proyecto_id = $_GET['proyecto_id'] ?? null;
    if ($proyecto_id) {
        $sql = "SELECT pm.proyecto_maquinaria_id, m.nombre, m.descripcion, pm.fecha_asignacion, pm.fecha_liberacion
                FROM proyectos_maquinaria pm
                JOIN maquinaria m ON pm.maquinaria_id = m.maquinaria_id
                WHERE pm.proyecto_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $proyecto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $maquinaria = [];
        while ($row = $result->fetch_assoc()) {
            $maquinaria[] = $row;
        }
        echo json_encode($maquinaria);
        $stmt->close();
    } else {
        echo json_encode(["error" => "ID del proyecto no proporcionado."]);
    }
} elseif ($method == 'POST') {
    // Leer y decodificar el cuerpo JSON de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar que la decodificación fue exitosa
    if (is_null($data)) {
        echo json_encode(["error" => "No se pudo decodificar el cuerpo de la solicitud."]);
        exit;
    }

    // Verifica si es una solicitud de eliminación simulada
    if (isset($data['_method']) && $data['_method'] === 'DELETE' && isset($data['proyecto_maquinaria_id'])) {
        $proyecto_maquinaria_id = $data['proyecto_maquinaria_id'];

        // Iniciar una transacción para asegurar la integridad de los datos 
        $conn->begin_transaction();
        try {
            // Obtener el maquinaria_id del registro que se eliminará
            $stmt = $conn->prepare("SELECT maquinaria_id FROM proyectos_maquinaria WHERE proyecto_maquinaria_id = ?");
            $stmt->bind_param("i", $proyecto_maquinaria_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $maquinaria_id = $row['maquinaria_id'];

                // Eliminar la asignación de maquinaria en `proyectos_maquinaria`
                $stmt = $conn->prepare("DELETE FROM proyectos_maquinaria WHERE proyecto_maquinaria_id = ?");
                $stmt->bind_param("i", $proyecto_maquinaria_id);

                if ($stmt->execute()) {
                    // Actualizar el estado de la maquinaria a 'Disponible'
                    $stmt = $conn->prepare("UPDATE maquinaria SET estado = 'Disponible' WHERE maquinaria_id = ?");
                    $stmt->bind_param("i", $maquinaria_id);

                    if ($stmt->execute()) {
                        // Confirmar la transacción
                        $conn->commit();
                        echo json_encode(["message" => "Maquinaria eliminada del proyecto y estado actualizado a Disponible."]);
                    } else {
                        // Revertir si la actualización de estado falla
                        $conn->rollback();
                        echo json_encode(["error" => "Error al actualizar el estado de la maquinaria: " . $stmt->error]);
                    }
                } else {
                    // Revertir si la eliminación falla
                    $conn->rollback();
                    echo json_encode(["error" => "Error al eliminar maquinaria del proyecto: " . $stmt->error]);
                }
            } else {
                $conn->rollback();
                echo json_encode(["error" => "No se encontró maquinaria asociada al registro."]);
            }
            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["error" => "Transacción fallida: " . $e->getMessage()]);
        }
    } 
    // Manejo para asignar nueva maquinaria
    elseif (isset($data['proyecto_id'], $data['maquinaria_id'], $data['fecha_asignacion'])) {
        $proyecto_id = $data['proyecto_id'];
        $maquinaria_id = $data['maquinaria_id'];
        $fecha_asignacion = $data['fecha_asignacion'];
        $fecha_liberacion = $data['fecha_liberacion'] ?? null;

        // Iniciar una transacción para asegurar la integridad de los datos
        $conn->begin_transaction();
        try {
            // Insertar la asignación en `proyectos_maquinaria`
            $stmt = $conn->prepare("INSERT INTO proyectos_maquinaria (proyecto_id, maquinaria_id, fecha_asignacion, fecha_liberacion) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $proyecto_id, $maquinaria_id, $fecha_asignacion, $fecha_liberacion);

            if ($stmt->execute()) {
                // Actualizar el estado de la maquinaria a 'En uso'
                $stmt = $conn->prepare("UPDATE maquinaria SET estado = 'En uso' WHERE maquinaria_id = ?");
                $stmt->bind_param("i", $maquinaria_id);

                if ($stmt->execute()) {
                    // Confirmar la transacción
                    $conn->commit();
                    echo json_encode(["message" => "Maquinaria asignada correctamente y estado actualizado a En uso."]);
                } else {
                    // Revertir si la actualización de estado falla
                    $conn->rollback();
                    echo json_encode(["error" => "Error al actualizar el estado de la maquinaria: " . $stmt->error]);
                }
            } else {
                // Revertir si la inserción falla
                $conn->rollback();
                echo json_encode(["error" => "Error al asignar maquinaria: " . $stmt->error]);
            }
            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["error" => "Transacción fallida: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["error" => "Datos incompletos para asignar o eliminar maquinaria."]);
    }
} else {
    echo json_encode(["error" => "Método de solicitud no soportado."]);
}

// Cerrar la conexión
$conn->close();
?>
