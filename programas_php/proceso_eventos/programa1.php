<?php
include "conexion.php";  // Conexión con la base de datos

$hum   = $_GET["humedad"];      // Humedad recibida
$temp  = $_GET["temperatura"];  // Temperatura recibida
$ID_TARJ = $_GET["ID_TARJ"];    // ID de la tarjeta

// Nuevos parámetros para actividad
$act_in  = $_GET["actividad_in"];   // Actividad de entrada
$act_out = $_GET["actividad_out"];  // Actividad de salida

$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Fallo la conexión a MySQL: " . $mysqli->connect_error);
}

// Insertar en la tabla incluyendo las nuevas columnas
$sql1 = "INSERT INTO datos_medidos 
         (ID_TARJ, temperatura, humedad, actividad_in, actividad_out, fecha, hora) 
         VALUES 
         ('$ID_TARJ', '$temp', '$hum', '$act_in', '$act_out', CURDATE(), CURTIME())";

echo "sql1...".$sql1."<br>"; // Debug para verificar la consulta generada

$result1 = $mysqli->query($sql1);

echo "result es...".$result1; // Si es 1, el ingreso fue correcto
?>
