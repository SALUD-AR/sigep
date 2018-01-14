SELECT 
  dbo.SMIAfiliados.afiApellido,
  dbo.SMIAfiliados.afiNombre,
  dbo.SMIAfiliados.afiDNI,
  dbo.SMIAfiliados.afiFechaNac,
  DATEDIFF(yy,SMIAfiliados.afiFechaNac,GETDATE()) as edad,
  dbo.SMIAfiliados.Activo,
  dbo.SMIAfiliados.afiDomCalle,
  dbo.SMIAfiliados.afiDomNro,
  dbo.SMIAfiliados.afiDomManzana,
  dbo.SMIAfiliados.afiDomPiso,
  dbo.SMIAfiliados.afiDomDepto,
  dbo.SMIAfiliados.afiDomBarrioParaje,
  dbo.SMIAfiliados.afiDomMunicipio,
  dbo.SMIAfiliados.afiDomDepartamento,
  dbo.SMIAfiliados.afiDomLocalidad,
  dbo.SMIAfiliados.afiTelefono,
  dbo.SMIEfectores.NombreEfector
FROM
  dbo.SMIAfiliados
  INNER JOIN dbo.SMIEfectores ON (dbo.SMIAfiliados.CUIEEfectorAsignado = dbo.SMIEfectores.CUIE)
WHERE
  (dbo.SMIAfiliados.afiTipoCategoria = '3' OR
  dbo.SMIAfiliados.afiTipoCategoria = '4') and
  (DATEDIFF(yy,SMIAfiliados.afiFechaNac,GETDATE()) < 5)
ORDER BY
  dbo.SMIEfectores.NombreEfector
