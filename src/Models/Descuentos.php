<?php

namespace Proyecto\Models;

use PDO;
use PDOException;

class Descuentos {

    private $conexion;

    public function __construct($pdo) {

        $this->conexion = $pdo;
    }


    public function validateUpdateDescuentos() {
        try {
            date_default_timezone_set('America/Bogota'); // Ajusta la zona horaria a Bogotá, Colombia

            $fecha_actual = date('Y-m-d');
            
            $query = "  SELECT id_product, no_product, pre_finpro, pre_ventap, fec_fin_descu, desc_produ
                        FROM productos 
                        WHERE fec_fin_descu <= :fecha_actual AND desc_produ != 0";

            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':fecha_actual', $fecha_actual, PDO::PARAM_STR);
            $mysql->execute();

            $results = $mysql->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function updateDescuentosProductos($data) {
        try {
            $query = "  UPDATE productos
            SET
                desc_produ = 0,
                pre_ventades = :pre_ventap,
                ren_product = :ren_product
            WHERE id_product = :id_product";
            $updateStmt = $this->conexion->prepare($query);

            $costoFinalStr = $data['costoFinal'];
            $costoFinalSinComas = str_replace(',', '', $costoFinalStr);
            $costoFinalFloat = (float) $costoFinalSinComas;

            $preVentaStr = $data['precioVenta'];
            $precioSinComas = str_replace(',', '', $preVentaStr);
            $precioFloat = (float) $precioSinComas;

            $gananciaBruta = $precioFloat - $costoFinalFloat;
            $rentabilidadNeta = ($gananciaBruta / $costoFinalFloat) * 100;

            $updateStmt->bindValue(':id_product', $data['id'], PDO::PARAM_INT);
            $updateStmt->bindValue(':pre_ventap', $data['precioVenta'], PDO::PARAM_STR);
            $updateStmt->bindValue(':ren_product', $rentabilidadNeta);

            //Ejecutamos
            return $updateStmt->execute();
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}