<?


/*****************************************************************************
 * Actualiza los clientes mas usado por el usuario
 * @return void
 * @param $id_entidad es el id de la entidad seleccioanda por el usuario
 * @param $id_usuario es el id del usuario que selecciona la entidad 
 * @param $fecha es la fehca en la que se realia la modificacion
 
 ****************************************************************************/

function actualizar_clientes_mas_usuados($id_entidad,$usuario,$fecha) {
	
    $sql="select * from usuarios_clientes where id_usuario=$usuario and id_entidad=$id_entidad";
    $resul_select=sql($sql,"No se pudo consulta si ya existia esa entrada usuario-cliente") or fin_pagina();
    if($resul_select->RecordCount()>0) {
    	$nuevo_peso=$resul_select->fields['peso_uso']+1;
        $sql="update usuarios_clientes set peso_uso=$nuevo_peso,fecha_ultimo_uso='$fecha' where id_usuario=$usuario and id_entidad=$id_entidad";
        $reul_update=sql($sql,"No se pudo realizar el update en la tabla usuarios_clientes") or fin_pagina();
    }
    else {
    	$sql="insert into usuarios_clientes 
    	      (id_usuario,id_entidad,fecha_ultimo_uso,peso_uso,empezo_uso_en)
              values ($usuario,$id_entidad,'$fecha',1,1)";
        $result_insert=sql($sql,"No se pudo realizar el insert en la tabla usuarios_clientes") or fin_pagina();
   }
}