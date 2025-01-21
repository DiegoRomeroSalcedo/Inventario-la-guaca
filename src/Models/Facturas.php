<?php

namespace Proyecto\Models;

use PDO;
use PDOException;

class Facturas {
    private $conexion;

    public function __construct($pdo) {
        $this->conexion = $pdo;
    }

    public function getFacturListData() {
        try {
            $query = "  SELECT
                            a.id_factura,
                            COUNT(b.id_factura) AS total_productos,
                            a.total_venta,
                            a.valor_recibido,
                            a.valor_devuelto,
                            a.tipo_pago,
                            c.id_cliente,
                            c.identificacion,
                            c.Nombre,
                            c.telefono,
                            a.fecha,
                            a.usuario_insercion
                        FROM
                            facturas a
                        INNER JOIN ventas b ON
                            a.id_factura = b.id_factura
                        LEFT JOIN clientes c ON
                            a.id_cliente = c.id_cliente
                        GROUP BY 
                            a.id_factura, 
                            a.total_venta,
                            a.valor_recibido,
                            a.valor_devuelto,
                            a.tipo_pago,
                            c.id_cliente,
                            c.identificacion,
                            c.Nombre,
                            c.telefono,
                            a.fecha,
                            a.usuario_insercion";
            $mysql = $this->conexion->prepare($query);
            $mysql->execute();
            return $mysql->fetchAll(PDO::FETCH_ASSOC);

            //Falta ajsutar  para que sirva el asincronismo
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getDetallesFactura($facturaid) {
        try {
            $query = "SELECT a.total_venta, a.valor_recibido, a.valor_devuelto, a.tipo_pago, b.id_producto, b.cantidad, b.precio_unitario, b.monto_venta, b.descuento_aplicado, c.no_product, e.nombre_marca, d.identificacion, d.Nombre, d.telefono, a.valor_recibido, a.valor_devuelto, a.tipo_pago
            FROM facturas a
            INNER JOIN ventas b ON a.id_factura = b.id_factura
            INNER JOIN productos c ON b.id_producto = c.id_product
            INNER JOIN marcas e ON c.id_marcapr =  e.id_marca
            LEFT JOIN clientes d ON a.id_cliente = d.id_cliente
            WHERE a.id_factura = :id_factura";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':id_factura', $facturaid, PDO::PARAM_INT);
            $mysql->execute();
            $results = $mysql->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch(PDOException $e) {
            echo json_encode(['error: ' => $e->getMessage()]);
        }
    }
}