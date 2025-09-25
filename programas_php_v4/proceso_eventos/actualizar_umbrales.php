<?php
include "conexion.php";  // Contiene $host, $user, $pw, $db

$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// --- Recibir ID de la colmena ---
if (!isset($_GET['colmena_id'])) {
    die("Error: falta colmena_id");
}
$colmena_id = intval($_GET['colmena_id']); // Sanitizar

// --- Obtener los umbrales de esa colmena ---
$sql = "SELECT temp_min, temp_max, hum_min, hum_max, activ_min, activ_max
        FROM umbrales
        WHERE colmena_id = $colmena_id
        LIMIT 1";

$res = $mysqli->query($sql);
if (!$res || $res->num_rows === 0) {
    die("Error: no se encontraron umbrales para la colmena");
}

$row = $res->fetch_assoc();

// --- Asegurar formato con 2 dígitos donde aplique ---
$temp_min = str_pad($row['temp_min'], 2, "0", STR_PAD_LEFT);
$temp_max = str_pad($row['temp_max'], 2, "0", STR_PAD_LEFT);
$hum_min  = str_pad($row['hum_min'], 2, "0", STR_PAD_LEFT);
$hum_max  = str_pad($row['hum_max'], 2, "0", STR_PAD_LEFT);
$act_min  = str_pad($row['activ_min'], 2, "0", STR_PAD_LEFT);
$act_max  = str_pad($row['activ_max'], 2, "0", STR_PAD_LEFT);

// --- Concatenar salida con comas ---
echo $temp_min . "," . $temp_max . "," . $hum_min . "," . $hum_max . "," . $act_min . "," . $act_max;
?>
