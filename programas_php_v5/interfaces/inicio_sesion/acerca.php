<!DOCTYPE HTML>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Acerca de - Sistema de Monitoreo de Colmenas</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #ffcc66, #ff9966);
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
      overflow-y: auto;
    }

    .about-box {
      background: white;
      color: #333;
      padding: 30px 40px;
      border-radius: 15px;
      width: 90%;
      max-width: 700px;
      margin: 40px auto;
      box-shadow: 0 6px 15px rgba(0,0,0,0.25);
      animation: fadeIn 0.8s ease;
    }

    h1 {
      color: orange;
      text-align: center;
      margin-bottom: 10px;
    }

    h2 {
      color: #e68a00;
      margin-top: 25px;
    }

    p {
      text-align: justify;
      line-height: 1.6;
    }

    ul {
      list-style-type: none;
      padding: 0;
    }

    li {
      padding: 4px 0;
    }

    strong {
      color: #cc6600;
    }

    .btn-volver {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      background: orange;
      color: white;
      padding: 10px 15px;
      border-radius: 8px;
      font-weight: bold;
      transition: background 0.3s;
    }

    .btn-volver:hover {
      background: #e69500;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .integrantes {
      background: #fff3e0;
      border-radius: 10px;
      padding: 15px;
      margin-top: 10px;
    }

    /* Ajustes para pantallas pequeñas */
    @media (max-width: 600px) {
      .about-box {
        padding: 20px;
        margin: 20px;
      }
      h1 {
        font-size: 22px;
      }
      h2 {
        font-size: 18px;
      }
      p, li {
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
  <div class="about-box">
    <h1>Acerca del Sistema</h1>

    <p>
      El <strong>Sistema de Monitoreo de Colmenas</strong> es un proyecto desarrollado por estudiantes del programa de 
      <strong>Ingeniería en Electrónica y Telecomunicaciones</strong> de la <strong>Universidad del Cauca</strong>. 
      Su propósito es ofrecer una herramienta web que permita supervisar en tiempo real las condiciones ambientales 
      y la actividad de las colmenas mediante sensores de <strong>temperatura</strong>, <strong>humedad</strong> 
      y <strong>actividad</strong>, además de permitir el control automático de actuadores como calefactores y 
      compuertas de entrada.
    </p>

    <h2>Funciones del Sistema</h2>
    <p>
      El sitio web permite el acceso a dos tipos de usuarios: <strong>Apicultores</strong> e <strong>Investigadores</strong>.
    </p>
    <ul>
      <li>🐝 <strong>Apicultor:</strong> puede ver sus colmenas, agregar nuevas, editar los umbrales de sensores, activar o desactivar el control automático de actuadores, visualizar tablas y gráficas de datos y consultar la lista de usuarios registrados.</li>
      <li>🔬 <strong>Investigador:</strong> puede consultar las gráficas y tablas de datos registrados para análisis comparativos.</li>
    </ul>

    <h2>Equipo de Desarrollo</h2>
    <div class="integrantes">
      <ul>
        <li>👩‍💻 <strong>Angela Isabel Becerra Muñoz</strong> (S)</li>
        <li>👩‍💻 <strong>Yulieth Gabriela Jaramillo</strong> (S)</li>
        <li>👨‍🔧 <strong>Oscar Styben Matabajoy Narvaez</strong> (H)</li>
        <li>👩‍💻 <strong>Evelin Nayeli Ortiz Cabrera</strong> (SM)</li>
        <li>👨‍💻 <strong>Francisco David Pino Mamian</strong> (S)</li>
        <li>👩‍🔧 <strong>Laura Isabel Reyes Fernández</strong> (H)</li>
        <li>👨‍💻 <strong>Luis Miguel Gómez Muñoz</strong> (S)</li>
      </ul>
      <p><em>(S: Software | H: Hardware | SM: Scrum Master)</em></p>
    </div>

    <h2>Información del Proyecto</h2>
    <p>
      Este proyecto fue desarrollado como parte de la asignatura <strong>Proyecto Integrador</strong> y se inició el 
      <strong>8 de agosto de 2025</strong>.
    </p>

    <div style="text-align:center;">
      <a href="index.php" class="btn-volver">⬅ Volver al inicio de sesión</a>
    </div>
  </div>
</body>
</html>
