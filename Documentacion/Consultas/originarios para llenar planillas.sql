select ori.cuie, efe_conv.nombre, ori.num_doc, ori.apellido, ori.nombre, ori.fecha_evento from trazadoras.ori
left join nacer.efe_conv using (cuie)

