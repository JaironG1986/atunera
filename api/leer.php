<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../config/database.php';

try {
    $db = (new Database())->getConnection();

    if (!$db) {
        throw new PDOException("No se pudo establecer conexión con la base de datos.");
    }

    $stmtProv = $db->query("SELECT id, nombre FROM proveedores ORDER BY nombre");
    $providers = $stmtProv->fetchAll(PDO::FETCH_ASSOC);

    $stmtEsp = $db->query("SELECT id, nombre FROM especies ORDER BY id");
    $species = $stmtEsp->fetchAll(PDO::FETCH_ASSOC);

    $historySql = "
        SELECT
            p.id,
            p.fecha_registro,
            p.lote,
            p.compra,
            p.partida,
            p.peso_neto,
            p.temperatura,
            p.rendimiento_esperado,
            p.kg_utilizables,
            p.alerta_status,
            p.total_pt,
            p.total_lomo,
            p.total_miga,
            p.proceso,
            e.nombre AS especie_nombre,
            t.rango AS talla_rango,
            pr.nombre AS proveedor_nombre
        FROM predicciones p
        LEFT JOIN especies e ON p.especie_id = e.id
        LEFT JOIN tallas t ON p.talla_id = t.id
        LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
        ORDER BY p.fecha_registro DESC
        LIMIT 200
    ";

    $stmtHist = $db->query($historySql);
    $history = $stmtHist->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => [
            "providers" => $providers,
            "species" => $species,
            "history" => $history
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
