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
  <meta charset="utf-8">
  <title>Gestión Usuarios Adicionar</title>
  <link rel="stylesheet" href="css/estilos_virtual.css" type="text/css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #fff8e1;
      margin: 0;
      padding: 0;
    }
    h1, h2 {
      color: #e65100;
      margin: 0.5em 0;
    }
    table {
      border-collapse: collapse;
      margin: auto;
    }
    .header {
      background: #ff9800;
      color: white;
      padding: 10px;
    }
    .header img {
      height: 80px;
    }
    .header h1 {
      color: white;
    }
    .usuario-info {
      text-align: right;
      font-size: 0.9em;
      padding: 10px;
    }
    .usuario-info a {
      color: #fff3e0;
      text-decoration: none;
      font-weight: bold;
    }
    .formulario {
      margin: 20px auto;
      width: 60%;
      background: #fff3e0;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.2);
    }
    .formulario td {
      padding: 8px;
    }
    .form-label {
      background: #ffcc80;
      color: #4e342e;
      font-weight: bold;
      text-align: center;
      width: 40%;
    }
    .form-input {
      background: #ffffff;
      text-align: center;
    }
    input[type="text"], 
    input[type="number"], 
    input[type="password"], 
    select {
      width: 90%;
      padding: 6px;
      border: 1px solid #ff9800;
      border-radius: 4px;
    }
    .botones {
      text-align: center;
      margin-top: 15px;
    }
    .btn {
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      margin: 0 10px;
    }
    .btn-guardar {
      background: #f57c00;
      color: white;
    }
    .btn-volver {
      background: #ffe0b2;
      color: #4e342e;
    }
    hr {
      border: 0;
      border-top: 2px solid #ffb74d;
      margin: 30px 0;
    }
  </style>
</head>
<body>

  <table width="100%">
    <tr class="header">
      <td width="70%" align="left">
        <table width="100%">
          <tr>
            <td width="70%" align="center">
              <h1>Sistema de Monitoreo de Colmenas</h1>
            </td>
          </tr>
        </table>
      </td>
      <td class="usuario-info">
        <?php  
          echo "<b>Nombre Usuario: </b>".$_SESSION["nombre_usuario"]."<br>";
          echo "<b>Tipo Usuario: </b>".$desc_tipo_usuario."<br>";
        ?>
        <a href="cerrar_sesion.php">Cerrar Sesión</a>
      </td>
    </tr>

<?php
if ((isset($_POST["enviado"]))) {
  $nombre_usuario = $_POST["nombre_usuario"];
  $nombre_usuario = str_replace("�","n",$nombre_usuario);
  $nombre_usuario = str_replace("�","N",$nombre_usuario);
  $num_id = $_POST["num_id"];
  $tipo_usuario = $_POST["tipo_usuario"];
  $direccion = $_POST["direccion"];
  $login = $_POST["login"];
  $activo = $_POST["activo"];
  $password = $_POST["password"];
  $id_tarjeta = $_POST["id_tarjeta"];
  $password_enc = md5($password);
  $mysqli = new mysqli($host, $user, $pw, $db);
  $sqlcon = "SELECT * from usuarios where identificacion='$num_id'";
  $resultcon = $mysqli->query($sqlcon);
  $numero_filas = $resultcon->num_rows;

  if ($numero_filas > 0) { 
    header('Location: gestion_usuarios.php?mensaje=5');
  } else {
    $sql = "INSERT INTO usuarios(tipo_usuario, nombre_completo, identificacion, passwd, direccion, login, activo, id_tarjeta) 
    VALUES ('$tipo_usuario','$nombre_usuario','$num_id','$password_enc','$direccion','$login','$activo','$id_tarjeta')";
    $result1 = $mysqli->query($sql);
    if ($result1 == 1) {
      header('Location: gestion_usuarios.php?mensaje=3');
    } else {
      header('Location: gestion_usuarios.php?mensaje=4');
    }
  }
} else {
?>

<tr>
  <td colspan="2">
    <div class="formulario">
      <h2>Gestión Usuarios - Adición Usuario</h2>
      <form method="POST" action="gestion_usuarios_add.php">
        <table width="100%" border="0">
          <tr>
            <td class="form-label">Nombre Usuario</td>
            <td class="form-input"><input type="text" name="nombre_usuario" required></td>
          </tr>
          <tr>
            <td class="form-label">Número Id</td>
            <td class="form-input"><input type="number" name="num_id" required></td>
          </tr>
          <tr>
            <td class="form-label">Tipo Usuario</td>
            <td class="form-input">
              <select name="tipo_usuario" required>
                <?php
                $sql6 = "SELECT * from tipo_usuario order by id DESC";
                $result6 = $mysqli->query($sql6);
                while($row6 = $result6->fetch_array(MYSQLI_NUM)) {
                  $tipo_usuario_con = $row6[0];
                  $desc_tipo_usuario_con = $row6[1];
                  echo "<option value='$tipo_usuario_con'>$desc_tipo_usuario_con</option>";
                }
                ?>
              </select>
            </td>
          </tr>
          <tr>
            <td class="form-label">Usuario</td>
            <td class="form-input"><input type="text" name="login" required></td>
          </tr>
          <tr>
            <td class="form-label">Clave</td>
            <td class="form-input"><input type="password" name="password" required></td>
          </tr>
          <tr>
            <td class="form-label">Dirección</td>
            <td class="form-input"><input type="text" name="direccion" required></td>
          </tr>
          <tr>
            <td class="form-label">Teléfono</td>
            <td class="form-input"><input type="number" name="telefono" required></td>
          </tr>
          <tr>
            <td class="form-label">Id Tarjeta</td>
            <td class="form-input"><input type="number" name="id_tarjeta" required></td>
          </tr>
          <tr>
            <td class="form-label">Activo (S/N)</td>
            <td class="form-input">
              <select name="activo" required>
                <option value="1">S (Activo)</option>
                <option value="0">N (Inactivo)</option>
              </select>
            </td>
          </tr>
        </table>
        <br>
        <input type="hidden" value="S" name="enviado">
        <div class="botones">
          <input class="btn btn-guardar" type="submit" value="Grabar" name="Modificar">
      </form>
          <form method="POST" action="gestion_usuarios.php" style="display:inline;">
            <input class="btn btn-volver" type="submit" value="Volver" name="Volver">
          </form>
        </div>
      <hr>
    </div>
  </td>
</tr>

<?php } ?>
</table>
</body>
</html>
