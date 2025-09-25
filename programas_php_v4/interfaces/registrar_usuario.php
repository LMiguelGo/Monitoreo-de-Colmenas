<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_completo = $_POST["nombre_completo"];
    $numero_id = $_POST["numero_id"];
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $direccion = $_POST["direccion"];
    $user1 = $_POST["user"];
    $password = md5($_POST["password"]); // Encriptamos con MD5
    $tipo_usuario = $_POST["tipo_usuario"];

    $mysqli = new mysqli($host, $user, $pw, $db);

    if ($mysqli->connect_errno) {
        die("Error en la conexión a MySQL: " . $mysqli->connect_error);
    }

    if ($tipo_usuario == "apicultor") {
        $sql = "INSERT INTO apicultores 
                (nombre_completo, numero_id, fecha_nacimiento, direccion, user, password) 
                VALUES ('$nombre_completo', '$numero_id', '$fecha_nacimiento', '$direccion', '$user1', '$password')";
    } else if ($tipo_usuario == "investigador") {
        $sql = "INSERT INTO investigadores 
                (nombre_completo, numero_id, fecha_nacimiento, direccion, user, password) 
                VALUES ('$nombre_completo', '$numero_id', '$fecha_nacimiento', '$direccion', '$user1', '$password')";
    } else {
        die("Tipo de usuario inválido.");
    }

    if ($mysqli->query($sql)) {
        echo "<script>alert('Usuario registrado exitosamente'); window.location.href='index.php';</script>";
    } else {
        echo "Error: " . $mysqli->error;
    }

    $mysqli->close();
}
?>
