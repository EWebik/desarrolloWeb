<?php
/**
 * Existen 3 maneras de trabajar PHP con MySQL
 * Orientada a objetos (OB)
 * Procedimiento (P)
 * PDO
 */

include '../config.php';

class MySQL
{
    private $oConBD = null;

    public $sqlCDB = "CREATE DATABASE db_php_mysql";

    public $sqlTabla = "
        CREATE TABLE resumen_productos (
            id_resumen                  INT(11)     UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nombre                      VARCHAR(45) NOT NULL,
            categoria                   VARCHAR(45) NOT NULL,
            precio                      FLOAT       NOT NULL,
            cantidad_vendidos           INT(11)     NOT NULL,
            en_almacen                  INT(11)     NOT NULL,
            fecha_alta                  datetime    NOT NULL
        )
    ";

    public function __construct()
    {
        global $usuarioBD, $passBD, $ipBD, $nombreBD;

        $this->usuarioBD = $usuarioBD;
        $this->passBD = $passBD;
        $this->ipBD = $ipBD;
        $this->nombreBD = $nombreBD;
    }

    /**
     * Conexión BD por Objetos
     */
    public function conBDOB()
    {
        $this->oConBD = new mysqli($this->ipBD, $this->usuarioBD, $this->passBD, $this->nombreBD);
        if ($this->oConBD->connect_error) {
            echo "Error al conectar a la base de datos: " . $this->oConBD->connect_error . "\n";
            return false;
        }
        echo "Conexión exitosa..." . "\n";
        return true;
    }

    /**
     * Conexión BD por Procedimientos
     */
    public function conBDP()
    {
        $this->oConBD = mysqli_connect($this->ipBD, $this->usuarioBD, $this->passBD, $this->nombreBD);
        if (!$this->oConBD) {
            echo "Error al conectar a la base de datos: " . mysqli_connect_error() . "\n";
            return false;
        }
        echo "Conexión exitosa..." . "\n";
        return true;
    }

    /**
     * Conexión BD por PDO
     */
    public function conBDPDO()
    {
        try {
            $this->oConBD = new PDO("mysql:host=" . $this->ipBD . ";dbname=" . $this->nombreBD, $this->usuarioBD, $this->passBD);
            echo "Conexión exitosa..." . "\n";
            return true;
        } catch (PDOException $e) {
            echo "Error al conectar a la base de datos: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Ejecuta un query con la sintaxis Objetos
     */
    public function execStrQueryOB($query)
    {
        if ($this->conBDOB() && $query != '') {
            if ($this->oConBD->query($query) === true) {
                echo "Consulta ejecutada \n";
            } else {
                echo "Error al ejecutar consulta " . $this->oConBD->error . "\n";
            }
            $this->oConBD->close();
        }
    }

    /**
     * Ejecuta un query con la sintaxis Procedimiento
     */
    public function execStrQueryP($query)
    {
        if ($this->conBDP() && $query != '') {
            if (mysqli_query($this->oConBD, $query)) {
                echo "Consulta ejecutada \n";
            } else {
                echo "Error al ejecutar consulta " . mysqli_error($this->oConBD) . "\n";
            }
            mysqli_close($this->oConBD);
        }
    }
    /**
     * Ejecuta un query con la sintaxis PDO
     */
    public function execStrQueryPDO($query)
    {
        try {
            if ($this->conBDPDO() && $query != '') {
                $this->oConBD->exec($query);
                echo "Consulta ejecutada \n";
            }
        } catch (PDOException $e) {
            echo "MySQL.execStrQueryPDO --Error-- " . $e->getMessage() . "\n";
        }
    }
}
