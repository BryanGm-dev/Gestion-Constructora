<?php
header('Content-Type: application/json');
// Configuración de CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
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
    // Obtener toda la materia prima disponible
    $sql = "SELECT materia_prima_id, nombre, cantidad_en_stock FROM materia_prima";
    $result = $conn->query($sql);
    
    $materiasPrimas = [];
    while ($row = $result->fetch_assoc()) {
        $materiasPrimas[] = $row;
    }
    
    echo json_encode($materiasPrimas);
} else {
    echo json_encode(["error" => "Método de solicitud no soportado."]);
}

// Cerrar la conexión
$conn->close();
?>
