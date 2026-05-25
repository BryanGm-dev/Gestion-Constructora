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
    if (isset($_GET['action']) && $_GET['action'] == 'count') {
        // Devolver solo el total de clientes
        $result = $conn->query("SELECT COUNT(*) as total FROM clientes");
        if ($result) {
            $row = $result->fetch_assoc();
            echo json_encode(["total" => $row['total']]);
        } else {
            echo json_encode(["error" => "Error al contar los clientes."]);
        }
    } else {
        // Consulta optimizada para obtener clientes y sus teléfonos en una sola operación
        $sql = "SELECT clientes.*, telefonos_clientes.numero_telefono 
                FROM clientes 
                LEFT JOIN telefonos_clientes ON clientes.cliente_id = telefonos_clientes.cliente_id";
        $result = $conn->query($sql);

        $clientes = [];
        while ($row = $result->fetch_assoc()) {
            $cliente_id = $row['cliente_id'];
            
            if (!isset($clientes[$cliente_id])) {
                // Crear una entrada para el cliente si no existe en el arreglo
                $clientes[$cliente_id] = [
                    "cliente_id" => $row['cliente_id'],
                    "nombre" => $row['nombre'],
                    "direccion" => $row['direccion'],
                    "email" => $row['email'],
                    "telefonos" => []
                ];
            }
            
            // Agregar el teléfono al arreglo de teléfonos
            if ($row['numero_telefono'] !== null) {
                $clientes[$cliente_id]["telefonos"][] = $row['numero_telefono'];
            }
        }

        echo json_encode(array_values($clientes)); // Convertir el arreglo asociativo a un arreglo indexado
    }
}

if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['nombre'], $data['direccion'], $data['email'], $data['telefono'])) {
        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("INSERT INTO clientes (nombre, direccion, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $data['nombre'], $data['direccion'], $data['email']);
            $stmt->execute();
            $cliente_id = $stmt->insert_id;
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO telefonos_clientes (cliente_id, numero_telefono) VALUES (?, ?)");
            $stmt->bind_param("is", $cliente_id, $data['telefono']);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            echo json_encode(["message" => "Cliente y teléfono creados exitosamente."]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["error" => "Error al crear cliente o teléfono: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["error" => "Datos incompletos para crear cliente"]);
    }
}

if ($method == 'POST' && isset($_GET['action']) && $_GET['action'] == 'add_phone') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['cliente_id'], $data['numero_telefono'])) {
        $stmt = $conn->prepare("INSERT INTO telefonos_clientes (cliente_id, numero_telefono) VALUES (?, ?)");
        $stmt->bind_param("is", $data['cliente_id'], $data['numero_telefono']);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Número de teléfono agregado exitosamente."]);
        } else {
            echo json_encode(["error" => "Error al agregar número de teléfono."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos para agregar teléfono"]);
    }
}

if ($method == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['cliente_id'], $data['nombre'], $data['direccion'], $data['email'])) {
        $stmt = $conn->prepare("UPDATE clientes SET nombre=?, direccion=?, email=? WHERE cliente_id=?");
        $stmt->bind_param("sssi", $data['nombre'], $data['direccion'], $data['email'], $data['cliente_id']);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Cliente actualizado exitosamente."]);
        } else {
            echo json_encode(["error" => "Error al actualizar cliente."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos para actualizar cliente"]);
    }
}

if ($method == 'DELETE') {
    $input = json_decode(file_get_contents("php://input"), true);
    $cliente_id = $input['cliente_id'] ?? null;

    if ($cliente_id) {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("DELETE FROM telefonos_clientes WHERE cliente_id = ?");
            $stmt->bind_param("i", $cliente_id);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("DELETE FROM clientes WHERE cliente_id = ?");
            $stmt->bind_param("i", $cliente_id);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            echo json_encode(["message" => "Cliente y teléfonos asociados eliminados correctamente"]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["error" => "Error al eliminar cliente o teléfonos asociados: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["error" => "ID de cliente no proporcionado"]);
    }
}

$conn->close();
?>
