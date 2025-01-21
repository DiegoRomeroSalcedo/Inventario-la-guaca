<?php

namespace Proyecto\Models;

use PDO;
use PDOException;

class Devolucion {

    private $conexion;

    public function __construct($pdo) {
        $this->conexion = $pdo;
    }

    //Este metodo se emplea para validar si un producto de una fcatura en especifico ya fue insertado y de esta manera comprobar si se debe insrtar o actualziar
    public function existfactur($id_factura){
        try {
            $query = "  SELECT id_factura, id_devolucion
                        FROM devoluciones 
                        WHERE id_factura = :id_factura";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':id_factura', $id_factura);
            $mysql->execute();

            $results = $mysql->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function validateCantidadProductos($id_devolucion, $id_producto) {
        try {
            $query = "  SELECT cantidad FROM detalle_devolucion WHERE id_devolucion = :id_devolucion AND id_producto = :id_producto";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue('id_devolucion', $id_devolucion);
            $mysql->bindValue(':id_producto', $id_producto);

            $mysql->execute();

            $result = $mysql->fetchAll(PDO::FETCH_ASSOC);

            return $result[0]['cantidad'] ? $result[0]['cantidad'] : 0;
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return 0;
        }
    }

    public function validateTotalDevolucion($id_devolucion) {
        try{
            $query = "  SELECT total FROM devoluciones WHERE id_devolucion = :id_devolucion";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':id_devolucion', $id_devolucion);
            $mysql->execute();

            $result = $mysql->fetchAll(PDO::FETCH_ASSOC);

            return $result[0]['total'] ? $result[0]['total'] : 0;
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function updateDevolucion($data, $id_devolucion) {
        try {
            $query = "  UPDATE devoluciones
                        SET
                            total = total + :total
                        WHERE id_devolucion = :id_devolucion AND id_factura = :id_factura";

        $mysql = $this->conexion->prepare($query);
        $mysql->bindValue(':total', $data['total']);
        $mysql->bindValue(':id_devolucion', $id_devolucion);
        $mysql->bindValue(':id_factura', $data['id_factura'], PDO::PARAM_INT);
        $mysql->execute();
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function updateDetalleDevolucion($id_devolucion, $id_producto, $cantidad) {
        try {
            $query = "  UPDATE detalle_devolucion
                        SET
                            cantidad = cantidad + :cantidad,
                            subtotal = precio_unitario * cantidad
                        WHERE 
                            id_devolucion = :id_devolucion AND
                            id_producto = :id_producto";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':cantidad', $cantidad);
            $mysql->bindValue(':id_devolucion', $id_devolucion);
            $mysql->bindValue(':id_producto', $id_producto);
            $mysql->execute();
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function addDevolucion($data){
        $idFactura = $data['id_factura'];
        $motivo = $data['motivo'];
        $total = $data['total'];
        $username = $_SESSION['username'];
        try {
            $query = "  INSERT INTO devoluciones(
                            id_factura,
                            fecha,
                            motivo,
                            total,  
                            usuario_insercion
                        ) VALUES(
                        :id_factura,
                        CURRENT_TIMESTAMP,
                        :motivo,
                        :total,
                        :usuario_insercion)";

            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':id_factura', $idFactura);
            $mysql->bindValue(':motivo', $motivo);
            $mysql->bindValue(':total', $total);
            $mysql->bindValue(':usuario_insercion', $username, PDO::PARAM_STR);
            $mysql->execute();

            $result = $this->conexion->lastInsertID();

            return $result;
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function addDetalleDevolucion($devolucionId, $dataProducto){
        $id_devolucion = (int) $devolucionId;
        $id_Producto = (int) $dataProducto['id_producto'];
        $cantidad = (int) $dataProducto['cantidad'];
        $precio_unitario = (float) $dataProducto['precioUnitario'];
        $subtotal = (float) $dataProducto['montoDevolucion'];

        try {
            $query = "  INSERT INTO detalle_devolucion(
                            id_devolucion,
                            id_producto,
                            cantidad,
                            precio_unitario,
                            subtotal
                        ) VALUES(
                            :id_devolucion,
                            :id_producto,
                            :cantidad,
                            :precio_unitario,
                            :subtotal
                        )";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':id_devolucion', $id_devolucion, PDO::PARAM_INT);
            $mysql->bindValue(':id_producto', $id_Producto, PDO::PARAM_INT);
            $mysql->bindValue(':cantidad', $cantidad);
            $mysql->bindValue(':precio_unitario', $precio_unitario);
            $mysql->bindValue(':subtotal', $subtotal);

            return $mysql->execute();
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getDataDevolucion() {

        try{
            $query = "  SELECT
                            a.id_factura,
                            a.motivo,
                            a.total,
                            a.usuario_insercion,
                            b.id_detalle,
                            b.cantidad,
                            b.precio_unitario,
                            b.subtotal,
                            c.no_product,
                            d.nombre_marca,
                            f.identificacion,
                            f.Nombre
                        FROM devoluciones a
                        INNER JOIN detalle_devolucion b ON a.id_devolucion = b.id_devolucion
                        INNER JOIN productos c ON b.id_producto = c.id_product
                        INNER JOIN marcas d ON c.id_marcapr = d.id_marca
                        INNER JOIN facturas e ON a.id_factura = e.id_factura
                        INNER JOIN clientes f ON e.id_cliente = f.id_cliente
                        ORDER BY
                            a.id_factura,
                            a.motivo,
                            a.total,
                            a.usuario_insercion,
                            b.id_detalle,
                            b.cantidad,
                            b.precio_unitario,
                            b.subtotal,
                            c.no_product,
                            d.nombre_marca";
            $mysql = $this->conexion->prepare($query);
            $mysql->execute();  
            return $mysql->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}