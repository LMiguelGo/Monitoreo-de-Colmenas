<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
require_once("../config/conexion_bd.php");

// Parámetros
$id_colmena = isset($_GET["id_colmena"]) ? intval($_GET["id_colmena"]) : 0;
$inicio     = isset($_GET["inicio"]) ? $_GET["inicio"] : date("Y-m-d H:i:s", strtotime("-1 hour"));
$fin        = isset($_GET["fin"]) ? $_GET["fin"] : date("Y-m-d H:i:s");
$periodo    = isset($_GET["periodo"]) ? intval($_GET["periodo"]) : 60; // en segundos por defecto

// Validaciones básicas
if ($periodo <= 0) $periodo = 60;

// Conexión
$conexion = new mysqli($host, $user, $pw, $db);
if ($conexion->connect_error) {
  http_response_code(500);
  echo json_encode(["error" => "Error de conexión: " . $conexion->connect_error]);
  exit;
}

// Preparo SQL agrupando por intervalos UNIX_TIMESTAMP floor(...)
$sql = "
SELECT 
  FROM_UNIXTIME(
    FLOOR(UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(fecha,' ',hora), '%Y-%m-%d %H:%i:%s')) / ?) * ?
  ) AS tiempo,
  AVG(temperatura) AS temp_prom,
  AVG(humedad) AS hum_prom,
  AVG(actividad_entrante) AS act_entrante_prom,
  AVG(actividad_saliente) AS act_saliente_prom
FROM datos_medidos
WHERE STR_TO_DATE(CONCAT(fecha,' ',hora), '%Y-%m-%d %H:%i:%s') BETWEEN ? AND ?
";

$params = [];
$types = ""; // tipos para bind_param

// Si se especificó id_colmena, añadir filtro
if ($id_colmena > 0) {
  $sql .= " AND colmena_id = ? ";
}

// Agrupar y ordenar
$sql .= " GROUP BY tiempo ORDER BY tiempo ASC; ";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
  http_response_code(500);
  echo json_encode(["error" => "Error preparando la consulta: " . $conexion->error]);
  exit;
}

// Bind dinámico dependiendo si id_colmena está presente
if ($id_colmena > 0) {
  // tipos: i (periodo), i (periodo), s (inicio), s (fin), i (id_colmena)
  $stmt->bind_param("iissi", $periodo, $periodo, $inicio, $fin, $id_colmena);
} else {
  // tipos: i (periodo), i (periodo), s (inicio), s (fin)
  $stmt->bind_param("iiss", $periodo, $periodo, $inicio, $fin);
}

if (!$stmt->execute()) {
  http_response_code(500);
  echo json_encode(["error" => "Error ejecutando la consulta: " . $stmt->error]);
  exit;
}

$res = $stmt->get_result();
$datos = [];
while ($row = $res->fetch_assoc()) {
  $datos[] = [
    "tiempo" => $row["tiempo"],
    "temperatura" => $row["temp_prom"] !== null ? floatval($row["temp_prom"]) : null,
    "humedad" => $row["hum_prom"] !== null ? floatval($row["hum_prom"]) : null,
    "actividad_entrante" => $row["act_entrante_prom"] !== null ? floatval($row["act_entrante_prom"]) : null,
    "actividad_saliente" => $row["act_saliente_prom"] !== null ? floatval($row["act_saliente_prom"]) : null
  ];
}

echo json_encode($datos, JSON_UNESCAPED_UNICODE);
?>
