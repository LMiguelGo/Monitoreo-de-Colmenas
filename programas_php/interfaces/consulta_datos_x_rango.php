<?php
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta por Rango de Fechas - Invernadero</title>
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
        form {
            margin: 20px 0;
            text-align: center;
        }
        form input[type="date"] {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin: 0 10px;
        }
        form input[type="submit"] {
            background-color: #ff6600;
            color: white;
            border: none;
            padding: 8px 20px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        form input[type="submit"]:hover {
            background-color: #ff9933;
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
        .range-info {
            text-align: center;
            font-weight: bold;
            color: #ff6600;
            margin-top: 15px;
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
    <h2>Consulta de Datos Medidos por Rango de Fechas</h2>
    
    <?php if (!isset($_POST["enviado"])) { ?>
        <form method="POST" action="consulta_datos_x_rango.php">
            <label><b>Fecha Inicial:</b></label>
            <input type="date" name="fecha_ini" required>
            <label><b>Fecha Final:</b></label>
            <input type="date" name="fecha_fin" required>
            <input type="hidden" name="enviado" value="S1">
            <input type="submit" value="Consultar">
        </form>
    <?php } else {
        $fecha_ini = $_POST["fecha_ini"];
        $fecha_fin = $_POST["fecha_fin"];

        $id_usuario1 = $_SESSION["id_usuario"];
        $sqlusu1 = "SELECT * FROM usuarios WHERE id='$id_usuario1'";
        $resultusu1 = $mysqli->query($sqlusu1);
        $rowusu1 = $resultusu1->fetch_array(MYSQLI_NUM);
        $id_tarjeta = $rowusu1[8];

        $sql1 = "SELECT * FROM datos_medidos 
                 WHERE ID_TARJ='$id_tarjeta' 
                 AND fecha >= '$fecha_ini' 
                 AND fecha <= '$fecha_fin' 
                 ORDER BY fecha";
        $result1 = $mysqli->query($sql1);
    ?>

    <div class="range-info">Rango consultado: desde <?= $fecha_ini ?> hasta <?= $fecha_fin ?></div>

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
            echo "<tr>
                    <td>$contador</td>
                    <td>$id_tarjeta</td>
                    <td>$row1[4]</td>
                    <td>$row1[5]</td>
                    <td>$row1[2]</td>
                    <td>$row1[3]</td>
                  </tr>";
        }
        ?>
    </table>

    <form method="POST" action="consulta_datos_x_rango.php" style="text-align:center; margin-top:15px;">
        <input type="submit" value="Volver">
    </form>

    <?php } ?>

</div>

</body>
</html>
