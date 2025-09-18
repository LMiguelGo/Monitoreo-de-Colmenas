<?php
// PROGRAMA DE MENU ADMINISTRADOR
include "conexion.php";

session_start();
if ($_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
    exit;
} else {
    $mysqli = new mysqli($host, $user, $pw, $db);
    $sqlusu = "SELECT * FROM tipo_usuario WHERE id='1'"; // ADMINISTRADOR
    $resultusu = $mysqli->query($sqlusu);
    $rowusu = $resultusu->fetch_array(MYSQLI_NUM);
    $desc_tipo_usuario = $rowusu[1];
    if ($_SESSION["tipo_usuario"] != $desc_tipo_usuario) {
        header('Location: index.php?mensaje=4');
        exit;
    }
}

// Datos de temperatura
$sql = "SELECT AVG(temperatura) as count FROM datos_medidos GROUP BY DAY(fecha) ORDER BY fecha";
$tempe = mysqli_query($mysqli, $sql);
$tempe = mysqli_fetch_all($tempe, MYSQLI_ASSOC);
$tempe = json_encode(array_column($tempe, 'count'), JSON_NUMERIC_CHECK);

// Datos de humedad
$sql = "SELECT AVG(humedad) as count FROM datos_medidos GROUP BY DAY(fecha) ORDER BY fecha";
$hume = mysqli_query($mysqli, $sql);
$hume = mysqli_fetch_all($hume, MYSQLI_ASSOC);
$hume = json_encode(array_column($hume, 'count'), JSON_NUMERIC_CHECK);

$category = ["febrero 13 de 2019", "febrero 14 de 2019", "febrero 15 de 2019"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar Informes</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff8f0;
            margin: 0;
            padding: 0;
            color: #333;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ff9900;
            padding: 10px 20px;
            color: #fff;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        header .user-info {
            text-align: right;
            font-size: 14px;
        }
        header .user-info a {
            display: inline-block;
            margin-top: 6px;
            background: #fff;
            color: #cc6600;
            font-weight: bold;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
        }
        .content {
            padding: 20px;
            max-width: 1000px;
            margin: auto;
        }
        .content h2 {
            color: #ff6600;
            text-align: center;
        }
        .chart-panel {
            background-color: #fff3e0;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            margin-bottom: 20px;
        }
        table img {
            border-radius: 5px;
        }
        hr {
            border: none;
            height: 2px;
            background-color: #ff9900;
            margin: 40px 0;
        }
    </style>
</head>
<body>

<header>
    <h1>Sistema de Monitoreo de Colmenas</h1>
    <div class="user-info">
        <p><strong>Usuario:</strong> <?php echo $_SESSION["nombre_usuario"]; ?></p>
        <p><strong>Tipo:</strong> <?php echo $desc_tipo_usuario; ?></p>
        <a href="cerrar_sesion.php" class="btn">Cerrar Sesión</a>
    </div>
</header>

<div class="content">
    <?php include "menu_admin.php"; ?>
    
    <h2>Generar Informes</h2>
    <div class="chart-panel">
        <div id="container" style="height: 400px;"></div>
    </div>
</div>

<script>
$(function () { 
    Highcharts.chart('container', {
        chart: { type: 'line' },
        title: { text: 'Temperatura y Humedad promedio por día' },
        xAxis: { categories: <?= json_encode($category) ?> },
        yAxis: { title: { text: 'Valor promedio' } },
        series: [
            { name: 'Temperatura', data: <?= $tempe ?>, color: '#ff6600' },
            { name: 'Humedad', data: <?= $hume ?>, color: '#ff9933' }
        ],
        credits: { enabled: false }
    });
});
</script>

</body>
</html>
