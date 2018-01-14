SELECT 
  facturacion.comprobante.cuie,
  nacer.smiafiliados.afidni AS num_doc,
  nacer.smiafiliados.afiapellido AS apellido,
  nacer.smiafiliados.afinombre AS nombre,
  nacer.smiafiliados.afifechanac AS fecha_nac,
  facturacion.comprobante.fecha_comprobante AS fecha_control,
  '0' AS peso,
  '0' AS talla,
  '0' AS perim_cefalico,
  facturacion.comprobante.fecha_comprobante AS triple_viral,
  nacer.efe_conv.nombre
FROM
  nacer.smiafiliados
  INNER JOIN facturacion.comprobante ON (nacer.smiafiliados.id_smiafiliados = facturacion.comprobante.id_smiafiliados)
  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
  INNER JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
  INNER JOIN nacer.efe_conv ON (facturacion.comprobante.cuie = nacer.efe_conv.cuie)
WHERE
  (facturacion.nomenclador.codigo = 'NPE 41') AND 
  (facturacion.comprobante.fecha_comprobante >= '2009-01-01')
