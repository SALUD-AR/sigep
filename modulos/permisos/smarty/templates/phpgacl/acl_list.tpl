<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

{include file="phpgacl/header.tpl"}
    <meta name="generator" content="HTML Tidy, see www.w3.org">
    <title>phpGACL Admin</title>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  </head>

<form method="get" name="acl_list" action="acl_list.php">
<table cellpadding="2" cellspacing="2" border="2" width="100%">
  <tr align="center">
	<td valign="top" colspan="11" bgcolor="#cccccc"><b>phpGACL - Lista de ACLs
		[ <a href="group_admin.php?group_type=aro">Administrar grupos ARO</a> ]
		[ <a href="acl_admin.php?return_page=acl_list.php">Administrar ACLs</a> ]
		[ <a href="acl_test.php">Probar ACLs</a> ]
		[ <a href="acl_debug.php">Depurar ACLs</a> ]
		</b>
		<br>
	</td>
  </tr>
  <tr>
    <td colspan="9" valign="top" bgcolor="#cccccc" align="center">
        <b>Filtros</b>
    </td>
  </tr>
  <tr>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b><br></b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>ACO</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>ARO</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Grupo ARO</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Valor de retorno</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Acceso</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Habilitado</b>
    </td>
  </tr>
  <tr>
    <td valign="middle" bgcolor="#cccccc" align="center">
        <b>Sección</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <input type="text" name="filter_aco_section_name" size="15" value="{$filter_aco_section_name}">
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <input type="text" name="filter_aro_section_name" size="15" value="{$filter_aro_section_name}">
    </td>
    <td rowspan="2" valign="middle" bgcolor="#cccccc" align="center">
        <input type="text" name="filter_aro_group_name" size="15" value="{$filter_aro_group_name}">
    </td>
    <td rowspan="2" valign="middle" bgcolor="#cccccc" align="center">
        <input type="text" name="filter_return_value" size="8" value="{$filter_return_value}">
    </td>
    <td rowspan="2" valign="middle" bgcolor="#cccccc" align="center">
		 <select name="filter_allow" tabindex="0">
			{html_options options=$options_filter_allow selected=$filter_allow}
		</select>
    </td>
    <td rowspan="2" valign="middle" bgcolor="#cccccc" align="center">
		 <select name="filter_enabled" tabindex="0">
			{html_options options=$options_filter_enabled selected=$filter_enabled}
		</select>
    </td>
  </tr>
  <tr>
    <td valign="middle" bgcolor="#cccccc" align="center">
        <b>Objeto</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <input type="text" name="filter_aco_name" size="15" value="{$filter_aco_name}">
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <input type="text" name="filter_aro_name" size="15" value="{$filter_aro_name}">
    </td>
  </tr>
  <tr>
    <td colspan="9" valign="top" bgcolor="#999999" align="center">
		<input type="submit" name="action" value="Filtrar">
    </td>
  </tr>
</table>
<table cellpadding="2" cellspacing="2" border="2" width="100%">
  <tr>
	<td valign="top" colspan="9" bgcolor="#cccccc">
		{include file="phpgacl/pager.tpl" pager_data=$paging_data link="?filter_aco_section_name=$filter_aco_section_name&filter_aco_name=$filter_aco_name&filter_aro_section_name=$filter_aro_section_name&filter_aro_name=$filter_aro_name&filter_axo_section_name=$filter_axo_section_name&filter_axo_name=$filter_axo_name&filter_aro_group_name=$filter_aro_group_name&filter_axo_group_name=$filter_axo_group_name&filter_return_value=$filter_return_value&filter_allow=$filter_allow&filter_enabled=$filter_enabled&"}
	</td>
  </tr>
  <tr valign="middle">
    <td bgcolor="#cccccc" align="center">
        <b>ID</b>
    </td>
    <td bgcolor="#cccccc" align="center">
        <b>Sección > ACO</b>
    </td>
    <td bgcolor="#cccccc" align="center">
        <b>Sección > ARO</b>
    </td>
    <td bgcolor="#cccccc" align="center">
        <b>Grupo ARO</b>
    </td>
    <td bgcolor="#cccccc" align="center">
        <b>Retorno</b>
    </td>

    <td bgcolor="#cccccc" align="center">
        <b>Acceso</b>
    </td>
    <td bgcolor="#cccccc" align="center">
        <b>Habilitado</b>
    </td>
    <td bgcolor="#cccccc" align="center">
        <b>Fecha de Modificación</b>
    </td>
    <td bgcolor="#cccccc" align="center">
        <b>Funciones</b>
    </td>

  </tr>

    {section name=x loop=$acls}
  <tr>
    <td valign="middle" rowspan="2" bgcolor="#cccccc" align="center">
            {$acls[x].id}
    </td>

    <td valign="top" bgcolor="#cccccc" align="left" nowrap>
       {section name=y loop=$acls[x].aco}
			<b>{$smarty.section.y.iteration}.</b> {$acls[x].aco[y].aco}
			<br>
		{/section}
       <br>
    </td>

    <td valign="top" bgcolor="#cccccc" align="left" nowrap>
       {section name=y loop=$acls[x].aro}
			<b>{$smarty.section.y.iteration}.</b> {$acls[x].aro[y].aro}
			<br>
		{/section}
       <br>
    </td>
    <td valign="top" bgcolor="#cccccc" align="left" nowrap>
       {section name=y loop=$acls[x].aro_groups}
			<b>{$smarty.section.y.iteration}.</b> {$acls[x].aro_groups[y].group}
			<br>
		{/section}
       <br>
    </td>

    <td valign="middle" bgcolor="#cccccc" align="center">
        {$acls[x].return_value}<br>
    </td>
    <td valign="middle" bgcolor="{if $acls[x].allow}green{else}red{/if}" align="center">
		{if $acls[x].allow}
			PERMITIDO
		{else}
			DENEGADO
		{/if}
    </td>
    <td valign="middle" bgcolor="{if $acls[x].enabled}green{else}red{/if}" align="center">
		{if $acls[x].enabled}
			Sí
		{else}
			No
		{/if}
    </td>
    <td valign="middle" bgcolor="#cccccc" align="center">
        {$acls[x].updated_date}
     </td>
    <td valign="middle" rowspan="2" bgcolor="#cccccc" align="center">
        [ <a href="acl_admin.php?action=edit&acl_id={$acls[x].id}&return_page={$return_page}">Editar</a> ]
        <input type="checkbox" name="delete_acl[]" value="{$acls[x].id}">
    </td>
  </tr>
  <tr>
    <td valign="middle" colspan="7" bgcolor="#cccccc" align="left">
        <b>Nota:</b> {$acls[x].note}<br>
    </td>
  </tr>
    {/section}
  <tr>
	<td valign="top" colspan="9" bgcolor="#cccccc">
		{include file="phpgacl/pager.tpl" pager_data=$paging_data link="?filter_aco_section_name=$filter_aco_section_name&filter_aco_name=$filter_aco_name&filter_aro_section_name=$filter_aro_section_name&filter_aro_name=$filter_aro_name&filter_axo_section_name=$filter_axo_section_name&filter_axo_name=$filter_axo_name&filter_aro_group_name=$filter_aro_group_name&filter_axo_group_name=$filter_axo_group_name&filter_return_value=$filter_return_value&filter_allow=$filter_allow&filter_enabled=$filter_enabled&"}
	</td>
  </tr>
	  <tr>
		<td valign="top" bgcolor="#999999" colspan="8">
		</td>
		<td valign="top" bgcolor="#999999">
		  <div align="center">
			<input type="submit" name="action" value="Borrar">
		  </div>
		</td>
	</tr>
    </table>
    <input type="hidden" name="return_page" value="{$return_page}">
</form>
{include file="phpgacl/footer.tpl"}

