SELECT 
  dbo.SMIAfiliados.afiApellido,
  dbo.SMIAfiliados.afiNombre,
  dbo.SMIAfiliados.afiTipoDoc,
  dbo.SMIAfiliados.afiDNI,
  dbo.SMIEfectores.NombreEfector,
  dbo.SMIAfiliados.afiFechaNac,
  dbo.SMIAfiliados.FechaDiagnosticoEmbarazo
 
FROM
  dbo.SMIAfiliados
  INNER JOIN dbo.SMIEfectores ON (dbo.SMIAfiliados.CUIEEfectorAsignado = dbo.SMIEfectores.CUIE)
WHERE
  dbo.SMIAfiliados.afiTipoCategoria = 1 AND 
  dbo.SMIAfiliados.FechaDiagnosticoEmbarazo >= '20080101' AND 
  FechaDiagnosticoEmbarazo < dateadd(dd, 365, '20080101')
