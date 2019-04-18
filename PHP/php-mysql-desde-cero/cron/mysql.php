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

    public function __construct()
    {
        global $usuarioBD, $passBD, $ipBD;

        $this->usuarioBD = $usuarioBD;
        $this->passBD = $passBD;
        $this->ipBD = $ipBD;
    }

    /**
     * Conexión BD por Objetos
     */
    public function conBDOB()
    {
        $this->oConBD = new mysqli($this->ipBD, $this->usuarioBD, $this->passBD);
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
        $this->oConBD = mysqli_connect($this->ipBD, $this->usuarioBD, $this->passBD);
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
            $this->oConBD = new PDO("mysql:host=" . $this->ipBD, $this->usuarioBD, $this->passBD);
            echo "Conexión exitosa..." . "\n";
            return true;
        } catch (PDOException $e) {
            echo "Error al conectar a la base de datos: " . $e->getMessage() . "\n";
            return false;
        }
    }
}
