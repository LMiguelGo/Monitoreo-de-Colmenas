<?php
include "conexion.php";

session_start();
$desc_tipo_usuario = "Apicultor";
if ($_SESSION["autenticado"] != "SIx3") {
  header('Location: index.php?mensaje=3');
} else {
  if ($_SESSION["tipo_usuario"] != "apicultor")
    header('Location: index.php?mensaje=4');
}
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Gestión de Usuarios</title>
    <meta charset="utf-8">
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

      h2 {
        margin-left: 80px;
        margin-top: 30px;
        color: #cc6600;
      }

      table {
        border-collapse: collapse;
        width: 90%;
        margin: 20px auto;
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

    <!-- Tabla de Apicultores -->
    <h2>Apicultores</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>Nombre Completo</th>
        <th>Número ID</th>
        <th>Fecha Nacimiento</th>
        <th>Fecha Registro</th>
        <th>Dirección</th>
        <th>Usuario</th>
      </tr>
      <?php
        $mysqli = new mysqli($host, $user, $pw, $db);

        $sql_ap = "SELECT * FROM apicultores ORDER BY nombre_completo";
        $result_ap = $mysqli->query($sql_ap);

        if (!$result_ap) {
            echo "<tr><td colspan='9' style='color:red;'>Error al cargar apicultores: " . $mysqli->error . "</td></tr>";
        } else {
            while($row_ap = $result_ap->fetch_array(MYSQLI_ASSOC)) {
                echo "<tr>
                        <td>{$row_ap['id']}</td>
                        <td>{$row_ap['nombre_completo']}</td>
                        <td>{$row_ap['numero_id']}</td>
                        <td>{$row_ap['fecha_nacimiento']}</td>
                        <td>{$row_ap['fecha_registro']}</td>
                        <td>{$row_ap['direccion']}</td>
                        <td>{$row_ap['user']}</td>
                      </tr>";
            }
        }
      ?>
    </table>

    <!-- Tabla de Investigadores -->
    <h2>Investigadores</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>Nombre Completo</th>
        <th>Número ID</th>
        <th>Fecha Nacimiento</th>
        <th>Fecha Registro</th>
        <th>Dirección</th>
        <th>Usuario</th>
      </tr>
      <?php
        $sql_inv = "SELECT * FROM investigadores ORDER BY nombre_completo";
        $result_inv = $mysqli->query($sql_inv);

        if (!$result_inv) {
            echo "<tr><td colspan='7' style='color:red;'>Error al cargar investigadores: " . $mysqli->error . "</td></tr>";
        } else {
            while($row_inv = $result_inv->fetch_array(MYSQLI_ASSOC)) {
                echo "<tr>
                        <td>{$row_inv['id']}</td>
                        <td>{$row_inv['nombre_completo']}</td>
                        <td>{$row_inv['numero_id']}</td>
                        <td>{$row_inv['fecha_nacimiento']}</td>
                        <td>{$row_inv['fecha_registro']}</td>
                        <td>{$row_inv['direccion']}</td>
                        <td>{$row_inv['user']}</td>
                      </tr>";
            }
        }
      ?>
    </table>
  </body>
</html>
