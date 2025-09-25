<?php
include "conexion.php";

session_start();
$desc_tipo_usuario = "Apicultor";
if ($_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
    exit;
} else {
    if ($_SESSION["tipo_usuario"] != "apicultor")
        header('Location: index.php?mensaje=4');
}

$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Error en la conexión a MySQL: " . $mysqli->connect_error);
}

$id_usuario1 = $_SESSION["id_usuario"];
$id_colmena = isset($_GET['id_colmena']) ? intval($_GET['id_colmena']) : 0;

if ($id_colmena > 0) {
    $titulo = "Datos de la colmena seleccionada";
    $sql1 = "SELECT d.id, d.fecha, d.hora,
                    d.temperatura, d.humedad, 
                    d.actividad_entrante, d.actividad_saliente,
                    c.nombre AS colmena_nombre
             FROM datos_medidos d
             INNER JOIN colmenas c ON d.colmena_id = c.id
             WHERE c.id = '$id_colmena'
             ORDER BY d.fecha DESC, d.hora DESC
             LIMIT 50";
    $result1 = $mysqli->query($sql1);
} else {
    $titulo = "Selecciona una colmena para consultar";
    $result1 = null;
}

$sql_colmenas = "SELECT id, nombre FROM colmenas WHERE apicultor_id = '$id_usuario1'";
$res_colmenas = $mysqli->query($sql_colmenas);
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
        width: 95%;
        margin: 20px auto;
      }

      h2 {
        margin-left: 80px;
        color: #cc6600;
      }

      form {
        margin-left: 80px;
        margin-bottom: 20px;
      }

      select, input[type="submit"] {
        padding: 8px 12px;
        border: 2px solid #ff9800;
        border-radius: 6px;
        font-size: 14px;
      }

      input[type="submit"] {
        background: #ff9800;
        color: white;
        font-weight: bold;
        cursor: pointer;
        margin-left: 8px;
        transition: background 0.3s;
      }

      input[type="submit"]:hover {
        background: #e65100;
      }

      table {
        border-collapse: collapse;
        width: 90%;
        margin: 0 auto 30px;
        background: #ffffff;
        box-shadow: 0 0 5px rgba(0,0,0,0.1);
      }

      th {
        background-color: #ffcc66;
        color: #663300;
        padding: 10px;
      }

      td {
        padding: 8px;
        text-align: center;
        border-bottom: 1px solid #ddd;
      }

      tr:nth-child(even) {
        background-color: #f9f9f9;
      }

      tr:hover {
        background-color: #ffe0b3;
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
        <h2><?php echo $titulo; ?></h2>

        <form method="GET" action="consulta_por_colmena.php">
            <label for="id_colmena"><b>Seleccionar colmena:</b></label>
            <select name="id_colmena" id="id_colmena" required>
                <option value="">-- Selecciona --</option>
                <?php while($col = $res_colmenas->fetch_assoc()) { ?>
                    <option value="<?php echo $col['id']; ?>" 
                        <?php if ($id_colmena == $col['id']) echo 'selected'; ?>>
                        <?php echo $col['nombre']; ?>
                    </option>
                <?php } ?>
            </select>
            <input type="submit" value="Consultar">
        </form>

        <?php if ($result1 && $result1->num_rows > 0) { ?>
        <table>
            <tr>
                <th>#</th>
                <th>Colmena</th>
                <th>Temperatura (°C)</th>
                <th>Humedad (%)</th>
                <th>Actividad Entrante</th>
                <th>Actividad Saliente</th>
                <th>Fecha</th>
                <th>Hora</th>
            </tr>
            <?php
            $contador = 0;
            while($row1 = $result1->fetch_assoc()) {
                $contador++;
                echo "<tr>
                        <td>$contador</td>
                        <td>".$row1['colmena_nombre']."</td>
                        <td>".$row1['temperatura']."</td>
                        <td>".$row1['humedad']."</td>
                        <td>".$row1['actividad_entrante']."</td>
                        <td>".$row1['actividad_saliente']."</td>
                        <td>".$row1['fecha']."</td>
                        <td>".$row1['hora']."</td>
                    </tr>";
            }
            ?>
        </table>
        <?php } elseif ($id_colmena > 0) { ?>
            <p style="text-align:center; color:red;">No hay datos registrados para esta colmena.</p>
        <?php } ?>
    </div>
</body>
</html>
