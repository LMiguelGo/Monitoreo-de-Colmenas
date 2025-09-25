<!DOCTYPE HTML>
<html>
<head>
  <title>Inicio de Sesión - Sistema de Monitoreo de Colmenas</title>
  <meta charset="utf-8">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      height: 100vh;
      background: linear-gradient(135deg, #ffcc66, #ff9966);
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      background: white;
      color: #333;
      padding: 30px 40px;
      border-radius: 15px;
      text-align: center;
      width: 350px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.25);
      animation: fadeIn 0.8s ease;
    }

    .login-box h2 {
      margin-bottom: 25px;
      color: orange;
      font-size: 22px;
    }

    .login-box table {
      width: 100%;
    }

    .login-box td {
      padding: 8px;
    }

    .login-box input[type="text"],
    .login-box input[type="password"],
    .login-box select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
    }

    .login-box input[type="text"]:focus,
    .login-box input[type="password"]:focus,
    .login-box select:focus {
      outline: none;
      border-color: orange;
      box-shadow: 0 0 5px rgba(255,165,0,0.5);
    }

    .login-box input[type="submit"] {
      background: orange;
      color: white;
      font-weight: bold;
      padding: 10px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s;
      width: 100%;
    }

    .login-box input[type="submit"]:hover {
      background: #e69500;
    }

    .acerca-link, .registro-link {
      display: block;
      margin-top: 15px;
      color: orange;
      text-decoration: none;
      font-size: 17px;
    }

    .acerca-link:hover, .registro-link:hover {
      text-decoration: underline;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Ingreso al Sistema</h2>
    <form method="POST" action="validar.php">
      <table>
        <tr>
          <td align="right">Usuario:</td>
          <td><input type="text" name="login1" required></td>
        </tr>
        <tr>
          <td align="right">Password:</td>
          <td><input type="password" name="passwd1" required></td>
        </tr>
        <tr>
          <td align="right">Tipo:</td>
          <td>
            <select name="tipo_usuario" required>
              <option value="apicultor">Apicultor</option>
              <option value="investigador">Investigador</option>
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="2" align="center">
            <input type="submit" value="Ingresar" name="Enviar">
          </td>
        </tr>
      </table>
      <?php
            if (isset($_GET["mensaje"])) {
              $mensaje = $_GET["mensaje"];
              if ($_GET["mensaje"]!=""){ ?>
                <div class="error-box">
                  <?php 
                    if ($mensaje == 1)
                      echo "El password del usuario no coincide.";
                    if ($mensaje == 2)
                      echo "No hay usuarios con el login ingresado o está inactivo.";
                    if ($mensaje == 3)
                      echo "No se ha logueado en el Sistema. Por favor ingrese los datos.";
                    if ($mensaje == 4)
                      echo "Su tipo de usuario no tiene permisos suficientes.";
                  ?>
                </div>
            <?php }
            }
            ?>
    </form>
    <a href="registro.php" class="registro-link">Registrarse</a>
    <a href="acerca.php" class="acerca-link">Acerca de</a>
  </div>
</body>
</html>
