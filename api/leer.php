<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/database.php';

try {
    $db = (new Database())->getConnection();
    
    $stmt_prov = $db->query("SELECT id, nombre FROM proveedores");
    $providers_data = $stmt_prov->fetchAll(PDO::FETCH_ASSOC);
    
   
    $stmt_esp = $db->query("SELECT id, nombre FROM especies");
    $species_data = $stmt_esp->fetchAll(PDO::FETCH_ASSOC);

  
    $stmt_hist = $db->query("SELECT p.lote, p.partida, e.nombre as especie_nombre, 
                             t.rango as talla_rango, pr.nombre as proveedor_nombre, 
                             p.peso_neto, p.rendimiento_esperado, p.kg_utilizables, 
                             p.alerta_status, p.fecha_registro 
                             FROM predicciones p
                             JOIN especies e ON p.especie_id = e.id
                             JOIN tallas t ON p.talla_id = t.id
                             JOIN proveedores pr ON p.proveedor_id = pr.id
                             ORDER BY p.fecha_registro DESC LIMIT 20");
    $history_data = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => ["providers" => $providers_data, "species" => $species_data, "history" => $history_data]
    ]);
} catch(PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>