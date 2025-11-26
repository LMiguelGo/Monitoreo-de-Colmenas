<?php
include "config/conexion_bd.php"; //OK
session_start();
$desc_tipo_usuario = "Apicultor";
if ($_SESSION["autenticado"] != "SIx3") {
  header('Location: ../inicio_sesion/index.php?mensaje=3');
} else {
  if ($_SESSION["tipo_usuario"] != "apicultor")
    header('Location: ../inicio_sesion/index.php?mensaje=4');
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre      = $_POST["nombre"];
    $latitud     = $_POST["latitud"];
    $longitud    = $_POST["longitud"];
    $ubicacion   = $_POST["ubicacion"];
    $dimensiones = $_POST["dimensiones"];
    $poblacion   = $_POST["poblacion"];
    $apicultor_id = $_SESSION["id_usuario"];

    $mysqli = new mysqli($host, $user, $pw, $db);

    if ($mysqli->connect_errno) {
        die("Error al conectar: " . $mysqli->connect_error);
    }

    $sql = "INSERT INTO colmenas (nombre, latitud, longitud, ubicacion, dimensiones, poblacion_abejas, apicultor_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sddssii", $nombre, $latitud, $longitud, $ubicacion, $dimensiones, $poblacion, $apicultor_id);

    if ($stmt->execute()) {
        $mensaje = "<div style='background:#d4edda; color:#155724; 
                               padding:10px; border-radius:5px; margin-bottom:15px; text-align:center;'>
                        ✅ Colmena agregada con éxito.
                    </div>";
    } else {
        $mensaje = "<div style='background:#f8d7da; color:#721c24; 
                               padding:10px; border-radius:5px; margin-bottom:15px; text-align:center;'>
                        ❌ Error al registrar la colmena: " . $stmt->error . "
                    </div>";
    }

    $stmt->close();
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Colmena</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 20px; text-align:center; }
        form { background: #fff; padding: 20px; border-radius: 8px; max-width: 450px; margin: auto; }
        label { display: block; margin-top: 10px; font-weight: bold; text-align:left; }
        input { width: 100%; padding: 8px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc; }
        button { margin-top: 15px; padding: 10px; width: 100%; background: #ff9800; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #e68900; }
        .btn-volver { margin-top: 15px; background: #ff9800; font-size: 14px; padding: 6px 18px; width: auto; }
        .btn-volver:hover { background: #e68900; }
    </style>
</head>
<body>

<h2>Agregar Nueva Colmena</h2>

<!-- Mostrar mensaje -->
<?php if (!empty($mensaje)) echo $mensaje; ?>

<form method="POST" action="">
    <label for="nombre">Nombre de la Colmena:</label>
    <input type="text" id="nombre" name="nombre" required>

    <label for="latitud">Latitud:</label>
    <input type="number" step="0.000001" id="latitud" name="latitud" required>

    <label for="longitud">Longitud:</label>
    <input type="number" step="0.000001" id="longitud" name="longitud" required>

    <label for="ubicacion">Ubicación:</label>
    <input type="text" id="ubicacion" name="ubicacion">

    <label for="dimensiones">Dimensiones:</label>
    <input type="text" id="dimensiones" name="dimensiones">

    <label for="poblacion">Población de Abejas:</label>
    <input type="number" id="poblacion" name="poblacion">

    <button type="submit">Agregar Colmena</button>
</form>

<!-- Botón volver siempre disponible -->
<a href="inicio_apicultor.php">  <!-- OK -->
    <button class="btn-volver">⬅ Volver a Mis Colmenas</button>
</a>

</body>
</html>
