<?php
session_start();

include "../config/conexion_bd.php"; 

$login = $_POST["login1"];
$passwd = $_POST["passwd1"];
$tipo_usuario = $_POST["tipo_usuario"];

// Encriptar la contraseña como en el registro
$passwd_comp = md5($passwd);

// Conexión a la BD
$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Error en la conexión a MySQL: " . $mysqli->connect_error);
}

// Definir la tabla según el tipo de usuario
if ($tipo_usuario == "apicultor") {
    $tabla = "apicultores";
} else {
    $tabla = "investigadores";
}

// Consultar usuario
$sql = "SELECT * FROM $tabla WHERE user = '$login'";
$result = $mysqli->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_array(MYSQLI_ASSOC);

    // Comparar contraseñas
    if ($row["password"] === $passwd_comp) {
        $_SESSION["autenticado"] = "SIx3";
        $_SESSION["tipo_usuario"] = $tipo_usuario;
        $_SESSION["nombre_usuario"] = $row["nombre_completo"];
        $_SESSION["id_usuario"] = $row["id"];

        // Redirección según rol
        if ($tipo_usuario == "apicultor") {
            header("Location: ../../apicultor/inicio_apicultor.php");
        } else {
            header("Location: ../../investigador/tabla_estatica.php");
        }
        exit;
    } else {
        // Password incorrecta
        header("Location: ../index.php?mensaje=1");
        exit;
    }
} else {
    // Usuario no encontrado
    header("Location: ../index.php?mensaje=2");
    exit;
}
?>
