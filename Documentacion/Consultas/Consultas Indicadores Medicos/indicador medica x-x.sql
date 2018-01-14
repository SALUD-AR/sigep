---//////////// TODOS LOS PENDEJOS QUE COMPLEN AÑOS EN EL BIMESTRE EN ESTE CASO TOMO BIMESTRE ENERO-FEBRERO DE 2010 y TIENEN 4 O MAS "NPE 32"


SELECT * from (
		select id_smiafiliados, afinombre, afiapellido, afifechanac, count (nomenclador.codigo) as "cantidad"
		from
		nacer.smiafiliados
		left JOIN facturacion.comprobante using (id_smiafiliados)
		left JOIN facturacion.prestacion using (id_comprobante)
		left join facturacion.nomenclador using (id_nomenclador)
		where (afifechanac + interval '365 days' between '2010/01/01' and '2010/02/28') and
		      (nomenclador.codigo='NPE 32')
		group by
		id_smiafiliados, afinombre, afiapellido, afifechanac
	       ) as select1	
where cantidad >=4
order by cantidad


---//////////// TODOS LOS PENDEJOS QUE COMPLEN AÑOS EN EL BIMESTRE EN ESTE CASO TOMO BIMESTRE ENERO-FEBRERO DE 2010

select count (id_smiafiliados) as "denominador" --afinombre, afiapellido,afidni,afifechanac
from
nacer.smiafiliados
where afifechanac + interval '365 days' between '2010/01/01' and '2010/02/28'