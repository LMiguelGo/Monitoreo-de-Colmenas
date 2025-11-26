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

// Inicializar conexión
$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Error al conectar a la base de datos: " . $mysqli->connect_error);
}

// Verificar sesión
if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../inicio_sesion/index.php?mensaje=3");
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
while ($row = $result->fetch_assoc()) {
    if (md5($row["id"]) == $id_colmena_enc) {
        $colmena = $row;
        break;
    }
}

if (!$colmena) {
    die("Colmena no encontrada.");
}

// Guardar datos en variables
$id_colmena     = $colmena["id"];
$nombre         = $colmena["nombre"];
$latitud        = $colmena["latitud"];
$longitud       = $colmena["longitud"];
$ubicacion      = $colmena["ubicacion"];
$dimensiones    = $colmena["dimensiones"];
$poblacion      = $colmena["poblacion_abejas"];
$apicultor_id   = $colmena["apicultor_id"];
$fecha_registro = $colmena["fecha_registro"];

// Verificar que el usuario sea dueño de la colmena
if ($apicultor_id != $_SESSION["id_usuario"]) {
    die("No tienes permiso para modificar esta colmena.");
}

// Si se envía el formulario, actualizar los datos
if (isset($_POST['guardar'])) {
    $nombre_new      = $mysqli->real_escape_string($_POST['nombre']);
    $latitud_new     = $mysqli->real_escape_string($_POST['latitud']);
    $longitud_new    = $mysqli->real_escape_string($_POST['longitud']);
    $ubicacion_new   = $mysqli->real_escape_string($_POST['ubicacion']);
    $dimensiones_new = $mysqli->real_escape_string($_POST['dimensiones']);
    $poblacion_new   = $mysqli->real_escape_string($_POST['poblacion']);

    // Verificar si ya existe otra colmena con el mismo nombre para este apicultor
    $sql_check = "SELECT id FROM colmenas 
                  WHERE nombre = '$nombre_new' 
                  AND apicultor_id = '$apicultor_id' 
                  AND id <> '$id_colmena'";
    $res_check = $mysqli->query($sql_check);

    if ($res_check && $res_check->num_rows > 0) {
        $error = "Ya existe otra colmena con el nombre <b>$nombre_new</b>. Elige uno distinto.";
    } else {
        // Actualizar colmena
        $sql_upd = "UPDATE colmenas 
                    SET nombre='$nombre_new', latitud='$latitud_new', longitud='$longitud_new',
                        ubicacion='$ubicacion_new', dimensiones='$dimensiones_new', poblacion_abejas='$poblacion_new'
                    WHERE id='$id_colmena'";
        if ($mysqli->query($sql_upd)) {
            header("Location: inicio_apicultor.php?mensaje=7");
            exit();
        } else {
            $error = "Error al actualizar: " . $mysqli->error;
        }
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
        .form-container { width: 450px; margin: 50px auto; padding: 20px; border: 1px solid #ffcc99; border-radius: 6px; background: #fff3e0; }
        label { display:block; margin-top:10px; font-weight:bold; }
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
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>

        <label>Latitud</label>
        <input type="number" step="0.000001" name="latitud" value="<?php echo htmlspecialchars($latitud); ?>">

        <label>Longitud</label>
        <input type="number" step="0.000001" name="longitud" value="<?php echo htmlspecialchars($longitud); ?>">

        <label>Ubicación</label>
        <input type="text" name="ubicacion" value="<?php echo htmlspecialchars($ubicacion); ?>">

        <label>Dimensiones</label>
        <input type="text" name="dimensiones" value="<?php echo htmlspecialchars($dimensiones); ?>">

        <label>Población de Abejas</label>
        <input type="number" name="poblacion" value="<?php echo htmlspecialchars($poblacion); ?>">

        <input type="submit" name="guardar" value="Guardar cambios" class="btn">
    </form>
</div>
</body>
</html>
