<?php
include "config/conexion_bd.php"; //OK

session_start();
$desc_tipo_usuario = "Apicultor";
if ($_SESSION["autenticado"] != "SIx3") {
  header('Location: ../inicio_sesion/index.php?mensaje=3');
} else {
  if ($_SESSION["tipo_usuario"] != "apicultor")
    header('Location: ../inicio_sesion/index.php?mensaje=4');
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

// Selección de colmena
$colmena_id = $_POST["colmena_select"] ?? ($_POST["colmena_id"] ?? null);
if (!$colmena_id && count($colmenas) > 0) {
    $colmena_id = $colmenas[0]["id"];
}

// Actualizar modo y actuadores
if (isset($_POST["enviado"]) && $_POST["enviado"] == "S1" && $colmena_id) {
    $modo = $_POST["modo"] ?? "automatico";

    // Insertar o actualizar modo en control_colmena
    $check = $mysqli->query("SELECT id FROM control_colmena WHERE colmena_id = $colmena_id");
    if ($check->num_rows > 0) {
        $q1 = "UPDATE control_colmena SET modo='$modo' WHERE colmena_id=$colmena_id";
    } else {
        $q1 = "INSERT INTO control_colmena (modo, colmena_id) VALUES ('$modo', $colmena_id)";
    }
    $mysqli->query($q1);

    // Si es modo manual, actualizar valores de actuadores
    if ($modo == "manual") {
        $entrada = floatval($_POST["entrada"]);
        // El calefactor ahora se guarda como 1 o 0
        $calefactor = isset($_POST["calefactor"]) ? 1 : 0;

        // Actualizar o insertar actuadores
        $mysqli->query("INSERT INTO actuadores (nombre, estado, tipo_estado, colmena_id)
                        VALUES ('compuertas', $entrada, 'angulo', $colmena_id)
                        ON DUPLICATE KEY UPDATE estado = $entrada, tipo_estado='angulo'");

        $mysqli->query("INSERT INTO actuadores (nombre, estado, tipo_estado, colmena_id)
                        VALUES ('calefactor', $calefactor, 'temperatura', $colmena_id)
                        ON DUPLICATE KEY UPDATE estado = $calefactor, tipo_estado='temperatura'");
    }

    $mensaje = "Configuración actualizada correctamente.";
}

// Obtener modo actual
$modo_actual = "automatico";
if ($colmena_id) {
    $res_modo = $mysqli->query("SELECT modo FROM control_colmena WHERE colmena_id = $colmena_id LIMIT 1");
    if ($res_modo && $res_modo->num_rows > 0) {
        $modo_actual = $res_modo->fetch_assoc()["modo"];
    }
}

// Obtener valores actuales de actuadores
$entrada_actual = 0;
$calefactor_actual = 0;
if ($colmena_id) {
    $res_act = $mysqli->query("SELECT nombre, estado FROM actuadores WHERE colmena_id = $colmena_id");
    while ($row = $res_act->fetch_assoc()) {
        if ($row["nombre"] == "compuertas") $entrada_actual = $row["estado"];
        if ($row["nombre"] == "calefactor") $calefactor_actual = $row["estado"];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Control de Colmena</title>

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
    max-width: 600px;
    margin: 30px auto;
    background: #fff;
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

  .switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
  }

  .switch input { opacity: 0; width: 0; height: 0; }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
  }

  input:checked + .slider { background-color: #ff6600; }
  input:checked + .slider:before { transform: translateX(26px); }
</style>
</head>

<body>
  <header>
    <h1>Sistema de Monitoreo de Colmenas</h1>
    <div class="user-info">
      <p><strong>Usuario:</strong> <?php echo $_SESSION["nombre_usuario"]; ?></p>
      <p><strong>Tipo:</strong> <?php echo $desc_tipo_usuario; ?></p>
      <a href="../inicio_sesion/includes/cerrar_sesion.php" class="btn">Cerrar Sesión</a>
    </div>
  </header>

  <?php include "includes/cinta_apicultor.php"; ?> <!-- OK -->

  <div class="container">
    <h2>Control Automático / Manual</h2>

    <?php if (isset($mensaje)) { ?>
        <div class="mensaje"><?php echo $mensaje; ?></div>
    <?php } ?>

    <!-- Selección de colmena -->
    <form method="POST" action="control_automatico.php">
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

    <?php if ($colmena_id) { ?>
    <form method="POST" action="control_automatico.php">
      <input type="hidden" name="colmena_id" value="<?php echo $colmena_id; ?>">
      <table>
        <tr>
          <td class="label">Modo de control:</td>
          <td>
            <label class="switch">
              <input type="checkbox" id="modoSwitch" name="modo" value="manual" <?php if ($modo_actual=="manual") echo "checked"; ?>>
              <span class="slider"></span>
            </label>
            <span id="modoLabel"><?php echo ucfirst($modo_actual); ?></span>
          </td>
        </tr>
      </table>

      <div id="manualControls" style="display:<?php echo ($modo_actual=='manual')?'block':'none'; ?>">
        <table>
          <tr>
            <td class="label">Entrada (0°–180°):</td>
            <td><input type="number" name="entrada" min="0" max="180" step="1" value="<?php echo $entrada_actual; ?>"></td>
          </tr>
          <tr>
            <td class="label">Calefactor:</td>
            <td>
              <label class="switch">
                <input type="checkbox" name="calefactor" value="1" <?php if ($calefactor_actual == 1) echo "checked"; ?>>
                <span class="slider"></span>
              </label>
              <span><?php echo ($calefactor_actual == 1) ? "Encendido" : "Apagado"; ?></span>
            </td>
          </tr>
        </table>
      </div>

      <div style="text-align:center; margin-top:20px;">
        <input type="hidden" name="enviado" value="S1">
        <input type="submit" value="Guardar cambios">
      </div>
    </form>
    <?php } ?>
  </div>

  <script>
    const switchInput = document.getElementById("modoSwitch");
    const label = document.getElementById("modoLabel");
    const controls = document.getElementById("manualControls");
    switchInput.addEventListener("change", () => {
      if (switchInput.checked) {
        label.textContent = "Manual";
        controls.style.display = "block";
      } else {
        label.textContent = "Automático";
        switchInput.value = "automatico";
        controls.style.display = "none";
      }
    });
  </script>
</body>
</html>
