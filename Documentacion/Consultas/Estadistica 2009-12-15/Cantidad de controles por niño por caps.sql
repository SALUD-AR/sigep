SELECT 
  nacer.efe_conv.nombre,
  trazadoras.nino.num_doc,
  trazadoras.nino.apellido,
  trazadoras.nino.nombre,
  trazadoras.nino.fecha_nac,
  to_char(age (current_date, trazadoras.nino.fecha_nac),'Y') as edad,
  count (nino.num_doc) as "Cantidad de Controles"
FROM
  nacer.efe_conv
  INNER JOIN trazadoras.nino ON (nacer.efe_conv.cuie = trazadoras.nino.cuie)
WHERE
  (nacer.efe_conv.cuidad = 'SAN LUIS')
GROUP BY
 nacer.efe_conv.nombre,
  trazadoras.nino.num_doc,
  trazadoras.nino.apellido,
  trazadoras.nino.nombre,
  trazadoras.nino.fecha_nac 
ORDER BY
  nacer.efe_conv.nombre, num_doc