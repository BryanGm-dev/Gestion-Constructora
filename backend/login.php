<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "constructora";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida a la base de datos."]));
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $login = $data['login'] ?? null; // Puede ser nombre de usuario o correo electrónico
    $password = $data['password'] ?? null;

    if (!$login || !$password) {
        echo json_encode(["error" => "Datos incompletos."]);
        exit;
    }

    // Consulta para obtener el usuario por nombre de usuario o correo
    $stmt = $conn->prepare("SELECT usuario_id, username, email, password_hash FROM usuarios WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["error" => "Usuario o correo no encontrado."]);
        exit;
    }

    $user = $result->fetch_assoc();

    // Verificación de contraseña
    if (password_verify($password, $user['password_hash'])) {
        echo json_encode([
            "message" => "Inicio de sesión exitoso.",
            "user" => [
                "usuario_id" => $user['usuario_id'],
                "username" => $user['username'],
                "email" => $user['email']
            ]
        ]);
    } else {
        echo json_encode(["error" => "Contraseña incorrecta."]);
    }

    $stmt->close();
}

$conn->close();
?>
