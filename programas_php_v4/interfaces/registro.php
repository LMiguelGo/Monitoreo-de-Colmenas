<!DOCTYPE HTML>
<html>
<head>
  <title>Registro de Usuarios - Sistema de Monitoreo</title>
  <meta charset="utf-8">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #fff7e6;
      margin: 0;
      padding: 0;
    }
    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      min-height: 100vh;
    }
    .register-box {
      background: orange;
      color: white;
      padding: 20px 30px;
      border-radius: 10px;
      text-align: center;
      width: 350px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .register-box h2 {
      margin-bottom: 20px;
    }
    .register-box input,
    .register-box select {
      width: 90%;
      padding: 8px;
      margin: 5px 0;
      border: none;
      border-radius: 5px;
    }
    .register-box input[type="submit"] {
      background: #fff;
      color: orange;
      font-weight: bold;
      cursor: pointer;
    }
    .register-box input[type="submit"]:hover {
      background: #f2f2f2;
    }
    .login-link {
      display: block;
      margin-top: 15px;
      color: white;
      font-size: 14px;
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="register-box">
      <h2>Registro de Nuevo Usuario</h2>
      <form method="POST" action="registrar_usuario.php">
        <input type="text" name="nombre_completo" placeholder="Nombre completo" required><br>
        <input type="text" name="numero_id" placeholder="Número de identificación" required><br>
        <input type="date" name="fecha_nacimiento" required><br>
        <input type="text" name="direccion" placeholder="Dirección" required><br>
        <input type="text" name="user" placeholder="Usuario" required><br>
        <input type="password" name="password" placeholder="Contraseña" required><br>
        
        <select name="tipo_usuario" required>
          <option value="">Seleccione tipo de usuario</option>
          <option value="apicultor">Apicultor</option>
          <option value="investigador">Investigador</option>
        </select><br>
        
        <input type="submit" value="Registrar">
      </form>
      <a href="index.php" class="login-link">Volver al inicio</a>
    </div>
  </div>
</body>
</html>
