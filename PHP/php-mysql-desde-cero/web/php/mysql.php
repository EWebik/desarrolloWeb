<?php
include '../../config.php';
class MySQL
{
    private $oConBD = null;

    public function __construct()
    {
        global $usuarioBD, $passBD, $ipBD, $nombreBD;
        $this->usuarioBD = $usuarioBD;
        $this->passBD = $passBD;
        $this->ipBD = $ipBD;
        $this->nombreBD = $nombreBD;
    }

    /**
     * Conexión BD por PDO
     */
    public function conBDPDO()
    {
        try {
            $this->oConBD = new PDO("mysql:host=" . $this->ipBD . ";dbname=" . $this->nombreBD, $this->usuarioBD, $this->passBD);
            return true;
        } catch (PDOException $e) {
            echo "Error al conectar a la base de datos: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function getVendidos()
    {
        $vendidos = 0;
        try {
            $strQuery = "SELECT SUM(cantidad_vendidos) as vendidos FROM resumen_productos";
            if ($this->conBDPDO()) {
                $pQuery = $this->oConBD->prepare($strQuery);
                $pQuery->execute();
                $vendidos = $pQuery->fetchColumn();
            }
        } catch (PDOException $e) {
            echo "MySQL.getVendidos: " . $e->getMessage() . "\n";
            return -1;
        }
        return $vendidos;

    }

    public function getAlmacen()
    {
        $almacen = 0;
        try {
            $strQuery = "SELECT SUM(en_almacen) as enAlmacen FROM resumen_productos";
            if ($this->conBDPDO()) {
                $pQuery = $this->oConBD->prepare($strQuery);
                $pQuery->execute();
                $almacen = $pQuery->fetchColumn();
            }
        } catch (PDOException $e) {
            echo "MySQL.getAlmacen: " . $e->getMessage() . "\n";
            return -1;
        }
        return $almacen;
    }
    public function getIngresos()
    {
        $ingreso = 0;
        try {
            $strQuery = "SELECT (SUM(precio) * SUM(cantidad_vendidos))/100000 as ingresos FROM resumen_productos";
            if ($this->conBDPDO()) {
                $pQuery = $this->oConBD->prepare($strQuery);
                $pQuery->execute();
                $ingreso = $pQuery->fetchColumn();
            }
        } catch (PDOException $e) {
            echo "MySQL.getIngresos: " . $e->getMessage() . "\n";
            return -1;
        }
        return $ingreso;
    }

    public function getDatosGrafica()
    {
        $jDatos = '';
        $rawdata = array();
        $i = 0;
        try {
            $strQuery = "SELECT sum(precio) as tPrecio, SUM(cantidad_vendidos) as tVendidos
            ,DATE_FORMAT(fecha_alta, '%Y-%m-%d') as fecha FROM resumen_productos GROUP BY DATE_FORMAT(fecha_alta, '%Y-%m-%d')";
            
            if ($this->conBDPDO()) {
                $pQuery = $this->oConBD->prepare($strQuery);
                $pQuery->execute();
                $pQuery->setFetchMode(PDO::FETCH_ASSOC);
                while($producto = $pQuery->fetch()) {
                    $oGrafica = new Grafica();
                    $oGrafica->totalPrecio = $producto['tPrecio'];
                    $oGrafica->totalVendidos = $producto['tVendidos'];
                    $oGrafica->fechaVenta = $producto['fecha'];
                    $rawdata[$i] = $oGrafica;
                    $i++;
                }
                $jDatos = json_encode( $rawdata);
            }
        } catch (PDOException $e) {
            echo "MySQL.getDatosGrafica: " . $e->getMessage() . "\n";
            return -1;
        }
        return $jDatos;
    }

}
class Grafica{
    public $totalVendidos = 0;
    public $totalPrecio = 0;
    public $fechaVenta = 0; 
}
