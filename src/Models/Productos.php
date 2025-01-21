<?php

namespace Proyecto\Models;

use PDO;
use PDOException;

class Productos
{
    private $conexion;

    public function __construct($pdo)
    {
        $this->conexion = $pdo;
    }

    public function getAll()
    {

        try {
            $mysql = $this->conexion->prepare("SELECT *, b.cantidad FROM `productos` a LEFT JOIN cantidad_productos b ON a.id_product = b.id_producto ORDER BY `id_product` ASC");
            $mysql->execute();
            return $mysql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getIdNombre()
    {
        try {
            $mysql = $this->conexion->prepare("SELECT id_product, no_product FROM productos ORDER BY no_product ASC");
            $mysql->execute();
            return $mysql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function insertarProducto($datos)
    {

        try {

            $mysql = $this->conexion->prepare(
                "INSERT INTO `productos`(
                    id_product, no_product, id_marcapr, 
                    cost_produ, rte_fuente, flet_produ, 
                    iva_produc, pre_finpro, uti_produc, 
                    pre_ventap, descuento, desc_produ, pre_ventades, fec_fin_descu,
                    ren_product, detalle_product, usuario_insercion, feha_insercion,
                    usuario_actualizacion, feha_actualizacion
                ) VALUES (
                    :id_product, :no_product, :id_marcapr, 
                    :cost_produ, :rte_fuente, :flet_produ, 
                    :iva_produc, :pre_finpro, :uti_produc, 
                    :pre_ventap, :descuento, :desc_produ, :pre_ventades, :fec_fin_descu,
                    :ren_product, :detalle_product, :usuario_insercion, CURRENT_TIMESTAMP,
                    :usuario_actualizacion, CURRENT_TIMESTAMP
                )"
            );

            foreach ($datos as $key => $value) {
                //Determinamos el tipo de dato
                $type = PDO::PARAM_STR;
                if (is_int($value)) {
                    $type = PDO::PARAM_INT;
                } elseif (is_float($value)) {
                    $type = PDO::PARAM_STR; //pdo no tiene un tipo especifico para float, por eso coloco str
                }

                $mysql->bindValue($key, $value, $type);
            }

            $mysql->execute();

            // Obtenemos el id autoincrement
            $lastId = $this->conexion->lastInsertID();

            $mysql = $this->conexion->prepare("
                SELECT a.no_product, b.nombre_marca
                FROM productos a 
                INNER JOIN marcas b ON a.id_marcapr = b.id_marca
                WHERE a.id_product = :id_product
            ");
            $mysql->bindParam(":id_product", $lastId, PDO::PARAM_INT);
            $mysql->execute();

            $nombre = $mysql->fetch(PDO::FETCH_ASSOC);

            header("Location: /add-productos?id=" . $lastId . "&nombre=" . urlencode($nombre['no_product']) . "&marca=" . urlencode($nombre['nombre_marca']));
            exit();
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                $_SESSION['error'] = "El regitro ya existe";
                header("Location: /add-productos");
                exit();
            } else {
                $_SESSION['error_sql'] = "Error, porfavor intente luego";
                echo "Error: " . $e->getMessage();
                // header("Location: /add-productos");
                // exit();
            }
        }
    }

    public function getProductData($id_producto, $id_marca)
    {

        try {

            $query = "";
            $query .= "SELECT 
                        *, b.nombre_marca, c.cantidad
                        FROM productos a
                        INNER JOIN marcas b ON a.id_marcapr = b.id_marca
                        LEFT JOIN cantidad_productos c ON a.id_product = c.id_producto";

            $conditions = [];
            if ($id_producto > 0) {
                $conditions[] = "id_product = :id_product";
            }

            if ($id_marca > 0) {
                $conditions[] = "id_marcapr = :id_marcapr";
            }

            if (!empty($conditions)) {
                $query .= " WHERE " . implode(" AND ", $conditions);
            }

            $mysql = $this->conexion->prepare($query);

            if ($id_producto > 0) {
                $mysql->bindValue(':id_product', $id_producto);
            }

            if ($id_marca > 0) {
                $mysql->bindValue(':id_marcapr', $id_marca);
            }

            $mysql->execute();

            // $mysql->debugDumpParams(); //Depuramos los paraemtros asi se ve la consulta como se esta ejecutando
            // die("Hola");
            $lastId = $this->conexion->lastInsertID();
            $results = $mysql->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $e) {
            echo "Error en la consulta: " . $e->getMessage();
        }
    }

    public function insertCantidadProducto($data, $encriptado)
    {

        try {

            $checkQuery = "SELECT COUNT(*) AS count FROM cantidad_productos WHERE id_producto = :id_producto";
            $checkQuery = $this->conexion->prepare($checkQuery);
            $checkQuery->bindValue(':id_producto', $data[':id_producto'], PDO::PARAM_INT);
            $checkQuery->execute();
            $results = $checkQuery->fetch(PDO::FETCH_ASSOC);
            $exists = isset($results['count']) && $results['count'] > 0;

            if (!$exists) {

                $mysql = $this->conexion->prepare(
                    "
                    INSERT INTO cantidad_productos (id_producto, cantidad, user_insert, fech_insert, user_actual, fech_actual) VALUES (:id_producto, :cantidad, :user_insert, CURRENT_TIMESTAMP, :user_actual, CURRENT_TIMESTAMP)
                    "
                );

                foreach ($data as $key => $value) {

                    if (is_int($value)) {
                        $type = PDO::PARAM_INT;
                    } else {
                        $type = PDO::PARAM_STR;
                    }

                    $mysql->bindValue($key, $value, $type);
                }

                $mysql->execute();
                $insertado = "insertado";

                header("Location: /get-add-cantidades?data=" . $encriptado . "&tipo=" . urlencode($insertado));
                exit();
            } else {
                $mysql = "UPDATE cantidad_productos SET cantidad = cantidad + :cantidad, user_actual = :user_actual WHERE id_producto = :id_producto";
                $mysql = $this->conexion->prepare($mysql);
                $mysql->bindParam(':id_producto', $data[':id_producto'], PDO::PARAM_INT);
                $mysql->bindParam(':cantidad', $data[':cantidad'], PDO::PARAM_INT);
                $mysql->bindParam(':user_actual', $data[':user_actual'], PDO::PARAM_STR);
                $mysql->execute();

                $mysql = "SELECT cantidad FROM cantidad_productos WHERE id_producto = :id_producto";
                $mysql = $this->conexion->prepare($mysql);
                $mysql->bindParam(':id_producto', $data[':id_producto'], PDO::PARAM_INT);
                $mysql->execute();
                $result = $mysql->fetch();

                $actualizado = "actualizado";
                header("Location: /get-add-cantidades?data=" . $encriptado . "&tipo=" . $actualizado . '&cantidad=' . $result['cantidad']);
                exit();
            }
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                $_SESSION['error'] = "El regitro ya existe";
                header("Location: /get-add-cantidades");
                exit();
            } else {
                $_SESSION['error_sql'] = "Error, porfavor intente luego";
                header("Location: /get-add-cantidades");
                exit();
            }
        }
    }

    public function searchUpdateProducto($id, $id_marca)
    {
        try {
            $mysql = "SELECT 
                        a.id_marca, a.nombre_marca,
                        b.id_product, b.no_product,
                        b.cost_produ, b.rte_fuente,
                        b.flet_produ, b.iva_produc,
                        b.pre_finpro, b.uti_produc,
                        b.pre_ventap, b.fec_fin_descu, b.desc_produ,
                        b.pre_ventades, b.ren_product,
                        b.detalle_product, c.cantidad,
                        c.fech_actual
                    FROM marcas a
                    INNER JOIN productos b ON a.id_marca = b.id_marcapr
                    LEFT JOIN cantidad_productos c ON b.id_product = c.id_producto
                    WHERE 1 = 1";

            if ($id > 0) {
                $mysql .= " AND b.id_product = :id_product";  // Corregido
            }

            if ($id_marca > 0) {
                $mysql .= " AND a.id_marca = :id_marca";
            }

            $mysql .= " ORDER BY b.id_product ASC";

            $mysql = $this->conexion->prepare($mysql);

            if ($id > 0) {
                $mysql->bindValue(':id_product', $id, PDO::PARAM_INT);
            }

            if ($id_marca > 0) {
                $mysql->bindValue(':id_marca', $id_marca, PDO::PARAM_INT);
            }

            $mysql->execute();

            return $mysql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Manejo de errores aquí
            echo "Error: " . $e->getMessage();
        }
    }

    public function updateProducts($data, $encrypted)
    {
        $usr_actualizacion = $_SESSION['username'];
        try {
            $mysql = "  UPDATE productos a 
                        LEFT JOIN cantidad_productos b ON a.id_product = b.id_producto
                        SET 
                        a.no_product = :nom_produc,
                        a.id_marcapr = :id_marcapr,
                        a.cost_produ = :cost_produ,
                        a.rte_fuente = :rte_fuente,
                        a.flet_produ = :flet_produ,
                        a.iva_produc = :iva_produc,
                        a.pre_finpro = :pre_finpro,
                        a.uti_produc = :uti_produc,
                        a.pre_ventap = :pre_ventap,
                        a.desc_produ = :desc_produ,
                        a.pre_ventades = :pre_ventades,
                        a.fec_fin_descu = :fec_fin_descu,
                        a.ren_product = :ren_product,
                        a.detalle_product = :detalle_product,
                        a.usuario_actualizacion = :usuario_actualizacion,
                        a.feha_actualizacion = CURRENT_TIMESTAMP,
                        b.cantidad = :cantidad,
                        b.user_actual = :user_actual,
                        b.fech_actual = CURRENT_TIMESTAMP
                        WHERE a.id_product = :id_product";
            $mysql = $this->conexion->prepare($mysql);
            $mysql->bindValue(':nom_produc', $data['nom_produc'], PDO::PARAM_STR);
            $mysql->bindValue(':id_marcapr', $data['marca_producto'], PDO::PARAM_INT);
            $mysql->bindValue(':cost_produ', $data['cost_produ'], PDO::PARAM_STR);
            $mysql->bindValue(':rte_fuente', $data['porc_rete'], PDO::PARAM_STR);
            $mysql->bindValue(':flet_produ', $data['porc_flete'], PDO::PARAM_STR);
            $mysql->bindValue(':iva_produc', $data['porc_iva'], PDO::PARAM_STR);
            $mysql->bindValue(':pre_finpro', $data['pre_finpro'], PDO::PARAM_STR);
            $mysql->bindValue(':uti_produc', $data['uti_product'], PDO::PARAM_STR);
            $mysql->bindValue(':pre_ventap', $data['pre_ventap'], PDO::PARAM_STR);
            $mysql->bindValue(':desc_produ', $data['des_product'], PDO::PARAM_STR);
            $mysql->bindValue(':pre_ventades', $data['pre_ventades'], PDO::PARAM_STR);
            $mysql->bindValue(':fec_fin_descu', $data['fec_fin_descu']);
            $mysql->bindValue(':ren_product', $data['rentabilidad'], PDO::PARAM_STR);
            $mysql->bindValue(':detalle_product', $data['detalle_produc'], PDO::PARAM_STR);
            $mysql->bindValue(':cantidad', $data['cantidad'], PDO::PARAM_STR);
            $mysql->bindValue(':usuario_actualizacion', $usr_actualizacion, PDO::PARAM_STR);
            $mysql->bindValue(':user_actual', $usr_actualizacion, PDO::PARAM_STR);
            $mysql->bindValue(':id_product', $data['id_product'], PDO::PARAM_INT);

            $mysql->execute();

            header('Location: /search-update-productos?data=' . urlencode($encrypted));
            exit();
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                $_SESSION['error'] = "El regitro ya existe";
                header("Location: /search-update-productos");
                exit();
            } else {
                $_SESSION['error_sql'] = "Error, porfavor intente luego";
                header("Location: /search-update-productos");
                exit();
            }
        }
    }


    public function serchVentaProducto($id_producto)
    {
        try {
            $mysql = "  SELECT
                        a.id_product, a.no_product, c.nombre_marca, a.pre_ventap, a.desc_produ, a.pre_ventades, a.detalle_product, b.cantidad
                        FROM productos a
                        LEFT JOIN cantidad_productos b ON a.id_product = b.id_producto
                        INNER JOIN marcas c ON a.id_marcapr = c.id_marca
                        WHERE a.id_product = :id_product";
            $mysql = $this->conexion->prepare($mysql);
            $mysql->bindValue(':id_product', $id_producto);
            $mysql->execute();

            $results = $mysql->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage();
        }
    }

    public function getCantidadStock($id_producto)
    {
        try {
            $query = "SELECT cantidad FROM cantidad_productos WHERE id_producto = :id_producto";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
            $mysql->execute();

            $results = $mysql->fetch(PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $e) {
            echo "Error en al consulta: " . $e->getMessage();
        }
    }

    public function updateCantidadVenta($cantidad, $id_producto)
    {

        $username = $_SESSION['username'];
        try {
            $query = "UPDATE
                            cantidad_productos
                        SET
                            cantidad = :cantidad,
                            user_actual = :user_actual,
                            fech_actual = CURRENT_TIMESTAMP 
                        WHERE
                            id_producto = :id_producto";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
            $mysql->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
            $mysql->bindValue(':user_actual', $username);

            return $mysql->execute();
        } catch (PDOException $e) {
            echo "Error al actualizar la cantidad: " . $e->getMessage();
        }
    }

    public function updateCantidadDevolucion($cantidad, $idProducto)
    {
        try {
            $query = "  UPDATE cantidad_productos
                        SET
                            cantidad = cantidad + :cantidad
                        WHERE 
                            id_producto = :id_producto";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':cantidad', $cantidad);
            $mysql->bindValue(':id_producto', $idProducto);

            return $mysql->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
