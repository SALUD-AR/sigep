SELECT 
  nacer.efe_conv.nombre,
  trazadoras.nino.num_doc,
  trazadoras.nino.apellido,
  trazadoras.nino.nombre,
  trazadoras.nino.fecha_nac,
  trazadoras.nino.fecha_control,
  trazadoras.nino.peso,
  trazadoras.nino.talla,
  trazadoras.nino.perim_cefalico,
  to_char(age (current_date, trazadoras.nino.fecha_nac),'YY') as edad
FROM
  nacer.efe_conv
  INNER JOIN trazadoras.nino ON (nacer.efe_conv.cuie = trazadoras.nino.cuie)
WHERE
  (nacer.efe_conv.cuidad = 'SAN LUIS')
ORDER BY
  nacer.efe_conv.nombre,num_doc
