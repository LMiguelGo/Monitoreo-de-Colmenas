<?php
session_start();
$desc_tipo_usuario = "Apicultor";
if ($_SESSION["autenticado"] != "SIx3") {
  header('Location: ../inicio_sesion/index.php?mensaje=3');
} else {
  if ($_SESSION["tipo_usuario"] != "apicultor")
    header('Location: ../inicio_sesion/index.php?mensaje=4');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Menú Principal</title>

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
    margin: 60px auto;
    background: #fff;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
    text-align: center;
  }

  h2 {
    color: #cc6600;
    margin-bottom: 30px;
  }

  .link-button {
    display: block;
    background: #ff6600;
    color: white;
    text-decoration: none;
    padding: 15px 20px;
    margin: 15px auto;
    width: 60%;
    border-radius: 8px;
    font-weight: bold;
    transition: background 0.3s;
  }

  .link-button:hover {
    background: #ff9800;
  }

  footer {
    text-align: center;
    color: #888;
    font-size: 14px;
    margin-top: 40px;
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

  <?php include "includes/cinta_apicultor.php"; ?>

  <div class="container">
    <h2>Tablas de mediciones</h2>

    <a href="tabla_estatica.php" class="link-button">Ver tabla en un lapso de tiempo</a>
    <a href="tabla_dinamica.php" class="link-button">Ver tabla en tiempo real</a>
  </div>
</body>
</html>
