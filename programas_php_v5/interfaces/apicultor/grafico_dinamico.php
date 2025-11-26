<?php
require_once("config/conexion_bd.php");

session_start();
$desc_tipo_usuario = "Apicultor";
if ($_SESSION["autenticado"] != "SIx3") {
  header('Location: ../inicio_sesion/index.php?mensaje=3');
} else {
  if ($_SESSION["tipo_usuario"] != "apicultor")
    header('Location: ../inicio_sesion/index.php?mensaje=4');
}

$desc_tipo_usuario = "Apicultor";
$id_apicultor = $_SESSION["id_usuario"] ?? 1;
$conexion = new mysqli($host, $user, $pw, $db);
$colmenas = $conexion->query("SELECT id, nombre FROM colmenas WHERE apicultor_id = $id_apicultor");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Monitoreo de Colmenas</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: Arial, sans-serif; background-color: #fff8f0; margin: 0; padding: 0; }
    header { background: orange; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
    header h1 { margin: 0; font-size: 24px; }
    .user-info { text-align: right; font-size: 14px; }
    .user-info p { margin: 2px 0; }
    .user-info a.btn { display: inline-block; margin-top: 6px; background: #fff; color: #cc6600; font-weight: bold; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
    .user-info a.btn:hover { background: #f2f2f2; }
    .menu-nav { display: flex; justify-content: center; background: orange; padding: 10px 0; border-top: 2px solid #e68a00; }
    .menu-nav a { color: white; text-decoration: none; margin: 0 20px; font-weight: bold; transition: color 0.3s; }
    .menu-nav a:hover { color: #333; }
    .container { max-width: 900px; margin: 40px auto; background: white; padding: 20px 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,.1); }
    h2 { text-align: center; color: #cc6600; margin-bottom: 25px; }
    .controls { text-align: center; margin-bottom: 20px; }
    .controls select { padding: 8px; border: 1px solid #ccc; border-radius: 6px; min-width: 200px; font-size: 14px; }
    canvas { margin-top: 20px; display: block; margin-left: auto; margin-right: auto; }
    #mensaje { text-align: center; font-weight: bold; color: red; }
  </style>
</head>
<body>
<header>
  <h1>Sistema de Monitoreo de Colmenas</h1>
  <div class="user-info">
    <p><strong>Usuario:</strong> <?= $_SESSION["nombre_usuario"]; ?></p>
    <p><strong>Tipo:</strong> <?= $desc_tipo_usuario; ?></p>
    <a href="../inicio_sesion/includes/cerrar_sesion.php" class="btn">Cerrar Sesión</a>
  </div>
</header>

<?php include "includes/cinta_apicultor.php"; ?>

<div class="container">
  <h2>Gráfico en tiempo real</h2>

  <div class="controls">
    <label for="colmena"><strong>Selecciona una colmena:</strong></label>
    <select id="colmena">
      <?php while ($row = $colmenas->fetch_assoc()): ?>
        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
      <?php endwhile; ?>
    </select>
  </div>

  <p id="mensaje"></p>
  <canvas id="grafico" width="800" height="400"></canvas>
</div>

<script>
const ctx = document.getElementById('grafico').getContext('2d');
let chart;
let intervalo;

async function cargarDatos() {
  const idColmena = document.getElementById('colmena').value;
  const url = `includes/get_datos_grafica_d.php?id_colmena=${idColmena}`;
  const mensaje = document.getElementById('mensaje');

  try {
    const resp = await fetch(url);
    const datos = await resp.json();

    if (!Array.isArray(datos) || datos.length === 0) {
      mensaje.textContent = "❌ No hay datos recientes para esta colmena.";
      if (chart) chart.destroy();
      return;
    }

    mensaje.textContent = "";

    const labels = datos.map(d => d.tiempo);
    const temps = datos.map(d => d.temperatura);
    const hums  = datos.map(d => d.humedad);
    const actsE = datos.map(d => d.actividad_entrante);
    const actsS = datos.map(d => d.actividad_saliente);

    if (chart) {
      chart.data.labels = labels;
      chart.data.datasets[0].data = temps;
      chart.data.datasets[1].data = hums;
      chart.data.datasets[2].data = actsE;
      chart.data.datasets[3].data = actsS;
      chart.update();
    } else {
      chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [
            { label: "Temperatura (°C)", data: temps, borderColor: "#ff6600", fill: false },
            { label: "Humedad (%)", data: hums, borderColor: "#3399ff", fill: false },
            { label: "Actividad Entrante", data: actsE, borderColor: "#33cc33", fill: false },
            { label: "Actividad Saliente", data: actsS, borderColor: "#cc33cc", fill: false }
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
    }
  } catch (err) {
    mensaje.textContent = "⚠️ Error cargando datos: " + err.message;
  }
}

document.getElementById('colmena').addEventListener('change', () => {
  if (intervalo) clearInterval(intervalo);
  cargarDatos();
  intervalo = setInterval(cargarDatos, 5000); // cada 5 segundos
});

// Inicial
cargarDatos();
intervalo = setInterval(cargarDatos, 5000);
</script>
</body>
</html>
