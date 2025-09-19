<?php
include "conexion.php";
session_start();

// Inicializar conexión
$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Error al conectar a la base de datos: " . $mysqli->connect_error);
}

// Verificar sesión
if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php?mensaje=3");
    exit();
}

// Obtener ID de colmena desde GET
if (!isset($_GET['id_col'])) {
    die("No se recibió la colmena.");
}

$id_colmena_enc = $_GET['id_col'];

// Buscar colmena en la base de datos
$sql = "SELECT * FROM colmenas";
$result = $mysqli->query($sql);
if (!$result) die("Error al consultar colmenas: " . $mysqli->error);

$colmena = null;
while($row = $result->fetch_array(MYSQLI_NUM)) {
    if (md5($row[0]) == $id_colmena_enc) {
        $colmena = $row;
        break;
    }
}

if (!$colmena) {
    die("Colmena no encontrada.");
}

// Guardar datos en variables
$id_colmena     = $colmena[0];
$nombre         = $colmena[1];
$latitud        = $colmena[2];
$longitud       = $colmena[3];
$id_usuario_col = $colmena[4];
$fecha_registro = $colmena[5];

// Verificar que el usuario sea dueño de la colmena
if ($id_usuario_col != $_SESSION["id_usuario"]) {
    die("No tienes permiso para modificar esta colmena.");
}

// Si se envía el formulario, actualizar los datos
if (isset($_POST['guardar'])) {
    $nombre_new  = $mysqli->real_escape_string($_POST['nombre']);
    $latitud_new = $mysqli->real_escape_string($_POST['latitud']);
    $longitud_new= $mysqli->real_escape_string($_POST['longitud']);

    // Actualizar colmena
    $sql_upd = "UPDATE colmenas 
                SET nombre='$nombre_new', latitud='$latitud_new', longitud='$longitud_new' 
                WHERE id_colmena ='$id_colmena'"; // Cambiar 'id' si tu columna tiene otro nombre real
    if ($mysqli->query($sql_upd)) {
        header("Location: gestion_usuarios.php?mensaje=7"); // mensaje personalizado
        exit();
    } else {
        $error = "Error al actualizar: " . $mysqli->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modificar Colmena</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial; background: #fff8f0; }
        .form-container { width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ffcc99; border-radius: 6px; background: #fff3e0; }
        input { width: 100%; padding: 8px; margin: 5px 0; }
        .btn { background: #ff9900; color: white; padding: 8px 12px; border: none; cursor: pointer; border-radius: 4px; }
        .btn:hover { background: #cc6600; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Modificar Colmena</h2>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
        <label>Nombre</label>
        <input type="text" name="nombre" value="<?php echo $nombre; ?>" required>

        <label>Latitud</label>
        <input type="text" name="latitud" value="<?php echo $latitud; ?>" required>

        <label>Longitud</label>
        <input type="text" name="longitud" value="<?php echo $longitud; ?>" required>

        <input type="submit" name="guardar" value="Guardar cambios" class="btn">
    </form>
</div>
</body>
</html>
