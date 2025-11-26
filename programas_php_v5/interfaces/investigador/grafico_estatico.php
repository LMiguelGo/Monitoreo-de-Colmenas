<?php
require_once("config/conexion_bd.php");

session_start();
$desc_tipo_usuario = "Investigador";

if (!isset($_SESSION["autenticado"]) || $_SESSION["autenticado"] != "SIx3") {
  header('Location: ../inicio_sesion/index.php?mensaje=3');
  exit;
}

if (!isset($_SESSION["tipo_usuario"]) || strtolower($_SESSION["tipo_usuario"]) != "investigador") {
  header('Location: ../inicio_sesion/index.php?mensaje=4');
  exit;
}

$id_apicultor = $_SESSION["id_usuario"] ?? 1;
$conexion = new mysqli($host, $user, $pw, $db);
if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}
$colmenas = $conexion->query("SELECT id, nombre FROM colmenas");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gráfico de Datos de Colmenas</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* (Mantengo tus estilos originales en una sola línea por elemento) */
    body {font-family: Arial, sans-serif; background-color: #fff8f0; margin: 0; padding: 0;}
    header {background: orange; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center;}
    header h1 {margin: 0; font-size: 24px;}
    .user-info {text-align: right; font-size: 14px;}
    .user-info p {margin: 2px 0;}
    .user-info a.btn {display: inline-block; margin-top: 6px; background: #fff; color: #cc6600; font-weight: bold; padding: 5px 10px; text-decoration: none; border-radius: 4px;}
    .user-info a.btn:hover {background: #f2f2f2;}
    .menu-nav {display: flex; justify-content: center; background: orange; padding: 10px 0; border-top: 2px solid #e68a00;}
    .menu-nav a {color: white; text-decoration: none; margin: 0 20px; font-weight: bold; transition: color 0.3s;}
    .menu-nav a:hover {color: #333;}
    .container {max-width: 900px; margin: 40px auto; background: white; padding: 20px 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,.1);}
    h2 {text-align: center; color: #cc6600; margin-bottom: 25px;}
    .controls {display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-bottom: 20px; padding: 15px; background: #fff3e0; border-radius: 10px;}
    .controls label {font-weight: bold; color: #a65300; display: block; margin-bottom: 5px;}
    .controls select, .controls input {padding: 8px; border: 1px solid #ccc; border-radius: 6px; min-width: 180px;}
    button {display: block; margin: 20px auto; background: orange; color: white; font-weight: bold; padding: 8px 18px; border: none; border-radius: 8px; cursor: pointer;}
    button:hover {background: #e68a00;}
    canvas {margin-top: 20px; display: block; margin-left: auto; margin-right: auto;}
    #mensaje {color: red; text-align: center; font-weight: bold;}
  </style>
</head>

<body>
<header>
  <h1>Sistema de Monitoreo de Colmenas</h1>
  <div class="user-info">
    <p><strong>Usuario:</strong> <?= htmlspecialchars($_SESSION["nombre_usuario"] ?? 'Desconocido') ?></p>
    <p><strong>Tipo:</strong> <?= htmlspecialchars($desc_tipo_usuario) ?></p>
    <a href="../inicio_sesion/includes/cerrar_sesion.php" class="btn">Cerrar Sesión</a>
  </div>
</header>

<?php include "includes/cinta_investigador.php"; ?>

<div class="container">
  <h2>Gráfico de datos por periodo</h2>

  <div class="controls">
    <div>
      <label for="colmena">Colmena:</label>
      <select id="colmena">
        <?php while ($row = $colmenas->fetch_assoc()): ?>
          <option value="<?= intval($row['id']) ?>"><?= htmlspecialchars($row['nombre']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div>
      <label for="fecha_inicio">Fecha de inicio:</label>
      <input type="datetime-local" id="fecha_inicio">
    </div>

    <div>
      <label for="fecha_fin">Fecha de fin:</label>
      <input type="datetime-local" id="fecha_fin">
    </div>

    <div>
      <label for="periodo">Periodo:</label>
      <select id="periodo">
        <option value="60">1 min</option>
        <option value="120">2 min</option>
        <option value="300">5 min</option>
        <option value="600">10 min</option>
        <option value="1800">30 min</option>
        <option value="3600">1 h</option>
        <option value="7200">2 h</option>
      </select>
    </div>
  </div>

  <button id="btn-generar">Generar gráfico</button>
  <p id="mensaje"></p>

  <canvas id="grafico" width="800" height="400"></canvas>
</div>

<script>
const ctx = document.getElementById('grafico').getContext('2d');
let chart;

function formatearFechaSQL(valor) {
  // datetime-local -> "YYYY-MM-DDTHH:MM" => queremos "YYYY-MM-DD HH:MM:00"
  return valor.replace('T', ' ') + ':00';
}

async function generarGrafico() {
  const idColmena = document.getElementById('colmena').value;
  const inicioRaw = document.getElementById('fecha_inicio').value;
  const finRaw = document.getElementById('fecha_fin').value;
  const periodo = document.getElementById('periodo').value;
  const mensaje = document.getElementById('mensaje');
  mensaje.textContent = '';

  if (!inicioRaw || !finRaw) {
    mensaje.textContent = "⚠️ Debes seleccionar una fecha de inicio y una de finalización.";
    return;
  }

  const inicio = formatearFechaSQL(inicioRaw);
  const fin = formatearFechaSQL(finRaw);

  // Incluyo id_colmena en la URL
  const url = `includes/get_datos_grafica_e.php?id_colmena=${encodeURIComponent(idColmena)}&inicio=${encodeURIComponent(inicio)}&fin=${encodeURIComponent(fin)}&periodo=${encodeURIComponent(periodo)}`;

  try {
    const resp = await fetch(url);
    if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
    const datos = await resp.json();

    if (!Array.isArray(datos) || datos.length === 0) {
      mensaje.textContent = "❌ No hay datos para el rango seleccionado.";
      if (chart) chart.destroy();
      return;
    }

    const labels = datos.map(d => d.tiempo);
    const temps = datos.map(d => d.temperatura);
    const hums  = datos.map(d => d.humedad);
    const actsEntrante = datos.map(d => d.actividad_entrante);
    const actsSaliente = datos.map(d => d.actividad_saliente);

    if (chart) chart.destroy();

    chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [
          { label: "Temperatura (°C)", data: temps, borderColor: "#ff6600", fill: false },
          { label: "Humedad (%)", data: hums, borderColor: "#3399ff", fill: false },
          { label: "Actividad Entrante", data: actsEntrante, borderColor: "#33cc33", fill: false },
          { label: "Actividad Saliente", data: actsSaliente, borderColor: "#cc33cc", fill: false }
        ]
      },
      options: {
        scales: {
          x: { title: { display: true, text: "Tiempo" } },
          y: { title: { display: true, text: "Valor" } }
        },
        plugins: { legend: { position: 'bottom' } }
      }
    });

  } catch (e) {
    mensaje.textContent = "⚠️ Error cargando datos: " + e.message;
    if (chart) chart.destroy();
  }
}

document.getElementById('btn-generar').addEventListener('click', generarGrafico);

// Valores por defecto (última hora)
const ahora = new Date();
const antes = new Date(ahora.getTime() - 60 * 60 * 1000);
document.getElementById('fecha_inicio').value = antes.toISOString().slice(0,16);
document.getElementById('fecha_fin').value = ahora.toISOString().slice(0,16);
</script>

</body>
</html>
