<?php
// agregar_colmena.php
include "conexion.php";
session_start();

// Verificar sesión
if (!isset($_SESSION["autenticado"]) || $_SESSION["autenticado"] != "SIx3") {
    header("Location: index.php?mensaje=3");
    exit;
}

$mensaje = "";
$mostrarBoton = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $latitud = $_POST["latitud"];
    $longitud = $_POST["longitud"];
    $id_usuario = $_SESSION["id_usuario"];

    $mysqli = new mysqli($host, $user, $pw, $db);

    if ($mysqli->connect_errno) {
        die("Error al conectar: " . $mysqli->connect_error);
    }

    $sql = "INSERT INTO colmenas (nombre, latitud, longitud, id_usuario) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssdi", $nombre, $latitud, $longitud, $id_usuario);

    if ($stmt->execute()) {
        $mensaje = "<div style='background:#d4edda; color:#155724; 
                               padding:10px; border-radius:5px; margin-bottom:15px; text-align:center;'>
                        ✅ Colmena agregada con éxito.
                    </div>";
        $mostrarBoton = true;
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
        form { background: #fff; padding: 20px; border-radius: 8px; max-width: 400px; margin: auto; }
        label { display: block; margin-top: 10px; font-weight: bold; text-align:left; }
        input { width: 100%; padding: 8px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc; }
        button { margin-top: 15px; padding: 10px; width: 100%; background: #ff9800; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #e68900; }
        .btn-volver { margin-top: 20px; background: #28a745; }
        .btn-volver:hover { background: #218838; }
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
    <input type="text" id="latitud" name="latitud" required>

    <label for="longitud">Longitud:</label>
    <input type="text" id="longitud" name="longitud" required>

    <button type="submit">Agregar Colmena</button>
</form>

<?php if ($mostrarBoton): ?>
    <a href="gestion_usuarios.php">
        <button class="btn-volver">⬅ Volver a Gestión de Usuarios</button>
    </a>
<?php endif; ?>

</body>
</html>
