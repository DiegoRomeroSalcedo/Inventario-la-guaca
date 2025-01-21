<?php

namespace Config;

use PDO;
use PDOException;

class ConfigConnect
{

    private static $instance = null; // Singleton
    private $dbHost;
    private $dbName;
    private $dbUser;
    private $dbPass;
    private $dsn;
    public $conn;

    /**
     * Contructor con configuración inyectada.
     * 
     * @param array $config
     */

    public function __construct(array $config)
    { //No pasamos parametros al constructor ya que el codigo sera el mismo, y estos se pasan cuando la clase es configurable o reutilizable

        $this->setConfigData($config); //Separamos la responsabilidad a un metodo que se encarga de validar y evaluar el JSON
        $this->dsn = "mysql:host={$this->dbHost};dbname={$this->dbName}";
    }

    /**
     * Singleton para obtener la instancia de conexión.
     * 
     * @param array $config
     * @return ConfigConnect
     */

    public static function getInstance(array $config)
    { // Uso del patron sigleton para obtener la instancia si esta existe

        if (!isset(self::$instance)) { //La variable no esta definida y es null?
            self::$instance = new ConfigConnect($config);
        }
        return self::$instance;
    }

    /**
     * Método para setear la configuración de la base de datos.
     * 
     * @param array $config
     */

    private function setConfigData(array $config)
    {
        $this->dbHost = $config['dbHost'];
        $this->dbName = $config['dbName'];
        $this->dbUser = $config['dbUser'];
        $this->dbPass = $config['dbPass'];
    }

    /**
     * Método para obtener la conexion PDO
     * 
     * @return PDO|null
     */

    public function getConnection()
    {
        $this->conn = null;
        $options = [
            PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT            => true,
            PDO::ATTR_CASE                  => PDO::CASE_NATURAL,
        ];

        try {
            $this->conn = new PDO($this->dsn, $this->dbUser, $this->dbPass, $options);
            // echo "Conexion extablecida Exitosamente";
        } catch (PDOException $e) {
            echo "Erro en la conexion a la BD: " . $e->getMessage();
        }

        return $this->conn;
    }
}
