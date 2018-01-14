<?

require_once ("../../config.php");
if(isset($_POST["id_pais"]))
	{
		$opcionespcias = '<option value="0"> Seleccione Provincia</option>';
		
		$strConsulta = "select id_provincia, nombre from uad.provincias where id_pais = '".$_POST['id_pais']."' order by nombre";
		$result =  @pg_exec($strConsulta);
		

		while( $fila = pg_fetch_array ($result) )
		{
			$opcionespcias.='<option value="'.$fila["id_provincia"].'">'.$fila["nombre"].'</option>';
		}

		echo $opcionespcias;
	}
	
if(isset($_POST["id_provincia"]))
	{
		$departamento = '<option value="0"> Seleccione Departamento</option>';
		
			$strConsulta = "select id_departamento, nombre from uad.departamentos where id_provincia = '".$_POST['id_provincia']."' order by nombre ";
			$result = @pg_exec($strConsulta); 
		

		while( $fila = pg_fetch_array ($result) )
		{
			$departamento.='<option value="'.$fila["id_departamento"].'">'.$fila["nombre"].'</option>';
		}

		echo $departamento;
	}
	
if(isset($_POST["id_departamento"]))
	{
	
		$opcionesloc = '<option value="0"> Seleccione Localidad</option>';
		
		
		$strConsulta = "select id_localidad, nombre from uad.localidades where id_departamento = '".$_POST['id_departamento']."' order by nombre";
		$result =  @pg_exec($strConsulta);
		

		while( $fila = pg_fetch_array ($result) )
		{
			$opcionesloc.='<option value="'.$fila["id_localidad"].'">'.$fila["nombre"].'</option>';
					
		}
		echo $opcionesloc;
		
	}

	?>
