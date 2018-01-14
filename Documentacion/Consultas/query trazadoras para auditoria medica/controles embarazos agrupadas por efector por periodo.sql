SELECT
  nacer.efe_conv.nombre,
  nacer.efe_conv.cuie,
  count (trazadoras.embarazadas.id_emb) as total
FROM
  nacer.efe_conv
  INNER JOIN trazadoras.embarazadas ON (nacer.efe_conv.cuie = trazadoras.embarazadas.cuie)
where trazadoras.embarazadas.fecha_control BETWEEN '2009-09-01' and '2009-12-31'
group by  nacer.efe_conv.cuie, nacer.efe_conv.nombre
order by nacer.efe_conv.cuie
