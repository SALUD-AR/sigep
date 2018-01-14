SELECT 
  dbo.SMIAfiliados.afiDNI,
  dbo.SMIAfiliados.afiApellido,
  dbo.SMIAfiliados.afiNombre,
  dbo.SMIAfiliados.maNroDocumento,
  dbo.SMIAfiliados.maApellido,
  dbo.SMIAfiliados.maNombre,
  dbo.SMIAfiliados.afiFechaNac,
  dbo.SMIEfectores.CUIE,
  dbo.SMIEfectores.TipoEfector,
  dbo.SMIEfectores.NombreEfector
FROM
  dbo.SMIAfiliados
  INNER JOIN dbo.SMIEfectores ON (dbo.SMIAfiliados.CUIELugarAtencionHabitual = dbo.SMIEfectores.CUIE)
