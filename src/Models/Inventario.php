<?php

namespace Proyecto\Models;

USE PDO;
use PDOException;

class Inventario {

    private $conexion;

    public function __construct($pdo) {
        $this->conexion = $pdo;
    }

    public function getUsrs() {
        try {
            $mysql = "SELECT id_user, nombre_usr FROM users WHERE 1 = 1 ORDER BY nombre_usr ASC";
            $mysql = $this->conexion->prepare($mysql);
            $mysql->execute();
            return $mysql->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error en la consulta: " . $e->getMessage();
        }
    }

    public function defaultData() {
        try {
            $mysql = "SELECT
                        b.id_product, b.no_product, a.id_marca, 
                        a.nombre_marca, c.cantidad, b.cost_produ, 
                        b.rte_fuente, b.flet_produ, b.iva_produc, 
                        b.pre_finpro, b.uti_produc, b.pre_ventap, 
                        b.desc_produ, b.pre_ventades, b.ren_product,
                        b.detalle_product,b.usuario_insercion,
                        b.feha_insercion,c.user_actual,c.fech_actual
                    FROM marcas a
                    INNER JOIN productos b ON a.id_marca = b.id_marcapr
                    LEFT JOIN cantidad_productos c ON b.id_product = c.id_producto
                    LEFT JOIN users d_insercion ON b.usuario_insercion = d_insercion.nombre_usr
                    LEFT JOIN users d_actualiza ON c.user_actual = d_actualiza.nombre_usr
                    ORDER BY c.fech_actual DESC LIMIT 200";
            $mysql = $this->conexion->prepare($mysql);
            $mysql->execute();
            return $mysql->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error al obtener los datos; " . $e->getMessage();
        }
    }

    public function searchData($data) {
        $marcaProd = $data[0];
        $idProduct = $data[1];
        $detalleMin = strtolower(trim($data[2])); // Convertir a minúsculas y eliminar espacios
        $det_replace = str_replace(' ', ',', $detalleMin); // Reemplazar espacios por comas
        $detalleftea = $det_replace ? '%' . $det_replace . '%' : null; // Construir el patrón LIKE
        $usrinsercion = $data[3];
        $usrActualiza = $data[4];
        $startDate = $data[5];
        $endDate = $data[6];
        
        try {
            // Construcción de la consulta SQL
            $query = "SELECT 
                b.id_product, b.no_product, a.id_marca, 
                a.nombre_marca, c.cantidad, b.cost_produ, 
                b.rte_fuente, b.flet_produ, b.iva_produc, 
                b.pre_finpro, b.uti_produc, b.pre_ventap, 
                b.desc_produ, b.pre_ventades, b.ren_product,
                b.detalle_product,b.usuario_insercion,
                b.feha_insercion,c.user_actual,c.fech_actual
                FROM marcas a
                INNER JOIN productos b ON a.id_marca = b.id_marcapr
                LEFT JOIN cantidad_productos c ON b.id_product = c.id_producto
                LEFT JOIN users d_insercion ON b.usuario_insercion = d_insercion.nombre_usr
                LEFT JOIN users d_actualiza ON c.user_actual = d_actualiza.nombre_usr
                WHERE 1 = 1";
        
            // Añadir condiciones basadas en los parámetros
            if ($marcaProd) {
                $query .= " AND a.id_marca = :id_marca";
            }
        
            if ($idProduct) {
                $query .= " AND b.id_product = :id_product";
            }
        
            if ($detalleftea) {
                $query .= " AND b.detalle_product LIKE :detalle_product";
            }
    
            if ($usrinsercion) {
                $query .= " AND d_insercion.id_user = :usrinsercion";
            }
    
            if ($usrActualiza) {
                $query .= " AND d_actualiza.id_user = :usrActualiza";
            }
        
            if ($startDate) {
                $query .= " AND c.fech_actual >= :startDate";
            }
        
            if ($endDate) {
                $query .= " AND c.fech_actual <= :endDate";
            }
        
            $query .= " ORDER BY c.fech_actual DESC";
        
            // Preparar la consulta
            $mysql = $this->conexion->prepare($query);
        
            // Asociar los parámetros
            if ($marcaProd) {
                $mysql->bindValue(':id_marca', $marcaProd, PDO::PARAM_INT);
            }
        
            if ($idProduct) {
                $mysql->bindValue(':id_product', $idProduct, PDO::PARAM_INT);
            }
        
            if ($detalleftea) {
                $mysql->bindValue(':detalle_product', $detalleftea, PDO::PARAM_STR);
            }
    
            if ($usrinsercion) {
                $mysql->bindValue(':usrinsercion', $usrinsercion, PDO::PARAM_INT);
            }
    
            if ($usrActualiza) {
                $mysql->bindValue(':usrActualiza', $usrActualiza, PDO::PARAM_INT);
            }
        
            if ($startDate) {
                $mysql->bindValue(':startDate', $startDate, PDO::PARAM_STR);
            }
        
            if ($endDate) {
                $mysql->bindValue(':endDate', $endDate, PDO::PARAM_STR);
            }
        
            // Ejecutar la consulta
            $mysql->execute();
    
            // Retornar los resultados
            return $mysql->fetchAll(PDO::FETCH_ASSOC);
        
        } catch (PDOException $e) {
            // Manejo de errores
            echo "Error en la consulta: " . $e->getMessage();
            return [];
        }
    }
    
}