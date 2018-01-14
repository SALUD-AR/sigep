SELECT
  nacer.efe_conv.nombre,
  nacer.efe_conv.cuie,
  count (trazadoras.partos.id_par) as total
FROM
  nacer.efe_conv
  INNER JOIN trazadoras.partos ON (nacer.efe_conv.cuie = trazadoras.partos.cuie)
where trazadoras.partos.fecha_parto BETWEEN '2009-09-01' and '2009-12-31'
group by  nacer.efe_conv.cuie, nacer.efe_conv.nombre
order by nacer.efe_conv.cuie
