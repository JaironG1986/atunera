<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT p.*, pr.nombre as proveedor_nombre 
          FROM predicciones p 
          JOIN proveedores pr ON p.proveedor_id = pr.id 
          ORDER BY p.fecha_registro DESC LIMIT 10";

$stmt = $db->prepare($query);
$stmt->execute();

$history = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($history);
?>
