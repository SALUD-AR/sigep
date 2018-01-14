SELECT 
  nacer.efe_conv.nombre,
  count (nino.id_nino) as "Cantidad de Controles"
FROM
  nacer.efe_conv
  INNER JOIN trazadoras.nino ON (nacer.efe_conv.cuie = trazadoras.nino.cuie)
WHERE
  (nacer.efe_conv.cuidad = 'SAN LUIS')
GROUP BY
 nacer.efe_conv.nombre
ORDER BY
  nacer.efe_conv.nombre