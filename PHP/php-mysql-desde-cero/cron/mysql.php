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
    private $strSelect = "
        select
            id_resumen, nombre, categoria, precio, cantidad_vendidos, en_almacen, fecha_alta
        from resumen_productos
        where
            cantidad_vendidos > ?
        order by precio desc
        limit ?
        ;
    ";

    private $strSelectPDO = "
        select
            id_resumen, nombre, categoria, precio, cantidad_vendidos, en_almacen, fecha_alta
        from resumen_productos
        where
            cantidad_vendidos > :cantidad_vendidos
        order by precio desc
        limit :limit
        ;
    ";

    private $strUpdate = "
        update resumen_productos
        set
             nombre = ?
            ,categoria = ?
        where
            id_resumen = ?
    ";

    private $strUpdatePDO = "
        update resumen_productos
        set
             nombre = :nombre
            ,categoria = :categoria
        where
            id_resumen = :id_resumen
    ";

    private $strDelete = "
        delete from resumen_productos where id_resumen = ?
    ";

    private $strDeletePDO = "
        delete from resumen_productos where id_resumen = :id_resumen
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
                $pQuery->bind_param(
                    "ssdiis",
                    $valor["nombre"],
                    $valor["categoria"],
                    $valor["precio"],
                    $valor["cantidad_vendidos"],
                    $valor["en_almacen"],
                    $valor["fecha_alta"]
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

                mysqli_stmt_bind_param(
                    $pQuery,
                    "ssdiis",
                    $valor["nombre"],
                    $valor["categoria"],
                    $valor["precio"],
                    $valor["cantidad_vendidos"],
                    $valor["en_almacen"],
                    $valor["fecha_alta"]
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


    /**
     * Sintaxis OB
     * Selecciona un limite de datos segun el criterio
     */
    public function consultasOB()
    {
        $cantidad = 50;
        $noProductos = 2;
        if ($this->conBDOB()) {
            $pQuery = $this->oConBD->prepare($this->strSelect);
            $pQuery->bind_param("ii", $cantidad, $noProductos);
            $pQuery->execute();
            $productos = $pQuery->get_result();
            while ($producto = $productos->fetch_assoc()) {
                printf(
                    "id: %s, nombre: %s, categoría: %s, precio: %s, vendidos: %s, en almacen: %s, fecha: %s \n",
                    $producto["id_resumen"],
                    $producto["nombre"],
                    $producto["categoria"],
                    $producto["precio"],
                    $producto["cantidad_vendidos"],
                    $producto["en_almacen"],
                    $producto["fecha_alta"]
                );
            }
            $pQuery->close();
            $this->oConBD->close();
        }
    }

    /**
     * Sintaxis OB
     * Update
     */
    public function consultasOBU()
    {
        $id = 1;
        $nombreP = "producto modificado OB";
        $catP = "Categoria EWebik OB";

        if ($this->conBDOB()) {
            $pQuery = $this->oConBD->prepare($this->strUpdate);
            $pQuery->bind_param("ssi", $nombreP, $catP, $id);
            $pQuery->execute();
            $pQuery->close();
            $this->oConBD->close();
        }
    }

    /**
     * Sintaxis OB
     * DELETE
     */
    public function consultasOBD()
    {
        $id = 1;
        if ($this->conBDOB()) {
            $pQuery = $this->oConBD->prepare($this->strDelete);
            $pQuery->bind_param("i", $id);
            $pQuery->execute();
            $pQuery->close();
            $this->oConBD->close();
        }
    }
    /**
     * Sintaxis P
     * Selecciona un limite de datos segun el criterio
     */
    public function consultasP()
    {
        $cantidad = 50;
        $noProductos = 100;
        if ($this->conBDP()) {
            $pQuery = mysqli_stmt_init($this->oConBD);
            mysqli_stmt_prepare($pQuery, $this->strSelect);
            mysqli_stmt_bind_param($pQuery, "ii", $cantidad, $noProductos);
            mysqli_stmt_execute($pQuery);
            mysqli_stmt_bind_result($pQuery, $id_resumen, $nombre, $categoria, $precio, $cantidad_vendidos, $en_almacen, $fecha_alta);
            while (mysqli_stmt_fetch($pQuery)) {
                printf(
                    "id: %s, nombre: %s, categoría: %s, precio: %s, vendidos: %s, en almacen: %s, fecha: %s \n",
                    $id_resumen,
                    $nombre,
                    $categoria,
                    $precio,
                    $cantidad_vendidos,
                    $en_almacen,
                    $fecha_alta
                );
            }
            mysqli_stmt_close($pQuery);
            mysqli_close($this->oConBD);
        }
    }
    /**
     * Sintaxis P
     * Update
     */
    public function consultasPU()
    {
        $id = 1;
        $nombreP = "producto modificado P";
        $catP = "Categoria EWebik P";
        if ($this->conBDP()) {
            $pQuery = mysqli_stmt_init($this->oConBD);
            mysqli_stmt_prepare($pQuery, $this->strUpdate);
            mysqli_stmt_bind_param($pQuery, "ssi", $nombreP, $catP, $id);
            mysqli_stmt_execute($pQuery);
            mysqli_stmt_close($pQuery);
            mysqli_close($this->oConBD);
        }
    }
    /**
     * Sintaxis P
     * Update
     */
    public function consultasPD()
    {
        $id = 2;
        if ($this->conBDP()) {
            $pQuery = mysqli_stmt_init($this->oConBD);
            mysqli_stmt_prepare($pQuery, $this->strDelete);
            mysqli_stmt_bind_param($pQuery, "i", $id);
            mysqli_stmt_execute($pQuery);
            mysqli_stmt_close($pQuery);
            mysqli_close($this->oConBD);
        }
    }
    /**
     * Sintaxis PDO
     * Selecciona un limite de datos segun el criterio
     */
    public function consultasPDO()
    {
        $cantidad = 50;
        $noProductos = 100;
        try {
            if ($this->conBDPDO()) {
                $pQuery = $this->oConBD->prepare($this->strSelectPDO);
                $pQuery->bindValue(':cantidad_vendidos', $cantidad, PDO::PARAM_INT);
                $pQuery->bindValue(':limit', $noProductos, PDO::PARAM_INT);
                $pQuery->execute();
                $pQuery->setFetchMode(PDO::FETCH_ASSOC);
                while ($producto = $pQuery->fetch()) {
                    printf(
                        "id: %s, nombre: %s, categoría: %s, precio: %s, vendidos: %s, en almacen: %s, fecha: %s \n",
                        $producto["id_resumen"],
                        $producto["nombre"],
                        $producto["categoria"],
                        $producto["precio"],
                        $producto["cantidad_vendidos"],
                        $producto["en_almacen"],
                        $producto["fecha_alta"]
                    );
                }
                $this->oConBD = null;
            }
        } catch (PDOException $e) {
            echo ("MysSQL.consultasPDO -- " . $e->getMessage() . "\n");
        }
    }
    /**
     * Sintaxis PDO
     * Update
     */
    public function consultasPDOU()
    {
        $id = 1;
        $nombreP = "producto modificado PDO";
        $catP = "Categoria EWebik PDO";
        try {
            if ($this->conBDPDO()) {
                $pQuery = $this->oConBD->prepare($this->strUpdatePDO);
                $pQuery->bindValue(':nombre', $nombreP, PDO::PARAM_STR);
                $pQuery->bindValue(':categoria', $catP, PDO::PARAM_STR);
                $pQuery->bindValue(':id_resumen', $id, PDO::PARAM_INT);
                $pQuery->execute();
                $this->oConBD = null;
            }
        } catch (PDOException $e) {
            echo ("MysSQL.consultasPDOU -- " . $e->getMessage() . "\n");
        }
    }
    /**
     * Sintaxis PDO
     * Update
     */
    public function consultasPDOD()
    {
        $id = 3;
        try {
            if ($this->conBDPDO()) {
                $pQuery = $this->oConBD->prepare($this->strDeletePDO);
                $pQuery->bindValue(':id_resumen', $id, PDO::PARAM_INT);
                $pQuery->execute();
                $this->oConBD = null;
            }
        } catch (PDOException $e) {
            echo ("MysSQL.consultasPDOU -- " . $e->getMessage() . "\n");
        }
    }

    /**
     * Sintaxis Objetos
     * Executando Procedimiento almacenado
     */
    public function execSPOB()
    {
        if ($this->conBDOB()) {
            $pQuery = $this->oConBD->prepare(" call SP_INDICADORES(); ");
            $pQuery->execute();
            $indicadores = $pQuery->get_result();
            while ($indicador = $indicadores->fetch_assoc()) {
                printf(
                    "Productos totales: %s, Productos en almacen: %s, Ingresos totales: %s \n",
                    $indicador["PT"],
                    $indicador["PA"],
                    $indicador["IT"]

                );
            }
            $pQuery->close();
            $this->oConBD->close();
        }
    }

    /**
     * Sintaxis procedimientos
     * Executando Procedimiento almacenado
     */
    public function execSPP()
    {
        if ($this->conBDP()) {
            $pQuery = mysqli_stmt_init($this->oConBD);
            mysqli_stmt_prepare($pQuery, " call SP_INDICADORES(); ");
            mysqli_stmt_execute($pQuery);
            mysqli_stmt_bind_result($pQuery, $PT, $PA, $IT);
            while (mysqli_stmt_fetch($pQuery)) {
                printf(
                    "Productos totales: %s, Productos en almacen: %s, Ingresos totales: %s \n",
                    $PT,
                    $PA,
                    $IT
                );
            }
            mysqli_stmt_close($pQuery);
            mysqli_close($this->oConBD);
        }
    }

    /**
     * Sintaxis PDO
     * Executando Procedimiento almacenado
     */
    public function execSPPDO()
    {
        try {
            if ($this->conBDPDO()) {
                $pQuery = $this->oConBD->prepare(" call SP_INDICADORES(); ");
                $pQuery->execute();
                $pQuery->setFetchMode(PDO::FETCH_ASSOC);
                while ($indicador = $pQuery->fetch()) {
                    printf(
                        "Productos totales: %s, Productos en almacen: %s, Ingresos totales: %s \n",
                        $indicador["PT"],
                        $indicador["PA"],
                        $indicador["IT"]

                    );
                }
                $this->oConBD = null;
            }
        } catch (PDOException $e) {
            echo ("MysSQL.execSPPDO -- " . $e->getMessage() . "\n");
        }
    }

    /**
     * Sintaxis Objetos
     * Executando Procedimiento almacenado con parámetros
     */
    public function execSPParametrosOB()
    {
        $nombre = "zapatos";
        $categoria = "calzado";
        $precio = 500;
        $cantidad_vendidos = 20;
        $en_almacen = 30;
        $fecha_alta = "2020-01-30";
        if ($this->conBDOB()) {
            $pQuery = $this->oConBD->prepare(" call SP_INSERTAR_PRODUCTO(?,?,?,?,?,?); ");
            $pQuery->bind_param("ssdiis", $nombre, $categoria, $precio, $cantidad_vendidos, $en_almacen,  $fecha_alta);
            $pQuery->execute();
            $indicadores = $pQuery->get_result();
            while ($indicador = $indicadores->fetch_assoc()) {
                printf("Producto insertado con ID: %s \n", $indicador["ultimo_id"]);
            }
            $pQuery->close();
            $this->oConBD->close();
        }
    }

    /**
     * Sintaxis procedimientos
     * Executando Procedimiento almacenado con parámetros
     */
    public function execSPParametrosP()
    {
        $nombre = "zapatos procedimientos";
        $categoria = "calzado";
        $precio = 500;
        $cantidad_vendidos = 20;
        $en_almacen = 30;
        $fecha_alta = "2020-01-30";

        if ($this->conBDP()) {
            $pQuery = mysqli_stmt_init($this->oConBD);
            mysqli_stmt_prepare($pQuery, " call SP_INSERTAR_PRODUCTO(?,?,?,?,?,?); ");
            mysqli_stmt_bind_param($pQuery, "ssdiis", $nombre, $categoria, $precio, $cantidad_vendidos, $en_almacen,  $fecha_alta);
            mysqli_stmt_execute($pQuery);
            mysqli_stmt_bind_result($pQuery, $ultimo_id);
            while (mysqli_stmt_fetch($pQuery)) {
                printf("Producto insertado con ID: %s \n", $ultimo_id);
            }
            mysqli_stmt_close($pQuery);
            mysqli_close($this->oConBD);
        }
    }

    /**
     * Sintaxis PDO
     * Executando Procedimiento almacenado con parámetros
     */
    public function execSPParametrosPDO()
    {
        $nombre = "zapatos PDO";
        $categoria = "calzado";
        $precio = 500;
        $cantidad_vendidos = 20;
        $en_almacen = 30;
        $fecha_alta = "2020-01-30";
        try {
            if ($this->conBDPDO()) {
                $pQuery = $this->oConBD->prepare(" call SP_INSERTAR_PRODUCTO(:nombre,:categoria,:precio,:cantidad_vendidos,:en_almacen,:fecha_alta); ");
                $pQuery->bindParam(':nombre', $nombre);
                $pQuery->bindParam(':categoria', $categoria);
                $pQuery->bindParam(':precio', $precio);
                $pQuery->bindParam(':cantidad_vendidos', $cantidad_vendidos);
                $pQuery->bindParam(':en_almacen', $en_almacen);
                $pQuery->bindParam(':fecha_alta', $fecha_alta);
                $pQuery->execute();
                $pQuery->setFetchMode(PDO::FETCH_ASSOC);
                while ($indicador = $pQuery->fetch()) {
                    printf("Producto insertado con ID: %s \n", $indicador["ultimo_id"]);
                }
                $this->oConBD = null;
            }
        } catch (PDOException $e) {
            echo ("MysSQL.execSPParametrosPDO -- " . $e->getMessage() . "\n");
        }
    }


}
