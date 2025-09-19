<?php
// PROGRAMA DE MENU CONSULTA
include "conexion.php";

session_start();
if ($_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
    exit;
} else {
    $mysqli = new mysqli($host, $user, $pw, $db);
    $sqlusu = "SELECT * FROM tipo_usuario WHERE id='2'"; // TIPO CONSULTA
    $resultusu = $mysqli->query($sqlusu);
    $rowusu = $resultusu->fetch_array(MYSQLI_NUM);
    $desc_tipo_usuario = $rowusu[1];
    if ($_SESSION["tipo_usuario"] != $desc_tipo_usuario) {
        header('Location: index.php?mensaje=4');
        exit;
    }
}

$id_usuario1 = $_SESSION["id_usuario"];
$sqlusu1 = "SELECT * FROM usuarios WHERE id='$id_usuario1'";
$resultusu1 = $mysqli->query($sqlusu1);
$rowusu1 = $resultusu1->fetch_array(MYSQLI_NUM);
$id_tarjeta= $rowusu1[8];

$sql1 = "SELECT * FROM datos_medidos WHERE ID_TARJ='$id_tarjeta' ORDER BY id DESC LIMIT 5";
$result1 = $mysqli->query($sql1);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Datos - Invernadero Automatizado</title>
    <meta http-equiv="refresh" content="15" />
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
            background-color: #ff9800;
            padding: 10px 20px;
            color: #fff;
        }
        header img {
            width: 150px;
            height: auto;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        .user-info {
            text-align: right;
            font-size: 14px;
        }
        .user-info a {
            display: inline-block;
            margin-top: 6px;
            background: #fff;
            color: #cc6600;
            font-weight: bold;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            background-color: #fff3e0;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #ff6600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #ff9933;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #fff3e0;
        }
        table tr:hover {
            background-color: #ffe0b2;
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

<?php include "menu_consul.php"; ?>

<div class="container">
    <h2>Últimos datos medidos del invernadero asignado</h2>

    <table>
        <tr>
            <th>#</th>
            <th>Id de la Tarjeta</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Temperatura (°C)</th>
            <th>Humedad (%)</th>
        </tr>
        <?php
        $contador = 0;
        while($row1 = $result1->fetch_array(MYSQLI_NUM)) {
            $contador++;
            $temp = $row1[2];
            $hum = $row1[3];
            $fecha = $row1[4];
            $hora = $row1[5];
            echo "<tr>
                    <td>$contador</td>
                    <td>$id_tarjeta</td>
                    <td>$fecha</td>
                    <td>$hora</td>
                    <td>$temp</td>
                    <td>$hum</td>
                  </tr>";
        }
        ?>
    </table>
</div>

</body>
</html>