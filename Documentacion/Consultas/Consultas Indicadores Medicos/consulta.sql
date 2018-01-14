-- ///////////////////////////////////////////////////////////////////indicador Full full


select cast(numerador as float)/ cast(denominador as float) as "indicador" from
(
	select * from
	(
			select sum(resultado) as "numerador" from 
			(
			select *,
			CASE WHEN ((codigo='MEM 72') and ((select count (id_smiafiliados)
								from
								nacer.smiafiliados
								left JOIN facturacion.comprobante using (id_smiafiliados)
								left JOIN facturacion.prestacion using (id_comprobante)
								left join facturacion.nomenclador using (id_nomenclador)
								WHERE
								 (codigo = 'MEM 73') and (id_smiafiliados=select1.id_smiafiliados) and (fecha_comprobante + interval '30 days'>= select1.fecha_comprobante) and (fecha_comprobante between '2010/01/01' AND '2010/12/31')
								  group by id_smiafiliados
							)>=1)
			)
							THEN 1
			     ELSE 0
			END as resultado

			from (
			select distinct
			id_smiafiliados, comprobante.fecha_comprobante, codigo
			from
			nacer.smiafiliados
			left JOIN facturacion.comprobante using (id_smiafiliados)
			left JOIN facturacion.prestacion using (id_comprobante)
			left join facturacion.nomenclador using (id_nomenclador)
			WHERE
			 (facturacion.nomenclador.codigo = 'MEM 72') and (facturacion.comprobante.fecha_comprobante between '2010/01/01' AND '2010/12/31')
			 order by id_smiafiliados, codigo, fecha_comprobante) as select1
			) as a
	) as numerador,

	(
			select 
			count (id_smiafiliados) as denominador
			from
			nacer.smiafiliados
			left JOIN facturacion.comprobante using (id_smiafiliados)
			left JOIN facturacion.prestacion using (id_comprobante)
			left join facturacion.nomenclador using (id_nomenclador)
			WHERE
			 (facturacion.nomenclador.codigo = 'MEM 72') and (facturacion.comprobante.fecha_comprobante between '2010/09/01' AND '2010/10/31')

	)as denominador   ----/////////facturacion.comprobante.periodo between '2010/09' AND '2010/10'

) as indicador


-- //////////////////////////////////////////////////////////////    numerador


select sum(resultado) as "numerador" from 
(
select *,
CASE WHEN ((codigo='MEM 72') and ((select count (id_smiafiliados)
					from
					nacer.smiafiliados
					left JOIN facturacion.comprobante using (id_smiafiliados)
					left JOIN facturacion.prestacion using (id_comprobante)
					left join facturacion.nomenclador using (id_nomenclador)
					WHERE
					 (codigo = 'MEM 73') and (id_smiafiliados=select1.id_smiafiliados) and (fecha_comprobante + interval '30 days'>= select1.fecha_comprobante) and (fecha_comprobante between '2010/01/01' AND '2010/12/31')
					  group by id_smiafiliados
				)>=1)
)
				THEN 1
     ELSE 0
END as resultado

from (
select distinct
id_smiafiliados, comprobante.fecha_comprobante, codigo
from
nacer.smiafiliados
left JOIN facturacion.comprobante using (id_smiafiliados)
left JOIN facturacion.prestacion using (id_comprobante)
left join facturacion.nomenclador using (id_nomenclador)
WHERE
 (facturacion.nomenclador.codigo = 'MEM 72') and (facturacion.comprobante.fecha_comprobante between '2010/01/01' AND '2010/12/31')
 order by id_smiafiliados, codigo, fecha_comprobante) as select1
) as a


-- ///////////////////////////////////////////////////////////////////////  Denominador


select 
count (id_smiafiliados) as denominador
from
nacer.smiafiliados
left JOIN facturacion.comprobante using (id_smiafiliados)
left JOIN facturacion.prestacion using (id_comprobante)
left join facturacion.nomenclador using (id_nomenclador)
WHERE
 (facturacion.nomenclador.codigo = 'MEM 72') and (facturacion.comprobante.fecha_comprobante between '2010/01/01' AND '2010/12/31')



-- //////////////////////////////////////////////////////////     corrobora con detalle CASE

select *,
CASE WHEN ((codigo='MEM 72') and ((select count (id_smiafiliados)
					from
					nacer.smiafiliados
					left JOIN facturacion.comprobante using (id_smiafiliados)
					left JOIN facturacion.prestacion using (id_comprobante)
					left join facturacion.nomenclador using (id_nomenclador)
					WHERE
					 (codigo = 'MEM 73') and (id_smiafiliados=select1.id_smiafiliados) and (fecha_comprobante + interval '30 days'>= select1.fecha_comprobante) and (fecha_comprobante between '2010/01/01' AND '2010/12/31')
					  group by id_smiafiliados
				)>=1)
)
				THEN 'si'
     ELSE 'sorry loco'
END as resultado

from (
select distinct
id_smiafiliados, comprobante.fecha_comprobante, codigo
from
nacer.smiafiliados
left JOIN facturacion.comprobante using (id_smiafiliados)
left JOIN facturacion.prestacion using (id_comprobante)
left join facturacion.nomenclador using (id_nomenclador)
WHERE
 (facturacion.nomenclador.codigo = 'MEM 72') and (facturacion.comprobante.fecha_comprobante between '2010/01/01' AND '2010/12/31')
 order by id_smiafiliados, codigo, fecha_comprobante) as select1

--/////////////////////////////////////////////////////////    corrobora con detalle TOTAL

select distinct
id_smiafiliados, comprobante.fecha_comprobante, codigo
from
nacer.smiafiliados
left JOIN facturacion.comprobante using (id_smiafiliados)
left JOIN facturacion.prestacion using (id_comprobante)
left join facturacion.nomenclador using (id_nomenclador)
WHERE
 ((facturacion.nomenclador.codigo = 'MEM 72') or (facturacion.nomenclador.codigo = 'MEM 73'))and (facturacion.comprobante.fecha_comprobante between '2010/01/01' AND '2010/12/31')
 order by id_smiafiliados, codigo, fecha_comprobanteselect distinct
id_smiafiliados, comprobante.fecha_comprobante, codigo
from
n