<?php
// PROGRAMA DE MENU ADMINISTRADOR
include "conexion.php";

session_start();
if ($_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
    exit;
} else {
    $desc_tipo_usuario = "apicultor";

    if ($_SESSION["tipo_usuario"] != $desc_tipo_usuario) {
        header('Location: index.php?mensaje=4');
        exit;
    }
}

// Datos de ejemplo
$tempe = json_encode([22, 24, 21], JSON_NUMERIC_CHECK);
$hume  = json_encode([60, 55, 70], JSON_NUMERIC_CHECK);

// Categorías de ejemplo
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
      }

      header {
        background: orange;
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      header h1 {
        margin: 0;
        font-size: 24px;
      }

      .user-info {
        text-align: right;
        font-size: 14px;
      }

      .user-info p {
        margin: 2px 0;
      }

      .user-info a.btn {
        display: inline-block;
        margin-top: 6px;
        background: #fff;
        color: #cc6600;
        font-weight: bold;
        padding: 5px 10px;
        text-decoration: none;
        border-radius: 4px;
      }

      .user-info a.btn:hover {
        background: #f2f2f2;
      }

      .menu-nav {
        display: flex;
        justify-content: center;
        background: orange;
        padding: 10px 0;
        border-top: 2px solid #e68a00;
      }

      .menu-nav a {
        color: white;
        text-decoration: none;
        margin: 0 20px;
        font-weight: bold;
        transition: color 0.3s;
      }

      .menu-nav a:hover {
        color: #333;
      }

      .content {
        padding: 20px;
        max-width: 1000px;
        margin: auto;
      }

      .content h2 {
        color: #cc6600;
        text-align: center;
        margin-top: 30px;
      }

      .chart-panel {
        background-color: #fff3e0;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

<?php include "cinta_apicultor.php"; ?>

<div class="content">
    <h2>Generar Informes</h2>
    <div class="chart-panel">
        <div id="container" style="height: 400px;"></div>
    </div>
</div>

<script>
$(function () { 
    Highcharts.chart('container', {
        chart: { type: 'line' },
        title: { text: 'Temperatura y Humedad (Ejemplo)' },
        xAxis: { categories: <?= json_encode($category) ?> },
        yAxis: { title: { text: 'Valor promedio' } },
        series: [
            { name: 'Temperatura', data: <?= $tempe ?>, color: '#ff6600' },
            { name: 'Humedad', data: <?= $hume ?>, color: '#3399ff' }
        ],
        credits: { enabled: false }
    });
});
</script>

</body>
</html>
