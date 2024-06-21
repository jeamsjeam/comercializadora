-- Procedimientos almacenados
/*DELIMITER $$

CREATE PROCEDURE ObtenerPrecioProducto(
    IN producto_id BIGINT,
    IN moneda_id BIGINT,
    IN fecha DATE
)
BEGIN
    IF fecha IS NULL THEN
        SET fecha = CURDATE();
    END IF;

    IF moneda_id IS NULL THEN
        -- Obtener precios en todas las monedas
        SELECT 
            p.nombre AS producto,
            m.nombre AS moneda,
            p.precio * h.tasa_cambio AS precio_convertido,
            h.tasa_cambio,
            h.fecha
        FROM 
            productos p
        JOIN 
            tasas_cambio h ON h.moneda_id IN (SELECT id FROM monedas)
        JOIN 
            monedas m ON h.moneda_id = m.id
        WHERE 
            p.id = producto_id
            AND h.fecha = (
                SELECT MAX(fecha)
                FROM tasas_cambio
                WHERE moneda_id = h.moneda_id
                AND fecha <= fecha
            );
    ELSE
        -- Obtener precio en la moneda especificada
        SELECT 
            p.nombre AS producto,
            m.nombre AS moneda,
            p.precio * h.tasa_cambio AS precio_convertido,
            h.tasa_cambio,
            h.fecha
        FROM 
            productos p
        JOIN 
            tasas_cambio h ON h.moneda_id = moneda_id
        JOIN 
            monedas m ON h.moneda_id = m.id
        WHERE 
            p.id = producto_id
            AND h.fecha = (
                SELECT MAX(fecha)
                FROM tasas_cambio
                WHERE moneda_id = moneda_id
                AND fecha <= fecha
            );
    END IF;
END $$

DELIMITER ;

CALL ObtenerPrecioProducto(1, 2, '2024-06-01'); -- Producto ID 1, Moneda ID 2 (Dólar), Fecha '2024-06-01'
CALL ObtenerPrecioProducto(1, NULL, '2024-06-01'); -- Producto ID 1, Todas las monedas, Fecha '2024-06-01'
CALL ObtenerPrecioProducto(1, NULL, NULL); -- Producto ID 1, Todas las monedas, Fecha actual
*/