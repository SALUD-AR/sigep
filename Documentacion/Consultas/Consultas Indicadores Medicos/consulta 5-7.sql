-- ///////////////////////////////////////////////////////////////////indicador Full full


select cast(numerador as float)/ cast(denominador as float) as "indicador" from
(
	select * from
	(
			select sum(resultado) as "numerador" from 
			(
				select DISTINCT *,
				CASE WHEN (
					   ((select count (id_smiafiliados)
									from
									nacer.smiafiliados
									left JOIN facturacion.comprobante using (id_smiafiliados)
									left JOIN facturacion.prestacion using (id_comprobante)
									left join facturacion.nomenclador using (id_nomenclador)
									WHERE
									 (codigo = 'MER 08') and (id_smiafiliados=select1.id_smiafiliados) and (fecha_comprobante between select1.fecha_comprobante AND select1.fecha_comprobante + interval '270 days')
									  group by id_smiafiliados
					    )>=6) and
					    ((select count (id_smiafiliados)
									from
									nacer.smiafiliados
									left JOIN facturacion.comprobante using (id_smiafiliados)
									left JOIN facturacion.prestacion using (id_comprobante)
									left join facturacion.nomenclador using (id_nomenclador)
									WHERE
									 (codigo = 'LMI 44') and (id_smiafiliados=select1.id_smiafiliados) and (fecha_comprobante between select1.fecha_comprobante AND select1.fecha_comprobante + interval '270 days')
									  group by id_smiafiliados
					    )>=1) and
					   
					    ((select count (id_smiafiliados)
									from
									nacer.smiafiliados
									left JOIN facturacion.comprobante using (id_smiafiliados)
									left JOIN facturacion.prestacion using (id_comprobante)
									left join facturacion.nomenclador using (id_nomenclador)
									WHERE
									 (codigo = 'IMI 51') and (id_smiafiliados=select1.id_smiafiliados) and (fecha_comprobante between select1.fecha_comprobante AND select1.fecha_comprobante + interval '270 days')
									  group by id_smiafiliados
					    )>=1)
					)

				THEN 1
				ELSE 0
				END as resultado

				from (
					select
					id_smiafiliados, comprobante.fecha_comprobante, codigo
					from
					nacer.smiafiliados
					left JOIN facturacion.comprobante using (id_smiafiliados)
					left JOIN facturacion.prestacion using (id_comprobante)
					left join facturacion.nomenclador using (id_nomenclador)
					WHERE
					 (facturacion.nomenclador.codigo = 'MEM 01') and (facturacion.comprobante.fecha_comprobante between '2010/01/01' AND '2010/12/31')
					 order by id_smiafiliados, codigo, fecha_comprobante) as select1
			) as a
	) as numerador,

	(
			select DISTINCT
			count (id_smiafiliados) as denominador
			from
			nacer.smiafiliados
			left JOIN facturacion.comprobante using (id_smiafiliados)
			left JOIN facturacion.prestacion using (id_comprobante)
			left join facturacion.nomenclador using (id_nomenclador)
			WHERE
			 (facturacion.nomenclador.codigo = 'MEM 01') and (facturacion.comprobante.fecha_comprobante between '2010/01/01' AND '2010/12/31')

	)as denominador   ----/////////facturacion.comprobante.periodo between '2010/09' AND '2010/10'

) as indicadornt (id_smiafiliados) as denominador
			from
			nacer.smiafiliados
			left JOIN facturacion.comprobante using (id_smiafiliados)
			left JOIN facturacion.prestacion using (id_comprobante)
			left join facturacion.nomenclador using (id_nomenclador)
			WHERE
			 (facturacion.nomenclador.codigo = 'MEM 01') and (facturacion.comprobante.fecha_comprobante between '2010/01/01' AND '2010/12/31')

	)as denominador   ----/////////facturacion.comprobante.periodo between '2010/09'