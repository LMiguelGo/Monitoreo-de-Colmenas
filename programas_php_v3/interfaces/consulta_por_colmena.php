<?php
include "conexion.php";

session_start();
if ($_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
    exit;
} else {
    $mysqli = new mysqli($host, $user, $pw, $db);
    if ($mysqli->connect_errno) {
        die("Error en la conexión a MySQL: " . $mysqli->connect_error);
    }
}

// Obtener el id_usuario y su tarjeta asociada
$id_usuario1 = $_SESSION["id_usuario"];
$sqlusu1 = "SELECT * FROM usuarios WHERE id='$id_usuario1'";
$resultusu1 = $mysqli->query($sqlusu1);
$rowusu1 = $resultusu1->fetch_array(MYSQLI_NUM);
$id_tarjeta = $rowusu1[8]; // campo de la tarjeta asignada

// Verificar si se envió un id_colmena por GET
$id_colmena = isset($_GET['id_colmena']) ? intval($_GET['id_colmena']) : 0;

if ($id_colmena > 0) {
    $sql1 = "SELECT * FROM datos_medidos WHERE ID_TARJ='$id_colmena' ORDER BY id DESC LIMIT 20";
    $titulo = "Datos de la colmena con ID $id_colmena";
} else {
    $sql1 = "SELECT * FROM datos_medidos WHERE ID_TARJ='$id_tarjeta' ORDER BY id DESC LIMIT 20";
    $titulo = "Datos de tu colmena asignada (ID $id_tarjeta)";
}
$result1 = $mysqli->query($sql1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta por Colmena</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff8f0;
            margin: 0;
            padding: 0;
        }
        header {
            background: linear-gradient(90deg, #ff9800, #f57c00);
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 26px;
            font-weight: bold;
            box-shadow: 0px 3px 6px rgba(0,0,0,0.2);
        }
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        h1 {
            color: #e65100;
            text-align: center;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="number"] {
            padding: 10px;
            border: 2px solid #ff9800;
            border-radius: 6px;
            outline: none;
            font-size: 14px;
        }
        input[type="number"]:focus {
            border-color: #e65100;
        }
        input[type="submit"] {
            background: #ff9800;
            color: white;
            border: none;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 8px;
            transition: background 0.3s;
        }
        input[type="submit"]:hover {
            background: #e65100;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
        }
        th {
            background-color: #ff9800;
            color: white;
            padding: 12px;
        }
        td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background-color: #fff3e0;
        }
    </style>
</head>
<body>
    <header>🐝 Consulta de Datos por Colmena</header>
    <?php include "menu_admin.php"; ?>
    <div class="container">
        <h1><?php echo $titulo; ?></h1>

        <form method="GET" action="consulta_por_colmena.php">
            <label for="id_colmena"><b>Consultar otra colmena (ID):</b></label>
            <input type="number" name="id_colmena" id="id_colmena" required>
            <input type="submit" value="Consultar">
        </form>

        <table>
            <tr>
                <th>#</th>
                <th>ID Colmena</th>
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
                        <td>".$row1[1]."</td>
                        <td>".$row1[4]."</td>
                        <td>".$row1[5]."</td>
                        <td>".$row1[2]."</td>
                        <td>".$row1[3]."</td>
                      </tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
