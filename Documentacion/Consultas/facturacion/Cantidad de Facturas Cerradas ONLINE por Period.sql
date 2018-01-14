SELECT
count(facturacion.factura.id_factura) as cantidad
FROM
facturacion.factura
WHERE
facturacion.factura.estado = 'C' AND
facturacion.factura.online = 'SI' AND
facturacion.factura.periodo = '2010/08'