<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/database.php';
require_once '../core/Prediccion.php';

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->lote) && !empty($data->peso)) {
    try {
        $db = (new Database())->getConnection();
        
        $result = Prediction::calculate($data->peso, $data->temperatura, $data->proveedor, $data->especie, $data->talla);
        
        $query = "INSERT INTO predicciones 
                  (lote, partida, especie_id, talla_id, proveedor_id, peso_neto, temperatura, rendimiento_esperado, kg_utilizables, alerta_status) 
                  VALUES (:lote, :partida, :esp, :talla, :prov, :peso, :temp, :rend, :kg, :alerta)";
        
        $stmt = $db->prepare($query);
        
        $rend = $result['percent'] / 100;
        $alerta_val = $result['alert'] ? 1 : 0;

        $stmt->execute([
            ':lote' => $data->lote,
            ':partida' => $data->partida,
            ':esp' => $data->especie,
            ':talla' => $data->talla,
            ':prov' => $data->proveedor,
            ':peso' => $data->peso,
            ':temp' => $data->temperatura,
            ':rend' => $rend,
            ':kg' => $result['kg'],
            ':alerta' => $alerta_val
        ]);

        echo json_encode(["status" => "success", "data" => $result]);

    } catch(PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error BD: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
}
?>