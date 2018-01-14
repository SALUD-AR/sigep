<?

require_once ("../../config.php");
include_once('lib_inscripcion.php');
	
if(isset($_POST["id_pais"]))
	{
		$opciones6 = '<option value="0"> Seleccione Provincia</option>';
		
		$strConsulta = "select id_provincia, nombre from uad.provincias where id_pais = '".$_POST['id_pais']."' order by nombre";
		$result =  @pg_exec($strConsulta);
		

		while( $fila = pg_fetch_array ($result) )
		{
			$opciones6.='<option value="'.$fila["id_provincia"].'">'.$fila["nombre"].'</option>';
		}

		echo $opciones6;
	}
	
if(isset($_POST["id_provincia"]))
	{
		$opciones7 = '<option value="0"> Seleccione Localidad</option>';
		
		$strConsulta = "select l.id_localidad, l.nombre from uad.localidades l,uad.provincias p, uad.departamentos d 
						where p.id_provincia = '".$_POST['id_provincia']."' and d.id_provincia = p.id_provincia and 
						l.id_departamento = d.id_departamento order by nombre";
		$result =  @pg_exec($strConsulta);
		

		while( $fila = pg_fetch_array ($result) )
		{
			$opciones7.='<option value="'.$fila["id_provincia"].'">'.$fila["nombre"].'</option>';
		}

		echo $opciones7;
	}
	
if(isset($_POST["id_departamento"]))
	{
	
		$opciones2 = '<option value="0"> Seleccione Localidad</option>';
		
		
		$strConsulta = "select id_localidad, nombre from uad.localidades where id_departamento = '".$_POST['id_departamento']."' order by nombre";
		$result =  @pg_exec($strConsulta);
		

		while( $fila = pg_fetch_array ($result) )
		{
			$opciones2.='<option value="'.$fila["id_localidad"].'">'.$fila["nombre"].'</option>';
					
		}
		echo $opciones2;
		
	}

	if(isset($_POST["id_localidad"]))
	{
		
		$opciones5 = '<option> Codigo Postal </option>';

		
		$strConsulta = "select id_codpos, codigopostal from uad.codpost where id_localidad = '".$_POST['id_localidad']."'";
		$result =  @pg_exec($strConsulta);
		

		while( $fila = pg_fetch_array ($result) )
		{
		$opciones5.='<option value="'.$fila["id_codpos"].'">'.$fila["codigopostal"].'</option>';
					
		}
			
		echo $opciones5;
		
		
	}
	if (isset($_POST["id_codpos"]))
	
	{
		
		$opciones3 = '<option value="0"> Seleccione Municipio</option>';
						
		$strConsulta = "select id_municipio, nombre from uad.municipios where id_codpos = '".$_POST['id_codpos']."' order by nombre";
		$result =  @pg_exec($strConsulta);
				
		while( $fila = pg_fetch_array ($result) )
		{
			$opciones3.='<option value="'.$fila["id_municipio"].'">'.$fila["nombre"].'</option>';
		}
		
		echo $opciones3;	
		
	}
	
	
	if(isset($_POST["id_municipio"]))
	{
		$opciones4 = '<option value="0"> Seleccione Barrio</option>';

		
		$strConsulta = "select id_barrio, nombre from uad.barrios where id_municipio = '".$_POST['id_municipio']."' order by nombre";
		$result =  @pg_exec($strConsulta);
		

		while( $fila = pg_fetch_array ($result) )
		{
			$opciones4.='<option value="'.$fila["id_barrio"].'">'.$fila["nombre"].'</option>';
		}

		echo $opciones4;
		
	}
	?>
