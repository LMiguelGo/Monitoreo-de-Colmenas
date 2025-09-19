<?php
include "conexion.php";  // Contiene $host, $user, $pw, $db

$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Función para obtener y formatear un valor
function obtenerValor($mysqli, $id, $tipo) {
    $sql = "SELECT minimo, maximo FROM datos_maximos WHERE id=$id";
    $res = $mysqli->query($sql);
    if (!$res) {
        return "00";
    }
    $row = $res->fetch_assoc();
    $valor = $row[$tipo]; // "minimo" o "maximo"
    // Asegurar que siempre tenga 2 dígitos
    return str_pad($valor, 2, "0", STR_PAD_LEFT);
}

// --- Obtener valores de la tabla ---
// id=1 → temperatura
$temp_min = obtenerValor($mysqli, 1, "minimo");
$temp_max = obtenerValor($mysqli, 1, "maximo");

// id=2 → humedad
$hum_min = obtenerValor($mysqli, 2, "minimo");
$hum_max = obtenerValor($mysqli, 2, "maximo");

// id=3 → actividad
$act_min = obtenerValor($mysqli, 3, "minimo");
$act_max = obtenerValor($mysqli, 3, "maximo");

// --- Concatenar salida con comas ---
echo $temp_min . "," . $temp_max . "," . $hum_min . "," . $hum_max . "," . $act_min . "," . $act_max;
?>
