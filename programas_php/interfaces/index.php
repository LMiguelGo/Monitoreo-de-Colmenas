<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>Página de Inicio - Sistema de Monitoreo de Colmenas</title>
  <meta charset="utf-8">
  <meta http-equiv="refresh" content="15" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #fff7e6;
      margin: 0;
      padding: 0;
    }
    table {
      border-collapse: collapse;
    }
    header {
      background-color: orange;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
    }
    header h1 {
      margin: 0;
      font-size: 26px;
    }
    .login-box {
      background: orange;
      color: white;
      padding: 20px;
      border-radius: 8px;
      text-align: center;
    }
    .login-box h2 {
      margin-bottom: 15px;
    }
    .login-box table {
      width: 100%;
    }
    .login-box td {
      padding: 6px;
    }
    .login-box input[type="text"],
    .login-box input[type="password"] {
      width: 90%;
      padding: 5px;
      border: none;
      border-radius: 4px;
    }
    .login-box input[type="submit"] {
      background: #fff;
      color: orange;
      font-weight: bold;
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .login-box input[type="submit"]:hover {
      background: #f2f2f2;
    }
    .error-box {
      margin-top: 10px;
      background: #ffdddd;
      color: #d60000;
      font-weight: bold;
      padding: 8px;
      border-radius: 5px;
    }
    .content {
      background: #C8DDC8;
      padding: 20px;
    }
    .content h2 {
      margin-top: 10px;
      color: #000;
    }
    .content p {
      color: #555;
      font-size: 15px;
      text-align: justify;
      line-height: 1.5;
    }
    .banner {
      background: orange;
      text-align: center;
      padding: 10px;
    }
    .banner h1 {
      margin: 0;
      color: white;
    }
  </style>
</head>
<body>
  <table width="90%" align="center" cellpadding="5" border="0">
    <tr>
      <!-- Imagen del panal -->
      <td valign="top" align="center" width="70%" bgcolor="orange">
        <img src="img/panal.jpg" width="650" height="200">
      </td>
      <!-- Caja de login -->
      <td valign="top" align="center" width="30%">
        <div class="login-box">
          <h2>Ingreso de Usuarios</h2>
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
                <td></td>
                <td align="center"><input type="submit" value="Ingresar" name="Enviar"></td>
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
        </div>
      </td>
    </tr>
    <!-- Banner inferior -->
    <tr>
      <td colspan="2" class="banner">
        <h1>Sistema de Monitoreo de Colmenas</h1>
      </td>
    </tr>
    <!-- Contenido -->
    <tr>
      <td valign="top" align="left" colspan="2" class="content">
        <h2>Descripción del Sistema</h2>
        <p>
          El sistema de monitoreo de colmenas está diseñado para supervisar en tiempo real las condiciones ambientales internas y externas de la colmena, así como el flujo de abejas en sus accesos. Para ello, se utilizan sensores de <b>temperatura y humedad</b> que permiten evaluar el microclima dentro de la colmena, y sensores <b>infrarrojos</b> que registran la entrada y salida de abejas. Toda esta información es transmitida a un servidor en línea, donde se almacena y procesa, permitiendo al apicultor acceder a los datos desde cualquier lugar y recibir alertas inmediatas cuando se detectan valores fuera de los rangos establecidos.
        </p>
        <h2>Servicios</h2>
        <p>
          El sistema ofrece al apicultor diversas funcionalidades: <br><br>
          • <b>Monitoreo ambiental en tiempo real</b> (temperatura y humedad dentro de la colmena). <br>
          • <b>Conteo de abejas</b> que ingresan y salen, para estimar la actividad de la colonia. <br>
          • <b>Alertas automáticas por Telegram</b>, notificando anomalías en el microclima o en la actividad de vuelo. <br>
          • <b>Almacenamiento de datos en la nube</b>, facilitando el análisis histórico y la comparación de diferentes colmenas. <br>
          • <b>Actualización dinámica de umbrales</b> desde el servidor, sin necesidad de reprogramar el sistema. <br>
        </p>
        <h2>Quiénes Somos</h2>
        <p>
          Somos un equipo enfocado en la aplicación de tecnologías de <b>electrónica, telecomunicaciones e Internet de las Cosas (IoT)</b> al servicio de la apicultura. Nuestro objetivo es brindar a los apicultores herramientas innovadoras que permitan mejorar la gestión de sus colmenas, garantizar el bienestar de las abejas y optimizar la producción de miel, a través de soluciones accesibles, confiables y adaptadas a las necesidades del sector.
        </p>
      </td>
    </tr>
  </table>
</body>
</html>
