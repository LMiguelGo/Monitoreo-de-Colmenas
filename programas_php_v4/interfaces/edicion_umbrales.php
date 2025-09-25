<?php
include "conexion.php";

session_start();
$desc_tipo_usuario = "Apicultor";
if ($_SESSION["autenticado"] != "SIx3") {
  header('Location: index.php?mensaje=3');
  exit;
}

$usuario_id = $_SESSION["id_usuario"];

$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Obtener colmenas del usuario
$sql_colmenas = "SELECT id, nombre FROM colmenas WHERE apicultor_id = $usuario_id";
$res_colmenas = $mysqli->query($sql_colmenas);
$colmenas = $res_colmenas->fetch_all(MYSQLI_ASSOC);

// Colmena seleccionada
$colmena_id = null;
if (isset($_POST["colmena_select"])) {
    $colmena_id = intval($_POST["colmena_select"]);
} elseif (isset($_POST["enviado"]) && $_POST["enviado"] == "S1") {
    $colmena_id = intval($_POST["colmena_id"]);

    $temp_min = $_POST["temp_min"];
    $temp_max = $_POST["temp_max"];
    $hum_min  = $_POST["hum_min"];
    $hum_max  = $_POST["hum_max"];
    $act_min  = $_POST["act_min"];
    $act_max  = $_POST["act_max"];

    $check = $mysqli->query("SELECT id FROM umbrales WHERE colmena_id = $colmena_id");
    if ($check->num_rows > 0) {
        $q = "UPDATE umbrales 
              SET temp_min='$temp_min', temp_max='$temp_max',
                  hum_min='$hum_min', hum_max='$hum_max',
                  activ_min='$act_min', activ_max='$act_max'
              WHERE colmena_id='$colmena_id'";
    } else {
        $q = "INSERT INTO umbrales (temp_min, temp_max, hum_min, hum_max, activ_min, activ_max, colmena_id)
              VALUES ('$temp_min','$temp_max','$hum_min','$hum_max','$act_min','$act_max','$colmena_id')";
    }

    if ($mysqli->query($q)) {
        $mensaje = "Umbrales actualizados correctamente";
    } else {
        $mensaje = "Error actualizando umbrales: " . $mysqli->error;
    }
}

if (!$colmena_id && count($colmenas) > 0) {
    $colmena_id = $colmenas[0]["id"];
}

// Obtener umbrales actuales
$umbrales = [
    "temp_min" => "", "temp_max" => "",
    "hum_min" => "", "hum_max" => "",
    "activ_min" => "", "activ_max" => ""
];
if ($colmena_id) {
    $sql = "SELECT * FROM umbrales WHERE colmena_id = $colmena_id LIMIT 1";
    $res = $mysqli->query($sql);
    if ($res && $res->num_rows > 0) {
        $umbrales = $res->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Umbrales</title>
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

      .container {
        max-width: 700px;
        margin: 30px auto;
        background: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 5px rgba(0,0,0,0.1);
      }

      h2 {
        text-align: center;
        color: #cc6600;
        margin-bottom: 20px;
      }

      .mensaje {
        background:#ffe0b2;
        padding:10px;
        border-radius:5px;
        text-align:center;
        font-weight:bold;
        color:#ff9800;
        margin-bottom:15px;
      }

      table {
        width:100%;
        border-collapse: collapse;
      }

      td {
        padding:10px;
      }

      td.label {
        text-align:right;
        font-weight:bold;
        color:#cc6600;
        width:50%;
      }

      input[type="number"], select {
        width:120px;
        padding:5px;
        border:1px solid #ccc;
        border-radius:5px;
      }

      input[type="submit"] {
        background:#ff6600;
        color:#fff;
        padding:10px 20px;
        border:none;
        border-radius:5px;
        cursor:pointer;
        font-weight:bold;
      }

      input[type="submit"]:hover {
        background:#ff9800;
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

  <div class="container">
    <h2>Consulta y Modifica Umbrales</h2>

    <?php if (isset($mensaje)) { ?>
        <div class="mensaje"><?php echo $mensaje; ?></div>
    <?php } ?>

    <!-- Selección de colmena -->
    <form method="POST" action="edicion_umbrales.php">
        <table>
            <tr>
                <td class="label">Seleccionar Colmena:</td>
                <td>
                    <select name="colmena_select" onchange="this.form.submit()">
                        <?php foreach ($colmenas as $c) { ?>
                            <option value="<?php echo $c['id']; ?>" <?php if ($c['id']==$colmena_id) echo "selected"; ?>>
                                <?php echo htmlspecialchars($c['nombre']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
        </table>
    </form>

    <!-- Edición de umbrales -->
    <?php if ($colmena_id) { ?>
    <form method="POST" action="edicion_umbrales.php">
        <input type="hidden" name="colmena_id" value="<?php echo $colmena_id; ?>">
        <table>
            <tr>
                <td class="label">Temperatura (mín - máx):</td>
                <td>
                    <input type="number" step="0.1" name="temp_min" value="<?php echo $umbrales['temp_min']; ?>" required>
                    <input type="number" step="0.1" name="temp_max" value="<?php echo $umbrales['temp_max']; ?>" required>
                </td>
            </tr>
            <tr>
                <td class="label">Humedad (mín - máx):</td>
                <td>
                    <input type="number" step="0.1" name="hum_min" value="<?php echo $umbrales['hum_min']; ?>" required>
                    <input type="number" step="0.1" name="hum_max" value="<?php echo $umbrales['hum_max']; ?>" required>
                </td>
            </tr>
            <tr>
                <td class="label">Actividad (mín - máx):</td>
                <td>
                    <input type="number" step="0.1" name="act_min" value="<?php echo $umbrales['activ_min']; ?>" required>
                    <input type="number" step="0.1" name="act_max" value="<?php echo $umbrales['activ_max']; ?>" required>
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
    <?php } ?>
  </div>
</body>
</html>
