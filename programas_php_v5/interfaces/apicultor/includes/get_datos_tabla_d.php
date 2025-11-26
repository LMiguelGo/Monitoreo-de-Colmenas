<?php
include "../config/conexion_bd.php";

$id_colmena = isset($_GET['id_colmena']) ? intval($_GET['id_colmena']) : 0;

if ($id_colmena <= 0) {
    echo "<p id='mensaje'>Colmena no válida.</p>";
    exit;
}

$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Traer solo los últimos 50 valores
$sql = "SELECT temperatura, humedad, actividad_entrante, actividad_saliente, fecha, hora
        FROM datos_medidos
        WHERE colmena_id = $id_colmena
        ORDER BY CONCAT(fecha, ' ', hora) DESC
        LIMIT 50";

$res = $mysqli->query($sql);

if ($res && $res->num_rows > 0) {
    echo "<table>
            <tr>
              <th>#</th>
              <th>Temperatura (°C)</th>
              <th>Humedad (%)</th>
              <th>Actividad Entrante</th>
              <th>Actividad Saliente</th>
              <th>Fecha</th>
              <th>Hora</th>
            </tr>";
    $n = 0;
    while ($row = $res->fetch_assoc()) {
        $n++;
        echo "<tr>
                <td>$n</td>
                <td>{$row['temperatura']}</td>
                <td>{$row['humedad']}</td>
                <td>{$row['actividad_entrante']}</td>
                <td>{$row['actividad_saliente']}</td>
                <td>{$row['fecha']}</td>
                <td>{$row['hora']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p id='mensaje'>No hay datos recientes para esta colmena.</p>";
}

$mysqli->close();
?>
