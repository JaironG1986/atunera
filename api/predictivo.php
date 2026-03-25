<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Prediccion.php';

try {
    $data = json_decode(file_get_contents("php://input"));

    $lote = trim($data->lote ?? '');
    $compra = trim($data->compra ?? 'ND');
    $partida = trim($data->partida ?? '');

    $proveedor = (int)($data->proveedor ?? 0);
    $especie = (int)($data->especie ?? 0);
    $talla = (int)($data->talla ?? 0);

    $peso = isset($data->peso) ? (float)$data->peso : null;
    $temperatura = isset($data->temperatura) ? (float)$data->temperatura : null;

    $totalLomo = (isset($data->total_lomo) && $data->total_lomo !== '' && $data->total_lomo !== null) ? (float)$data->total_lomo : null;
    $totalMiga = (isset($data->total_miga) && $data->total_miga !== '' && $data->total_miga !== null) ? (float)$data->total_miga : null;
    $totalPt   = (isset($data->total_pt)   && $data->total_pt   !== '' && $data->total_pt   !== null) ? (float)$data->total_pt   : null;

    $proceso = trim($data->proceso ?? 'COCIDO');
    if ($proceso === '') {
        $proceso = 'COCIDO';
    }

    if (
        $lote === '' ||
        $partida === '' ||
        $peso === null ||
        $temperatura === null ||
        $proveedor <= 0 ||
        $especie <= 0 ||
        $talla <= 0
    ) {
        throw new InvalidArgumentException("Datos incompletos o inválidos.");
    }

    $db = (new Database())->getConnection();

    if (!$db) {
        throw new PDOException("No se pudo establecer conexión con la base de datos.");
    }

    $result = Prediction::calculate($peso, $temperatura, $proveedor, $especie, $talla);

    $rendimientoDecimal = $result['percent'] / 100;
    $alertaValor = $result['alert'] ? 1 : 0;

    $sql = "
        INSERT INTO predicciones (
            lote,
            compra,
            partida,
            especie_id,
            talla_id,
            proveedor_id,
            peso_neto,
            temperatura,
            rendimiento_esperado,
            kg_utilizables,
            alerta_status,
            total_pt,
            total_lomo,
            total_miga,
            proceso
        ) VALUES (
            :lote,
            :compra,
            :partida,
            :especie_id,
            :talla_id,
            :proveedor_id,
            :peso_neto,
            :temperatura,
            :rendimiento_esperado,
            :kg_utilizables,
            :alerta_status,
            :total_pt,
            :total_lomo,
            :total_miga,
            :proceso
        )
        ON DUPLICATE KEY UPDATE
            compra = VALUES(compra),
            especie_id = VALUES(especie_id),
            talla_id = VALUES(talla_id),
            proveedor_id = VALUES(proveedor_id),
            peso_neto = VALUES(peso_neto),
            temperatura = VALUES(temperatura),
            rendimiento_esperado = VALUES(rendimiento_esperado),
            kg_utilizables = VALUES(kg_utilizables),
            alerta_status = VALUES(alerta_status),
            total_pt = VALUES(total_pt),
            total_lomo = VALUES(total_lomo),
            total_miga = VALUES(total_miga),
            proceso = VALUES(proceso),
            fecha_registro = CURRENT_TIMESTAMP
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':lote' => $lote,
        ':compra' => $compra,
        ':partida' => $partida,
        ':especie_id' => $especie,
        ':talla_id' => $talla,
        ':proveedor_id' => $proveedor,
        ':peso_neto' => $peso,
        ':temperatura' => $temperatura,
        ':rendimiento_esperado' => $rendimientoDecimal,
        ':kg_utilizables' => $result['kg'],
        ':alerta_status' => $alertaValor,
        ':total_pt' => $totalPt,
        ':total_lomo' => $totalLomo,
        ':total_miga' => $totalMiga,
        ':proceso' => $proceso
    ]);

    echo json_encode([
        "status" => "success",
        "data" => [
            "percent" => $result['percent'],
            "kg" => $result['kg'],
            "alert" => $result['alert']
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(400);

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
