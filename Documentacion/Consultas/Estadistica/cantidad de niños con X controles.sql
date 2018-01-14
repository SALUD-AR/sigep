select count (total) from(
select count (num_doc) as total
from trazadoras.nino
group by num_doc) as pp
where total=2
