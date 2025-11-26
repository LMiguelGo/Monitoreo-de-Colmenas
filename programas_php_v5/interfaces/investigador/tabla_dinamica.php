<?php
include "config/conexion_bd.php";
session_start();

$desc_tipo_usuario = "Investigador";
if ($_SESSION["autenticado"] != "SIx3") {
    header("Location: ../inicio_sesion/index.php?mensaje=3");
    exit;
} elseif ($_SESSION["tipo_usuario"] != "investigador") {
    header('Location: ../inicio_sesion/index.php?mensaje=4');
    exit;
}

$id_usuario1 = $_SESSION["id_usuario"];
$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Error en la conexión a MySQL: " . $mysqli->connect_error);
}

$sql_colmenas = "SELECT id, nombre FROM colmenas";
$res_colmenas = $mysqli->query($sql_colmenas);

$id_colmena = isset($_GET['id_colmena']) ? intval($_GET['id_colmena']) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Consulta dinámica por colmena</title>
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

  header h1 { margin: 0; font-size: 24px; }

  .user-info { text-align: right; font-size: 14px; }
  .user-info p { margin: 2px 0; }

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

  .user-info a.btn:hover { background: #f2f2f2; }

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
    max-width: 1000px;
    margin: 30px auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
  }

  h2 {
    text-align: center;
    color: #cc6600;
    margin-bottom: 20px;
  }

  form { 
    text-align: center;
    margin-bottom: 20px; 
  }

  select {
    padding: 8px 12px;
    border: 2px solid #ff9800;
    border-radius: 6px;
    font-size: 14px;
  }

  table {
    border-collapse: collapse;
    width: 90%;
    margin: 0 auto 30px;
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

  tr:nth-child(even) { background-color: #f9f9f9; }
  tr:hover { background-color: #ffe0b3; }

  #mensaje {
    text-align: center;
    color: red;
    font-weight: bold;
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

<?php include "includes/cinta_investigador.php"; ?>

<div class="container">
  <h2>Monitoreo dinámico (últimos 50 registros)</h2>

  <form id="formColmena">
    <label for="id_colmena"><b>Seleccionar colmena:</b></label>
    <select name="id_colmena" id="id_colmena" required>
      <option value="">-- Selecciona --</option>
      <?php while($col = $res_colmenas->fetch_assoc()) { ?>
        <option value="<?php echo $col['id']; ?>" <?php if ($id_colmena == $col['id']) echo 'selected'; ?>>
          <?php echo $col['nombre']; ?>
        </option>
      <?php } ?>
    </select>
  </form>

  <div id="tablaDatos"></div>
</div>

<script>
// Función para actualizar tabla cada 5 segundos
function cargarDatos() {
  const colmena = document.getElementById("id_colmena").value;
  if (!colmena) {
    document.getElementById("tablaDatos").innerHTML = "<p id='mensaje'>Selecciona una colmena para ver sus datos.</p>";
    return;
  }

  fetch("includes/get_datos_tabla_d.php?id_colmena=" + colmena)
    .then(response => response.text())
    .then(data => {
      document.getElementById("tablaDatos").innerHTML = data;
    })
    .catch(err => {
      console.error("Error al cargar datos:", err);
    });
}

// Detectar cambio de colmena
document.getElementById("id_colmena").addEventListener("change", cargarDatos);

// Actualizar cada 5 segundos automáticamente
setInterval(cargarDatos, 5000);
</script>

</body>
</html>
