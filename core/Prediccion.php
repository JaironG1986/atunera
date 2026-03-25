<?php

class Prediction
{
    public static function calculate($peso, $temp, $proveedor_id, $especie_id, $talla_id): array
    {
        $rendimiento_ideal = 0.46;

        $factor_temp  = ((float)$temp > 4.0) ? 0.92 : 1.0;
        $factor_prov  = ((int)$proveedor_id === 3) ? 0.95 : 1.0;
        $factor_talla = ((int)$talla_id <= 2) ? 0.96 : 1.0;

        $rendimiento_final = $rendimiento_ideal * $factor_temp * $factor_prov * $factor_talla;
        $kg_utilizables    = (float)$peso * $rendimiento_final;
        $alerta            = ($rendimiento_final < 0.44);

        return [
            "percent" => round($rendimiento_final * 100, 2),
            "kg"      => round($kg_utilizables, 2),
            "alert"   => $alerta
        ];
    }
}
