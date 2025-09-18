<?php
include "conexion.php";

// Procesar formulario
if (isset($_POST["enviado"]) && $_POST["enviado"] == "S1") {
    $temp_min = $_POST["temp_min"];
    $temp_max = $_POST["temp_max"];
    $hum_min  = $_POST["hum_min"];
    $hum_max  = $_POST["hum_max"];
    $act_min  = $_POST["act_min"];
    $act_max  = $_POST["act_max"];

    $mysqli = new mysqli($host, $user, $pw, $db);
    if ($mysqli->connect_errno) {
        die("Error de conexión: " . $mysqli->connect_error);
    }

    $queries = [
        "UPDATE datos_maximos SET minimo='$temp_min', maximo='$temp_max' WHERE nombre_dato='temperatura'",
        "UPDATE datos_maximos SET minimo='$hum_min',  maximo='$hum_max'  WHERE nombre_dato='humedad'",
        "UPDATE datos_maximos SET minimo='$act_min',  maximo='$act_max'  WHERE nombre_dato='actividad'"
    ];

    $ok = true;
    foreach ($queries as $q) {
        if (!$mysqli->query($q)) $ok = false;
    }

    $mensaje = $ok ? "Datos actualizados correctamente" : "Error actualizando datos";
}

// Función para obtener valores actuales
function obtenerValores($mysqli, $nombre) {
    $sql = "SELECT minimo, maximo FROM datos_maximos WHERE nombre_dato='$nombre'";
    $res = $mysqli->query($sql);
    return $res->fetch_assoc();
}

$mysqli = new mysqli($host, $user, $pw, $db);

$temp = obtenerValores($mysqli, "temperatura");
$hum  = obtenerValores($mysqli, "humedad");
$act  = obtenerValores($mysqli, "actividad");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Umbrales - Invernadero Automatizado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff8f0;
            margin: 0;
            padding: 0;
            color: #333;
        }
        header {
            background-color: #ff9800;
            padding: 15px 20px;
            color: white;
            text-align: center;
        }
        header img {
            width: 200px;
            height: auto;
            display: block;
            margin: auto;
        }
        h1 {
            margin: 10px 0 0;
            color: #fff;
        }
        .container {
            max-width: 700px;
            margin: 30px auto;
            background-color: #fff3e0;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .mensaje {
            background-color: #ffe0b2;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            color: #ff9800;
        }
        form table {
            width: 100%;
            border-collapse: collapse;
        }
        form td {
            padding: 10px;
        }
        form td.label {
            text-align: right;
            font-weight: bold;
            color: #ff6600;
            width: 50%;
        }
        form td.input {
            text-align: left;
        }
        input[type="number"] {
            width: 80px;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: #ff6600;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        input[type="submit"]:hover {
            background-color: #ff9800;
        }
    </style>
</head>
<body>

<header>
    <img src="img/panal.jpg" alt="Logo Panal">
    <h1>Consulta y Modifica Umbrales</h1>
</header>
<?php include "menu_admin.php"; ?>
<div class="container">
    <?php if (isset($mensaje)) { ?>
        <div class="mensaje"><?php echo $mensaje; ?></div>
    <?php } ?>

    <form method="POST" action="edicion_umbrales.php">
        <table>
            <tr>
                <td class="label">Temperatura (mín - máx):</td>
                <td class="input">
                    <input type="number" name="temp_min" value="<?php echo $temp['minimo']; ?>" required>
                    <input type="number" name="temp_max" value="<?php echo $temp['maximo']; ?>" required>
                </td>
            </tr>
            <tr>
                <td class="label">Humedad (mín - máx):</td>
                <td class="input">
                    <input type="number" name="hum_min" value="<?php echo $hum['minimo']; ?>" required>
                    <input type="number" name="hum_max" value="<?php echo $hum['maximo']; ?>" required>
                </td>
            </tr>
            <tr>
                <td class="label">Actividad (mín - máx):</td>
                <td class="input">
                    <input type="number" name="act_min" value="<?php echo $act['minimo']; ?>" required>
                    <input type="number" name="act_max" value="<?php echo $act['maximo']; ?>" required>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center;">
                    <input type="hidden" name="enviado" value="S1">
                    <input type="submit" value="Actualizar">
                </td>
            </tr>
        </table>
    </form>
</div>

</body>
</html>
