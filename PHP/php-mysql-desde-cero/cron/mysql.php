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

  
    public $strInsert_old = "
		insert into resumen_productos
			(nombre,categoria,precio,cantidad_vendidos,en_almacen,fecha_alta)
		values
			('producto-1','categoria-2', 199.00, 30, 100,'2019-01-01')
        ";

    public $strInsert = "
    insert into resumen_productos
        (nombre,categoria,precio,cantidad_vendidos,en_almacen,fecha_alta)
    values
        (?,?,?,?,?,?)
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
        $id;
        if ($this->conBDOB() && $query != '') {
            if ($this->oConBD->query($query) === true) {
                $id = $this->oConBD->insert_id;
                echo "Consulta ejecutada \n";
            } else {
                echo "Error al ejecutar consulta " . $this->oConBD->error . "\n";
            }
            $this->oConBD->close();
        }
        return $id;
    }

    /**
     * Ejecuta un query con la sintaxis Procedimiento
     */
    public function execStrQueryP($query)
    {
        $id;
        if ($this->conBDP() && $query != '') {
            if (mysqli_query($this->oConBD, $query)) {
                $id = $this->oConBD->insert_id;
                echo "Consulta ejecutada \n";
            } else {
                echo "Error al ejecutar consulta " . mysqli_error($this->oConBD) . "\n";
            }
            mysqli_close($this->oConBD);
        }
        return $id;
    }
    /**
     * Ejecuta un query con la sintaxis PDO
     */
    public function execStrQueryPDO($query)
    {
        try {
            $id;
            if ($this->conBDPDO() && $query != '') {
                $this->oConBD->exec($query);
                $id = $this->oConBD->lastInsertId();
                echo "Consulta ejecutada \n";
                return $id;
            }
        } catch (PDOException $e) {
            echo "MySQL.execStrQueryPDO --Error-- " . $e->getMessage() . "\n";
        }
    }

    
    /**
     * Sintaxis Objetos
     * file_get_contents - permite leer un json
     * json_decode - Convierte un string en un JSON
     */
    public function insertarOB()
    {
        $json = file_get_contents('./datos.json');
        $jsonDatos = json_decode($json, true);
        //print_r($jsonDatos);
        if ($this->conBDOB()) {
            //echo ($this->strInsert) ;
            //Disminuye el riesgo de inyección sql
            $pQuery = $this->oConBD->prepare($this->strInsert);
            foreach ($jsonDatos as $id => $valor) {
                $pQuery->bind_param("ssdiis"
                    , $valor["nombre"]
                    , $valor["categoria"]
                    , $valor["precio"]
                    , $valor["cantidad_vendidos"]
                    , $valor["en_almacen"]
                    , $valor["fecha_alta"]
                );
                $pQuery->execute();
                //comprobando insert recibiendo el ultimo ID
                $idInsertado = $this->oConBD->insert_id;
                echo ("Nombre: " . $valor["nombre"] . ", Ultimo ID: " . $idInsertado . "\n");
            }
            $pQuery->close();
            $this->oConBD->close();
        }
    }

    /**
     * Sintaxis procedimientos
     */
    public function insertarP()
    {
        $json = file_get_contents('./datos.json');
        $jsonDatos = json_decode($json, true);

        if ($this->conBDP()) {
            $pQuery = mysqli_stmt_init($this->oConBD);
            mysqli_stmt_prepare($pQuery, $this->strInsert);

            foreach ($jsonDatos as $id => $valor) {

                mysqli_stmt_bind_param($pQuery, "ssdiis"
                    , $valor["nombre"]
                    , $valor["categoria"]
                    , $valor["precio"]
                    , $valor["cantidad_vendidos"]
                    , $valor["en_almacen"]
                    , $valor["fecha_alta"]
                );
                mysqli_stmt_execute($pQuery);
                $idInsertado = $this->oConBD->insert_id;
                echo ("Nombre: " . $valor["nombre"] . ", Ultimo ID: " . $idInsertado . "\n");
            }
            mysqli_close($this->oConBD);
        }
    }

    /**
     * Sintaxis PDO
     */
    public function insertarPDO()
    {
        $json = file_get_contents('./datos.json');
        $jsonDatos = json_decode($json, true);
        try {
            $this->strInsert = "
            insert into resumen_productos
                (nombre,categoria,precio,cantidad_vendidos,en_almacen,fecha_alta)
            values
                (:nombre,:categoria,:precio,:cantidad_vendidos,:en_almacen,:fecha_alta)
            ";
            if ($this->conBDPDO()) {
                $pQuery = $this->oConBD->prepare($this->strInsert);
                foreach ($jsonDatos as $id => $valor) {
                    $pQuery->bindParam(':nombre', $valor["nombre"]);
                    $pQuery->bindParam(':categoria', $valor["categoria"]);
                    $pQuery->bindParam(':precio', $valor["precio"]);
                    $pQuery->bindParam(':cantidad_vendidos', $valor["cantidad_vendidos"]);
                    $pQuery->bindParam(':en_almacen', $valor["en_almacen"]);
                    $pQuery->bindParam(':fecha_alta', $valor["fecha_alta"]);
                    $pQuery->execute();
                    $idInsertado = $this->oConBD->lastInsertId();
                    echo ("Nombre: " . $valor["nombre"] . ", Ultimo ID: " . $idInsertado . "\n");
                }
                $this->oConBD = null;
            }
        } catch (PDOException $e) {
            echo ("MysSQL.insertarPDO -- " . $e->getMessage() . "\n");
        }

    }

}
