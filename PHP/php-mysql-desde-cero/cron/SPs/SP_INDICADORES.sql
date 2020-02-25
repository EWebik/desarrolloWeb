DELIMITER $$ -- CAMBIANDO EL DELIMITADOR POR "$$"
DROP PROCEDURE IF EXISTS `SP_INDICADORES` $$
CREATE PROCEDURE `SP_INDICADORES`() -- NO TENEMOS DATOS DE ENTRADA
BEGIN
    DECLARE _PRODUCTOS_TOTALES		FLOAT;
    DECLARE _PRODUCTOS_ALMACEN		FLOAT;
    DECLARE _INGRESOS_TOTALES		FLOAT;
    
    -- Nos permite obtener el total de productos vendidos
	SET _PRODUCTOS_TOTALES = (SELECT SUM(cantidad_vendidos) as vendidos FROM resumen_productos);
    
    -- Nos regresa el total de productos en almacen
    SET _PRODUCTOS_ALMACEN = (SELECT SUM(en_almacen) as enAlmacen FROM resumen_productos);
    
    -- Nos regresa los ingresos totales el 100000 es un valor de corrección
	-- ya que son datos inventados
    SET _INGRESOS_TOTALES = (SELECT (SUM(precio) * SUM(cantidad_vendidos))/100000 as ingresos FROM resumen_productos);
    
    -- bien ahora solo debemos retornar los 3 datos mediante otro SELECT
    SELECT _PRODUCTOS_TOTALES AS PT, _PRODUCTOS_ALMACEN AS PA, _INGRESOS_TOTALES AS IT;
    
END$$
DELIMITER ; -- EL DELIMITADOR VUELVE A SER ";"