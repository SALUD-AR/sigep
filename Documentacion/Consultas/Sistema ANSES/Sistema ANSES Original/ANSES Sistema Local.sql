SELECT 
  nacer.smiafiliados.id_smiafiliados as id,
  nacer.smiafiliados.clavebeneficiario as ClaveInscripto,
  nacer.smiafiliados.afiapellido as Apellido,
  nacer.smiafiliados.afinombre as Nombre,
  nacer.smiafiliados.afidni as DNI,
  nacer.efe_conv.nombre as Efector,
  date (nacer.smiafiliados.fechainscripcion) as FechaInscripcion,
  nacer.smiafiliados.afifechanac AS FechaNacimiteno
FROM
  nacer.smiafiliados
  INNER JOIN nacer.efe_conv ON (nacer.smiafiliados.cuieefectorasignado = nacer.efe_conv.cuie)
