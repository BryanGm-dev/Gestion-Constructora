<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json"); // Indicar que la respuesta es JSON

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuración de la conexión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Constructora";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida a la base de datos."]));
}

// Consulta SQL para obtener los roles
$sql = "SELECT rol_id, nombre FROM roles";
$result = $conn->query($sql);

// Preparar los datos en formato JSON
$roles = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $roles[] = $row;
    }
    echo json_encode($roles); // Convertir el arreglo en JSON
} else {
    echo json_encode(["message" => "No se encontraron roles."]);
}

// Cerrar conexión
$conn->close();
?>
