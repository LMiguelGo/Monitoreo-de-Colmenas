<?php
include "conexion.php";  // Conexión con la base de datos

$temp  = $_GET["temperatura"];      // Temperatura recibida
$hum   = $_GET["humedad"];          // Humedad recibida
$act_in  = $_GET["actividad_in"];   // Actividad de entrada
$act_out = $_GET["actividad_out"];  // Actividad de salida
$ID_TARJ = $_GET["ID_TARJ"];        // ID de la tarjeta

$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Fallo la conexión a MySQL: " . $mysqli->connect_error);
}

// Insertar en la tabla datos_medidos
$sql1 = "INSERT INTO datos_medidos 
         (fecha, hora, temperatura, humedad, actividad_entrante, actividad_saliente, colmena_id) 
         VALUES 
         (CURDATE(), CURTIME(), $temp, $hum, $act_in, $act_out, $ID_TARJ)";

// Debug para verificar la consulta generada
echo "sql1...".$sql1."<br>"; 
$result1 = $mysqli->query($sql1);
echo "result es...".$result1; 
?>
