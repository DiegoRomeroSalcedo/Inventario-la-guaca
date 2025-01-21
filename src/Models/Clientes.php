<?php

namespace Proyecto\Models;
use PDO;
use PDOException;

class Clientes {
    private $conexion;

    public function __construct($pdo) {
        $this->conexion = $pdo;
    }

    public function getDataAll() {
        try {
            $query = "  SELECT
                            a.id_cliente,
                            a.identificacion,
                            a.Nombre,
                            a.telefono,
                            a.email,
                            a.direccion,
                            a.cantidad_compras,
                            COALESCE(SUM(b.total_venta), 0) AS total_compras,
                            a.fecha_ultima_compra,
                            a.devoluciones,
                            a.ultima_devolucion,
                            a.fecha_registro
                            FROM
                                clientes a
                            LEFT JOIN facturas b ON a.id_cliente = b.id_cliente
                            GROUP BY
                                a.id_cliente,
                                a.identificacion,
                                a.Nombre,
                                a.telefono,
                                a.email,
                                a.direccion,
                                a.cantidad_compras,
                                a.fecha_ultima_compra,
                                a.devoluciones,
                                a.ultima_devolucion,
                                a.fecha_registro";
            $mysql = $this->conexion->prepare($query);
            $mysql->execute();

            return $mysql->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getClienteCedula($cedula) {

        try {
            $query = "SELECT id_cliente, identificacion, cantidad_compras FROM clientes WHERE identificacion = :identificacion";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':identificacion', $cedula);
            $mysql->execute();
            $result = $mysql->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function insertCliente($cedulaCliente, $nomCliente, $nroCelular, $emailCliente, $dirCliente) {
        
        $cantidadCompras = 1;
        $username = $_SESSION['username'];
        try {
            $query = "  INSERT INTO clientes(
                            identificacion,
                            Nombre,
                            telefono,
                            email,
                            direccion,
                            cantidad_compras,
                            fecha_ultima_compra,
                            usr_regitro,
                            fecha_registro
                        )
                        VALUES(
                            :identificacion,
                            :Nombre,
                            :telefono,
                            :email,
                            :direccion,
                            :cantidad_compras,
                            CURRENT_TIMESTAMP,
                            :usr_regitro,
                            CURRENT_TIMESTAMP
                        )";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':identificacion', $cedulaCliente, PDO::PARAM_STR);
            $mysql->bindValue(':Nombre', $nomCliente, PDO::PARAM_STR);
            $mysql->bindValue(':telefono', $nroCelular, PDO::PARAM_STR);
            $mysql->bindValue(':email', $emailCliente);
            $mysql->bindValue(':direccion', $dirCliente);
            $mysql->bindValue(':cantidad_compras', $cantidadCompras);
            $mysql->bindValue(':usr_regitro', $username);
            $mysql->execute();

            $id_cliente = $this->conexion->lastInsertID();
            return $id_cliente;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function updateCompras($compra, $identificacion) {
        try {
            $query = "UPDATE clientes
                        SET cantidad_compras = :cantidad_compras
                        WHERE identificacion = :identificacion";

            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':identificacion', $identificacion);
            $mysql->bindValue(':cantidad_compras', $compra);
            $mysql->execute();
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function updateDevoluciones($identificacion) {
        date_default_timezone_set('America/Bogota'); // Ajusta la zona horaria a Bogotá, Colombia

        $cedula = (string) $identificacion;
        $fecha_actual = date('Y-m-d');

        try {
            $query = "  UPDATE clientes
                        SET
                            devoluciones = COALESCE(devoluciones, 0) + 1,
                            ultima_devolucion = CURRENT_TIMESTAMP
                        WHERE identificacion = :identificacion";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':identificacion', $cedula, PDO::PARAM_STR);
            $mysql->execute();
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getDataCliente($cedula) {
        try {
            $query = "  SELECT identificacion, Nombre, telefono, email, direccion FROM clientes WHERE identificacion = :identificacion";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':identificacion', $cedula);
            $mysql->execute();

            return $mysql->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

}