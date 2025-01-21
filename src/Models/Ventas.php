<?php

namespace Proyecto\Models;

use PDO;
use PDOException;

class Ventas {

    private $conexion;

    public function __construct($pdo) {
        $this->conexion = $pdo;
    }

    public function getDataVentas() {

        try {
            $query = "  SELECT
                            a.id_venta,
                            b.id_factura,
                            d.no_product,
                            e.nombre_marca,
                            a.cantidad,
                            a.precio_unitario,
                            a.monto_venta,
                            b.total_venta,
                            c.identificacion,
                            c.Nombre,
                            b.usuario_insercion,
                            b.fecha
                        FROM ventas a
                        INNER JOIN facturas b ON a.id_factura = b.id_factura
                        INNER JOIN productos d ON a.id_producto = d.id_product
                        INNER JOIN marcas e ON d.id_marcapr = e.id_marca
                        LEFT JOIN clientes c ON b.id_cliente = c.id_cliente";

            $mysql = $this->conexion->prepare($query);
            $mysql->execute();

            return $mysql->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error : " . $e->getMessage();
        }
    }

    public function addFactura($total, $id_cliente, $totalRecibido, $valorDevuelto, $tipoPago) {
        $username = $_SESSION['username'];

        try {
            $sql = "INSERT INTO facturas (total_venta, valor_recibido, valor_devuelto, tipo_pago, id_cliente, usuario_insercion) VALUES (:total_venta, :valor_recibido, :valor_devuelto, :tipo_pago, :id_cliente, :usuario_insercion)";
            $mysql = $this->conexion->prepare($sql);
            $mysql->bindValue(':total_venta', $total);
            $mysql->bindValue(':valor_recibido', $totalRecibido);
            $mysql->bindValue(':valor_devuelto', $valorDevuelto);
            $mysql->bindValue(':tipo_pago', $tipoPago, PDO::PARAM_STR);
            $mysql->bindValue(':id_cliente', $id_cliente);
            $mysql->bindValue(':usuario_insercion', $username);
            $mysql->execute();

            return $this->conexion->lastInsertID();
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function addVenta($idFactura, $idProducto, $cantidad, $preUnitario, $montoTotal, $decuentoAplicado) {
        
        $username = $_SESSION['username'];

        try {
            $query = "INSERT INTO ventas (id_factura, id_producto, cantidad, precio_unitario, monto_venta, descuento_aplicado, usuario_insercion, fecha_insercion)
                        VALUES (:id_factura, :id_producto, :cantidad, :precio_unitario, :monto_venta, :descuento_aplicado, :usuario_insercion, CURRENT_TIMESTAMP)";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':id_factura', $idFactura, PDO::PARAM_INT);
            $mysql->bindValue(':id_producto', $idProducto, PDO::PARAM_INT);
            $mysql->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
            $mysql->bindValue(':precio_unitario', $preUnitario);
            $mysql->bindValue(':monto_venta', $montoTotal);
            $mysql->bindValue(':descuento_aplicado', $decuentoAplicado);
            $mysql->bindValue(':usuario_insercion', $username);

            $results = $mysql->execute();

            return $results;
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getDataGrafic($year, $start_date, $end_date) {

        try {
            $query = "  SELECT 
                            DATE_FORMAT(fecha, '%Y-%m') AS mes, 
                            SUM(total_venta) AS total
                        FROM
                            facturas
                        WHERE
                            fecha BETWEEN :start_date AND :end_date
                            GROUP BY DATE_FORMAT(fecha, '%Y-%m')
                            ORDER BY DATE_FORMAT(fecha, '%Y-%m')";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindParam(':start_date', $start_date);
            $mysql->bindParam(':end_date', $end_date);

            $mysql->execute();

            //Obtenemos los datos
            $salesData = $mysql->fetchAll(PDO::FETCH_ASSOC);

            return $salesData;
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}