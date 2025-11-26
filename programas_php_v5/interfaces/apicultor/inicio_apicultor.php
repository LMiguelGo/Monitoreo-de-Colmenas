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
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Gestión de Colmenas</title>
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
        justify-content: space-between; /* título a la izquierda, usuario a la derecha */
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
      
      .add-colmena {
          font-size: 14px;
          text-decoration: none;
          color: white;
          background-color: #ffa52fff; /* verde acción */
          padding: 5px 10px;
          border-radius: 4px;
          transition: background 0.3s;
      }

      .add-colmena:hover {
          background-color: #fec46eff;
      }
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

    <h2 style="display: flex; align-items: center; gap: 15px; margin-left: 80px; color: #cc6600;">
      Mis Colmenas
      <a href="agregar_colmena.php" class="add-colmena">+ Añadir Colmena</a>
    </h2>

    <table>
      <tr>
        <th>Nombre</th>
        <th>Fecha Registro</th>
        <th>Latitud</th>
        <th>Longitud</th>
        <th>Ubicación</th>
        <th>Dimensiones</th>
        <th>Población Abejas</th>
        <th>Modificar</th>
      </tr>
      <?php
        $mysqli = new mysqli($host, $user, $pw, $db);

        $id_usuario_logueado = $_SESSION["id_usuario"];
        $sql_col = "SELECT * FROM colmenas WHERE apicultor_id='$id_usuario_logueado' ORDER BY nombre";
        $result_col = $mysqli->query($sql_col);

        if (!$result_col) {
            echo "<tr><td colspan='8' style='color:red;'>Error al cargar colmenas: " . $mysqli->error . "</td></tr>";
        } else {
            while($row_col = $result_col->fetch_array(MYSQLI_ASSOC)) {
                $id_colmena     = $row_col["id"];
                $nombre         = $row_col["nombre"];
                $fecha_registro = $row_col["fecha_registro"];
                $latitud        = $row_col["latitud"];
                $longitud       = $row_col["longitud"];
                $ubicacion      = $row_col["ubicacion"];
                $dimensiones    = $row_col["dimensiones"];
                $poblacion      = $row_col["poblacion_abejas"];
      ?>
      <tr>
        <td><?php echo $nombre; ?></td>
        <td><?php echo $fecha_registro; ?></td>
        <td><?php echo $latitud; ?></td>
        <td><?php echo $longitud; ?></td>
        <td><?php echo $ubicacion; ?></td>
        <td><?php echo $dimensiones; ?></td>
        <td><?php echo $poblacion; ?></td>
        <td><a href="modificar_colmena.php?id_col=<?php echo md5($id_colmena); ?>"><img src="img/icono_editar.jpg" width="30"></a></td>
      </tr>
      <?php
            }
        }
      ?>
    </table>
  </body>
</html>
