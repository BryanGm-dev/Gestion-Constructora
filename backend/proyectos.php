<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
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

if ($method == 'GET') {
    $sql = "SELECT * FROM Proyectos";
    $result = $conn->query($sql);

    $proyectos = [];
    while ($row = $result->fetch_assoc()) {
        if ($row['imagen']) {
            $row['imagen'] = "data:image/jpeg;base64," . base64_encode($row['imagen']);
        }
        $proyectos[] = $row;
    }

    echo json_encode($proyectos);
}

if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $imagen = isset($data['imagen']) ? base64_decode($data['imagen']) : null;

    $stmt = $conn->prepare("INSERT INTO Proyectos (nombre, descripcion, ubicacion, fecha_inicio, fecha_fin, fecha_reprogramada, estado, inversion_inicial, inversion_final, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssd", $data['nombre'], $data['descripcion'], $data['ubicacion'], $data['fecha_inicio'], $data['fecha_fin'], $data['fecha_reprogramada'], $data['estado'], $data['inversion_inicial'], $data['inversion_final'], $imagen);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Proyecto creado exitosamente."]);
    } else {
        echo json_encode(["error" => "Error al crear proyecto."]);
    }
    $stmt->close();
}

if ($method == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $proyecto_id = $data['proyecto_id'] ?? null;
    $nuevo_estado = $data['estado'] ?? null;

    if ($proyecto_id) {
        // Verificamos si la imagen está presente en los datos recibidos
        $imagen = isset($data['imagen']) ? base64_decode($data['imagen']) : null;

        // Actualización de la tabla Proyectos
        if ($imagen !== null) {
            $stmtUpdateProyecto = $conn->prepare(
                "UPDATE Proyectos SET nombre=?, descripcion=?, ubicacion=?, fecha_inicio=?, fecha_fin=?, 
                fecha_reprogramada=?, estado=?, inversion_inicial=?, inversion_final=?, imagen=? 
                WHERE proyecto_id=?"
            );
            $stmtUpdateProyecto->bind_param(
                "ssssssssssi",
                $data['nombre'], $data['descripcion'], $data['ubicacion'], $data['fecha_inicio'],
                $data['fecha_fin'], $data['fecha_reprogramada'], $nuevo_estado, $data['inversion_inicial'],
                $data['inversion_final'], $imagen, $proyecto_id
            );
        } else {
            $stmtUpdateProyecto = $conn->prepare(
                "UPDATE Proyectos SET nombre=?, descripcion=?, ubicacion=?, fecha_inicio=?, fecha_fin=?, 
                fecha_reprogramada=?, estado=?, inversion_inicial=?, inversion_final=? 
                WHERE proyecto_id=?"
            );
            $stmtUpdateProyecto->bind_param(
                "sssssssssi",
                $data['nombre'], $data['descripcion'], $data['ubicacion'], $data['fecha_inicio'],
                $data['fecha_fin'], $data['fecha_reprogramada'], $nuevo_estado, $data['inversion_inicial'],
                $data['inversion_final'], $proyecto_id
            );
        }

        if ($stmtUpdateProyecto->execute()) {
            $response["message"] = "Proyecto actualizado exitosamente.";

            // Si el estado cambia a "PF", actualizamos la maquinaria asociada
            if ($nuevo_estado === "PF") {
                $stmtUpdateMaquinaria = $conn->prepare(
                    "UPDATE maquinaria m
                    JOIN proyectos_maquinaria pm ON m.maquinaria_id = pm.maquinaria_id
                    SET m.estado = 'Disponible'
                    WHERE pm.proyecto_id = ?"
                );
                $stmtUpdateMaquinaria->bind_param("i", $proyecto_id);

                if ($stmtUpdateMaquinaria->execute()) {
                    $response["maquinaria_message"] = "Maquinaria liberada lista para usar en otro proyecto."; // Mensaje adicional
                } else {
                    $response["error"] = "Error al actualizar el estado de la maquinaria.";
                }
                $stmtUpdateMaquinaria->close();
            }
        } else {
            $response["error"] = "Error al actualizar el proyecto.";
        }
        $stmtUpdateProyecto->close();
    } else {
        $response["error"] = "Datos insuficientes para realizar la actualización.";
    }

    echo json_encode($response);
}



if ($method == 'DELETE') {
    $input = json_decode(file_get_contents("php://input"), true);
    $proyecto_id = $input['proyecto_id'] ?? null;

    if ($proyecto_id) {
        $stmt = $conn->prepare("DELETE FROM Proyectos WHERE proyecto_id = ?");
        $stmt->bind_param("i", $proyecto_id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Proyecto eliminado correctamente"]);
        } else {
            echo json_encode(["error" => "Error al eliminar proyecto"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "ID de proyecto no proporcionado"]);
    }
}

$conn->close();
?>
