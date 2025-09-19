<?php
// PROGRAMA DE MENU ADMINISTRADOR
include "conexion.php";
session_start();
if ($_SESSION["autenticado"] != "SIx3") {
  header('Location: index.php?mensaje=3');
} else {
  $mysqli = new mysqli($host, $user, $pw, $db);
  $sqlusu = "SELECT * from tipo_usuario where id='1'";
  $resultusu = $mysqli->query($sqlusu);
  $rowusu = $resultusu->fetch_array(MYSQLI_NUM);
  $desc_tipo_usuario = $rowusu[1];
  if ($_SESSION["tipo_usuario"] != $desc_tipo_usuario)
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
        background-color: #ff9900;
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      header h1 {
        margin: 0;
        font-size: 22px;
      }

      .user-info {
        text-align: right;
        font-size: 14px;
        color: #fff;
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

      h2 {
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
      .form-busqueda {
        width: 90%;
        margin: 20px auto;
        padding: 10px;
        background: #fff3e0;
        border: 1px solid #ffcc99;
        border-radius: 6px;
      }
      .form-busqueda input, .form-busqueda select {
        padding: 5px;
        margin: 5px;
      }
      .btn {
        background: #ff9900;
        color: white;
        border: none;
        padding: 6px 12px;
        cursor: pointer;
        border-radius: 4px;
      }
      .btn:hover {
        background: #cc6600;
      }
      .mensaje {
        margin: 10px auto;
        padding: 10px;
        width: 60%;
        border-radius: 4px;
        font-weight: bold;
      }
      .ok {
        background: #ddffdd;
        color: #006600;
      }
      .error {
        background: #ffdddd;
        color: #990000;
      }
    </style>
  </head>

  <body>
    <header>
      <div>
        <h1>Sistema de Monitoreo de Colmenas</h1>
      </div>
      <div class="user-info">
        <p><strong>Usuario:</strong> <?php echo $_SESSION["nombre_usuario"]; ?></p>
        <p><strong>Tipo:</strong> <?php echo $desc_tipo_usuario; ?></p>
        <a href="cerrar_sesion.php" class="btn">Cerrar Sesión</a>
      </div>
    </header>

    <?php
      include "menu_admin.php";
    ?>

    <section class="form-busqueda">
      <form action="gestion_usuarios.php" method="POST">
        Consultar por Identificación: 
        <input type="number" name="id_con" value="">
        Consultar por Nombre: 
        <input type="text" name="nombre_con" value="">
        Estado Usuario:
        <select name="estado">
          <option value=2>Todos</option>
          <option value=1>Activos</option>
          <option value=0>Inactivos</option>
        </select>
        <input type="submit" class="btn" name="Consultar" value="Consultar">
        <input type="hidden" value="1" name="enviado">
      </form>
      <p><a href="gestion_usuarios_add.php"><b>➕ Agregar Nuevo Usuario</b></a></p>
    </section>

    <?php
    if (isset($_GET["mensaje"]) && $_GET["mensaje"] != "") {
      $mensaje = $_GET["mensaje"];
      $clase = "error"; $texto = "";
      if ($mensaje == 1){ $clase="ok"; $texto="Usuario actualizado correctamente."; }
      if ($mensaje == 2){ $texto="Usuario no fue actualizado correctamente."; }
      if ($mensaje == 3){ $clase="ok"; $texto="Usuario creado correctamente."; }
      if ($mensaje == 4){ $texto="Usuario no fue creado. Se presentó un inconveniente."; }
      if ($mensaje == 5){ $texto="Usuario no fue creado. Ya existe usuario con la misma cédula."; }
      echo "<div class='mensaje $clase'>$texto</div>";
    }
    ?>

    <table>
      <tr>
        <th>Nombre Usuario</th>
        <th>Número Id</th>
        <th>Dirección</th>
        <th>Usuario</th>
        <th>Tipo Usuario</th>
        <th>Id Tarjeta</th>
        <th>Activo (S/N)</th>
        <th>Modificar</th>
      </tr>
      <?php
      $mysqli = new mysqli($host, $user, $pw, $db);
      if ((isset($_POST["enviado"]))) {
        $id_con = $_POST["id_con"];
        $nombre_con = $_POST["nombre_con"];
        $estado = $_POST["estado"];
        $sql1 = "SELECT * from usuarios order by nombre_completo";
        if (($id_con == "") and ($nombre_con == "")) {
          if ($estado != "2")
            $sql1 = "SELECT * from usuarios where activo='$estado' order by nombre_completo";
        }
        if (($id_con != "") and ($nombre_con == "")) {
          if ($estado == "2")
            $sql1 = "SELECT * from usuarios where identificacion='$id_con'";
          else
            $sql1 = "SELECT * from usuarios where identificacion='$id_con' and activo='$estado'";
        }
        if (($id_con == "") and ($nombre_con != "")) {
          if ($estado == "2")
            $sql1 = "SELECT * from usuarios where nombre_completo LIKE '%$nombre_con%' order by nombre_completo";
          else
            $sql1 = "SELECT * from usuarios where nombre_completo LIKE '%$nombre_con%' and activo='$estado' order by nombre_completo";
        }
        if (($id_con != "") and ($nombre_con != "")) {
          if ($estado == "2")
            $sql1 = "SELECT * from usuarios where nombre_completo LIKE '%$nombre_con%' and identificacion='$id_con'";
          else
            $sql1 = "SELECT * from usuarios where nombre_completo LIKE '%$nombre_con%' and identificacion='$id_con' and activo='$estado'";
        }
      } else {
        $sql1 = "SELECT * from usuarios order by nombre_completo";
      }
      $result1 = $mysqli->query($sql1);
      while($row1 = $result1->fetch_array(MYSQLI_NUM)) {
        $id_usu  = $row1[0];
        $id_usu_enc = md5($id_usu);
        $nombre_usuario  = $row1[1];
        $num_id = $row1[2];
        $direccion = $row1[3];
        $usuario= $row1[5];
        $tipo_usuario  = $row1[7];
        $id_tarjeta = $row1[8];
        $activo= $row1[9];
        $desc_activo = ($activo == 1) ? "S" : "N";
        $sql3 = "SELECT * from tipo_usuario where id='$tipo_usuario'";
        $result3 = $mysqli->query($sql3);
        $row3 = $result3->fetch_array(MYSQLI_NUM);
        $desc_tipo_usuario = $row3[1];
      ?>
      <tr>
        <td><?php echo $nombre_usuario; ?></td>
        <td><?php echo $num_id; ?></td>
        <td><?php echo $direccion; ?></td>
        <td><?php echo $usuario; ?></td>
        <td><?php echo $desc_tipo_usuario; ?></td>
        <td><?php echo $id_tarjeta; ?></td>
        <td><?php echo $desc_activo; ?></td>
        <td><a href="gestion_usuarios_mod.php?id_usu=<?php echo $id_usu_enc; ?>"><img src="img/icono_editar.jpg" width=30></a></td>
      </tr>
      <?php } ?>
    </table>

    <h2>Mis Colmenas</h2>
    <table>
      <tr>
        <th>ID Colmena</th>
        <th>Nombre</th>
        <th>Latitud</th>
        <th>Longitud</th>
        <th>Fecha Registro</th>
        <th>Modificar</th>
      </tr>
      <?php
        $id_usuario_logueado = $_SESSION["id_usuario"]; // ID del usuario logueado
        $sql_col = "SELECT * FROM colmenas WHERE id_usuario='$id_usuario_logueado' ORDER BY nombre";
        $result_col = $mysqli->query($sql_col);

        if (!$result_col) {
            echo "<tr><td colspan='6' style='color:red;'>Error al cargar colmenas: " . $mysqli->error . "</td></tr>";
        } else {
            while($row_col = $result_col->fetch_array(MYSQLI_NUM)) {
                $id_colmena     = $row_col[0];
                $nombre         = $row_col[1];
                $latitud        = $row_col[2];
                $longitud       = $row_col[3];
                //$id_usuario_col = $row_col[4]; // ya filtrado, no es necesario mostrar
                $fecha_registro = $row_col[5];
      ?>
      <tr>
        <td><?php echo $id_colmena; ?></td>
        <td><?php echo $nombre; ?></td>
        <td><?php echo $latitud; ?></td>
        <td><?php echo $longitud; ?></td>
        <td><?php echo $fecha_registro; ?></td>
        <td><a href="gestion_colmenas_mod.php?id_col=<?php echo md5($id_colmena); ?>"><img src="img/icono_editar.jpg" width="30"></a></td>
      </tr>
      <?php
            }
        }
      ?>
    </table>



  </body>
</html>
