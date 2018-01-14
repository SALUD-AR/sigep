SELECT
  nacer.efe_conv.nombre,
  nacer.efe_conv.cuie,
  count (trazadoras.nino.id_nino) as total
FROM
  nacer.efe_conv
  INNER JOIN trazadoras.nino ON (nacer.efe_conv.cuie = trazadoras.nino.cuie)
where trazadoras.nino.fecha_control BETWEEN '2009-09-01' and '2009-12-31'
group by  nacer.efe_conv.cuie, nacer.efe_conv.nombre
order by nacer.efe_conv.cuie
