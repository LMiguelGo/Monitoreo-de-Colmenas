<!DOCTYPE HTML>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Registro de Usuarios - Sistema de Monitoreo</title>
  <style>
    /* --- Estilo general --- */
    body {
      margin: 0;
      padding: 0;
      font-family: "Segoe UI", Arial, sans-serif;
      background: linear-gradient(135deg, #fff4e0, #ffe1b0);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    /* --- Contenedor principal --- */
    .register-box {
      background: #ffb13cff;
      color: white;
      padding: 40px 35px;
      border-radius: 15px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.2);
      text-align: center;
      animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* --- Título --- */
    .register-box h2 {
      margin-bottom: 25px;
      font-size: 22px;
      letter-spacing: 0.5px;
    }

    /* --- Inputs y select --- */
    .register-box input,
    .register-box select {
      width: 100%;
      padding: 10px 12px;
      margin: 6px 0 12px;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      outline: none;
      box-sizing: border-box;
    }

    .register-box input[type="text"],
    .register-box input[type="password"],
    .register-box input[type="date"],
    .register-box select {
      background: #fff;
      color: #333;
    }

    .register-box input:focus,
    .register-box select:focus {
      box-shadow: 0 0 0 2px rgba(255,255,255,0.8);
    }

    /* --- Botón principal --- */
    .register-box input[type="submit"] {
      background: white;
      color: #ff9800;
      font-weight: bold;
      text-transform: uppercase;
      cursor: pointer;
      transition: 0.3s;
    }

    .register-box input[type="submit"]:hover {
      background: #f8f8f8;
      transform: scale(1.03);
    }

    /* --- Enlace inferior --- */
    .login-link {
      display: inline-block;
      margin-top: 15px;
      color: #fff;
      font-size: 14px;
      text-decoration: none;
      transition: 0.3s;
    }

    .login-link:hover {
      color: #ffe8cc;
      text-decoration: underline;
    }

    /* --- Responsividad --- */
    @media (max-width: 480px) {
      .register-box {
        width: 90%;
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>
  <div class="register-box">
    <h2>Registro de Nuevo Usuario</h2>
    <form method="POST" action="includes/registrar_usuario.php">
      <input type="text" name="nombre_completo" placeholder="Nombre completo" required>
      <input type="text" name="numero_id" placeholder="Número de identificación" required>
      <input type="date" name="fecha_nacimiento" required>
      <input type="text" name="direccion" placeholder="Dirección" required>
      <input type="text" name="user" placeholder="Usuario" required>
      <input type="password" name="password" placeholder="Contraseña" required>

      <select name="tipo_usuario" required>
        <option value="">Seleccione tipo de usuario</option>
        <option value="apicultor">Apicultor</option>
        <option value="investigador">Investigador</option>
      </select>

      <input type="submit" value="Registrar">
    </form>
    <a href="index.php" class="login-link">← Volver al inicio</a>
  </div>
</body>
</html>
