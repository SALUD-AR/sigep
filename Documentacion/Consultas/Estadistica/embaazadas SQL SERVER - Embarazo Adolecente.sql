SELECT 
  dbo.SMIAfiliados.afiApellido AS 'Apellido',
  dbo.SMIAfiliados.afiNombre AS 'Nombre',
  dbo.SMIAfiliados.afiDNI AS 'DNI',
  dbo.SMIEfectores.NombreEfector as 'Efector',
  dbo.SMIAfiliados.afiFechaNac as 'Fecha de Nacimiento',
  dbo.SMIAfiliados.FechaProbableParto as 'Fecha Aproximada de Parto',
  dbo.SMIAfiliados.FechaProbableParto - dbo.SMIAfiliados.afiFechaNac AS 'Edad',
  dbo.SMIAfiliados.afiDomCalle as 'Calle',
  dbo.SMIAfiliados.afiDomNro as 'Numero',
  dbo.SMIAfiliados.afiDomManzana as 'Manzana',
  dbo.SMIAfiliados.afiDomPiso as 'Piso',
  dbo.SMIAfiliados.afiDomDepto as 'Departamento',
  dbo.SMIAfiliados.afiDomEntreCalle1 as 'Entre Calle',
  dbo.SMIAfiliados.afiDomEntreCalle2 as 'Entre Calle',
  dbo.SMIAfiliados.afiDomBarrioParaje as 'Barrio',
  dbo.SMIAfiliados.afiTelefono as 'Telefono'
FROM
  dbo.SMIAfiliados
  INNER JOIN dbo.SMIEfectores ON (dbo.SMIAfiliados.CUIEEfectorAsignado = dbo.SMIEfectores.CUIE)
WHERE
  dbo.SMIAfiliados.afiTipoCategoria = 1
