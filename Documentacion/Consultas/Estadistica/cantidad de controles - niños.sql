select distinct total,count (total)  from(
select count (num_doc) as total
from trazadoras.nino
group by num_doc) as pp
GROUP BY pp.total


