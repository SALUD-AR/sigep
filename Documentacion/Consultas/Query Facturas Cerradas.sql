SELECT
facturacion.factura.id_factura,
nacer.efe_conv.nombre,
facturacion.factura.periodo,
facturacion.factura.fecha_carga,
facturacion.factura.fecha_factura,
facturacion.log_factura.fecha as Fecha_Cierre,
facturacion.log_factura.tipo,
facturacion.log_factura.descripcion,
facturacion.log_factura.usuario
FROM
facturacion.factura
INNER JOIN facturacion.log_factura ON facturacion.factura.id_factura = facturacion.log_factura.id_factura
INNER JOIN nacer.efe_conv ON facturacion.factura.cuie = nacer.efe_conv.cuie
WHERE
facturacion.log_factura.tipo='Cerrar Factura'
ORDER BY
facturacion.factura.id_factura DESC
