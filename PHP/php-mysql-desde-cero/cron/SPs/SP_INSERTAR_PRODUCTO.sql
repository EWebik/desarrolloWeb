DELIMITER $$ -- CAMBIANDO EL DELIMITADOR POR "$$"
DROP PROCEDURE IF EXISTS `SP_INSERTAR_PRODUCTO` $$
CREATE PROCEDURE `SP_INSERTAR_PRODUCTO`(
`_nombre`						 VARCHAR(45),
`_categoria`					 VARCHAR(45),
`_precio`						 FLOAT,
`_cantidad_vendidos`			 INT,
`_en_almacen`					 VARCHAR(45),
`_fecha_alta`					 DATETIME		
)
BEGIN
   
   INSERT INTO resumen_productos
        (nombre,categoria,precio,cantidad_vendidos,en_almacen,fecha_alta)
    values
        (_nombre, _categoria, _precio, _cantidad_vendidos, _en_almacen, _fecha_alta);
        
   select LAST_INSERT_ID() as ultimo_id;
        
END$$
DELIMITER ; -- EL DELIMITADOR VUELVE A SER ";"