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
    // Obtener materia prima asignada a un proyecto específico
    $proyecto_id = $_GET['proyecto_id'] ?? null;
    if ($proyecto_id) {
        $sql = "SELECT pmp.proyecto_materia_prima_id, mp.nombre, pmp.cantidad_utilizada, pmp.costo, pmp.fecha_utilizacion
                FROM proyectos_materia_prima pmp
                JOIN materia_prima mp ON pmp.materia_prima_id = mp.materia_prima_id
                WHERE pmp.proyecto_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $proyecto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $materiaPrima = [];
        while ($row = $result->fetch_assoc()) {
            $materiaPrima[] = $row;
        }
        echo json_encode($materiaPrima);
        $stmt->close();
    } else {
        echo json_encode(["error" => "ID del proyecto no proporcionado."]);
    }
} elseif ($method == 'POST') {
    // Leer y decodificar el cuerpo JSON de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);

    if (is_null($data)) {
        echo json_encode(["error" => "No se pudo decodificar el cuerpo de la solicitud."]);
        exit;
    }

    if (isset($data['_method']) && $data['_method'] === 'DELETE' && isset($data['proyecto_materia_prima_id'])) {
        $proyecto_materia_prima_id = $data['proyecto_materia_prima_id'];
    
        $stmt = $conn->prepare("DELETE FROM proyectos_materia_prima WHERE proyecto_materia_prima_id = ?");
        $stmt->bind_param("i", $proyecto_materia_prima_id);
    
        if ($stmt->execute()) {
            echo json_encode(["message" => "Materia prima eliminada correctamente."]);
        } else {
            echo json_encode(["error" => "Error al eliminar materia prima: " . $stmt->error]);
        }
        $stmt->close();
    } elseif (isset($data['proyecto_id'], $data['materia_prima_id'], $data['cantidad_utilizada'], $data['costo'], $data['fecha_utilizacion'])) {
        $proyecto_id = $data['proyecto_id'];
        $materia_prima_id = $data['materia_prima_id'];
        $cantidad_utilizada = $data['cantidad_utilizada'];
        $costo_unitario = $data['costo'];
        $fecha_utilizacion = $data['fecha_utilizacion'];

        // Calcular el costo total
        $costo_total = $cantidad_utilizada * $costo_unitario;

        // Verificar que haya suficiente stock
        $stmt = $conn->prepare("SELECT cantidad_en_stock FROM materia_prima WHERE materia_prima_id = ?");
        $stmt->bind_param("i", $materia_prima_id);
        $stmt->execute();
        $stmt->bind_result($cantidad_en_stock);
        $stmt->fetch();
        $stmt->close();

        if ($cantidad_en_stock < $cantidad_utilizada) {
            echo json_encode(["error" => "Stock insuficiente para la asignación de materia prima."]);
            exit;
        }

        // Iniciar una transacción para asegurar la integridad de los datos
        $conn->begin_transaction();
        try {
            // Insertar la asignación en `proyectos_materia_prima`
            $stmt = $conn->prepare("INSERT INTO proyectos_materia_prima (proyecto_id, materia_prima_id, cantidad_utilizada, costo, fecha_utilizacion) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiids", $proyecto_id, $materia_prima_id, $cantidad_utilizada, $costo_unitario, $fecha_utilizacion);

            if ($stmt->execute()) {
                // Restar la cantidad utilizada del stock en `materia_prima`
                $stmt = $conn->prepare("UPDATE materia_prima SET cantidad_en_stock = cantidad_en_stock - ? WHERE materia_prima_id = ?");
                $stmt->bind_param("ii", $cantidad_utilizada, $materia_prima_id);

                if ($stmt->execute()) {
                    // Actualizar el costo total en `proyectos`
                    $stmt = $conn->prepare("UPDATE proyectos SET inversion_inicial = inversion_inicial + ? WHERE proyecto_id = ?");
                    $stmt->bind_param("di", $costo_total, $proyecto_id);

                    if ($stmt->execute()) {
                        // Confirmar la transacción
                        $conn->commit();
                        echo json_encode(["message" => "Materia prima asignada correctamente, stock actualizado y costo agregado a la inversión inicial."]);
                    } else {
                        // Revertir si la actualización de inversión inicial falla
                        $conn->rollback();
                        echo json_encode(["error" => "Error al actualizar la inversión inicial: " . $stmt->error]);
                    }
                } else {
                    // Revertir si la actualización de stock falla
                    $conn->rollback();
                    echo json_encode(["error" => "Error al actualizar el stock: " . $stmt->error]);
                }
            } else {
                // Revertir si la inserción falla
                $conn->rollback();
                echo json_encode(["error" => "Error al asignar materia prima: " . $stmt->error]);
            }
            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["error" => "Transacción fallida: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["error" => "Datos incompletos para asignar o eliminar materia prima."]);
    }
} else {
    echo json_encode(["error" => "Método de solicitud no soportado."]);
}

// Cerrar la conexión
$conn->close();
?>
