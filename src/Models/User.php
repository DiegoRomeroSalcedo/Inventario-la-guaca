<?php

namespace Proyecto\Models;

use PDO;
use PDOException;

class User
{
    private $id;
    private $role_user;
    private $username;
    private $password;
    private $conexion;

    public function __construct($pdo)
    {
        $this->conexion = $pdo;
    }

    public function validateCredentials($username, $password)
    {
        // Realizamos la validacion del usuario contra la BD
        // Retorna tru si son válidas, false de lo contrario.
        try {
            $mysql = $this->conexion->prepare("SELECT `id_user`, contrasena, `role_id` FROM users WHERE username = :username");
            $mysql->bindParam(":username", $username);

            if ($mysql->execute()) {

                $user = $mysql->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['contrasena'])) {
                    $this->id = $user['id_user'];
                    $this->role_user = $user['role_id'];
                    return true;
                }
            }
            return false;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function updateUser($id_usuario, $username, $password)
    {
        try {
            $query = "  UPDATE users
                        SET
                            username = :username,
                            contrasena = :contrasena
                        WHERE
                            id_user = :id_user";
            $mysql = $this->conexion->prepare($query);
            $mysql->bindValue(':username', $username);
            $mysql->bindValue(':contrasena', $password);
            $mysql->bindValue(':id_user', $id_usuario);
            $mysql->execute();

            $success = "Se actualizo el usuario con éxito";

            header('Location: /update-user?message=' . $success);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRole()
    {
        return $this->role_user;
    }

    public function getDataUser()
    {
        try {
            $query = "  SELECT id_user, username, contrasena, role_id FROM users";
            $mysql = $this->conexion->prepare($query);
            $mysql->execute();
            return $mysql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
