SELECT 
  efe_conv.nombre,
  count(nino.id_nino)+30 AS cb
FROM
  nacer.efe_conv
  left join trazadoras.nino using (cuie) 
where nino.fecha_carga between '2009-08-01' and '2009-08-31'
  GROUP BY  
  efe_conv.nombre
