<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

{include file="phpgacl/header.tpl"}
    <meta name="generator" content="HTML Tidy, see www.w3.org">
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  </head>

<form method="get" name="acl_debug" action="acl_debug.php">
<table cellpadding="2" cellspacing="2" border="2" width="100%">
  <tr align="center">
	<td valign="top" colspan="6" bgcolor="#cccccc"><b>phpGACL - Consola de depuracion de ACLs
   	[ <a href="acl_list.php">Lista de ACLs</a> ]
		[ <a href="acl_test.php">Prueba de ACLs</a> ]
		</b>
		<br>
	</td>
  </tr>
  <tr>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b><br></b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Sección ACO</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Valor ACO</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Sección ARO</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Valor ARO</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Root ARO Group ID</b>
    </td>
  </tr>
  <tr>
    <td valign="middle" bgcolor="#cccccc" align="center">
        <b>acl_query(</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <input type="text" name="aco_section_value" size="15" value="{$aco_section_value}">
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <input type="text" name="aco_value" size="15" value="{$aco_value}">
    </td>
    <td valign="middle" bgcolor="#cccccc" align="center">
        <input type="text" name="aro_section_value" size="15" value="{$aro_section_value}">
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
		<input type="text" name="aro_value" size="15" value="{$aro_value}">
    </td>
    <td valign="middle" bgcolor="#cccccc" align="center">
		<input type="text" name="root_aro_group_id" size="15" value="{$root_aro_group_id}">
       <b>)</b>
    </td>
  </tr>
  </tr>
	  <tr>
		<td valign="top" bgcolor="#999999" colspan="9" align="center">
			<input type="submit" name="action" value="Enviar">
		</td>
	</tr>
</table>
<table cellpadding="2" cellspacing="2" border="2" width="100%">
    {section name=x loop=$acls}
	{if $smarty.section.x.first}
  <tr>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>ID</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Sección ACO</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Valor ACO</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Sección ARO</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Valor ARO</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>ARO Group ID</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>ARO Group Tree Level</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Valor de retorno</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Acceso</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Fecha de actualización</b>
    </td>
  </tr>
	{/if}
  <tr>
    <td valign="middle" rowspan="2" bgcolor="#cccccc" align="center">
        {$acls[x].id}
    </td>

    <td valign="top" bgcolor="#cccccc" align="left" nowrap>
		{$acls[x].aco_section_value}
    </td>
    <td valign="top" bgcolor="#cccccc" align="left" nowrap>
		{$acls[x].aco_value}
    </td>

    <td valign="top" bgcolor="#cccccc" align="left" nowrap>
		{$acls[x].aro_section_value}<br>
    </td>
    <td valign="top" bgcolor="#cccccc" align="left" nowrap>
		{$acls[x].aro_value}<br>
    </td>

    <td valign="top" bgcolor="#cccccc" align="left" nowrap>
		{$acls[x].aro_group_id}<br>
    </td>
    <td valign="top" bgcolor="#cccccc" align="left" nowrap>
		{$acls[x].aro_tree_level}<br>
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
    <td valign="middle" bgcolor="#cccccc" align="center">
        {$acls[x].updated_date}
     </td>
  </tr>
  <tr>
    <td valign="middle" colspan="13" bgcolor="#cccccc" align="left">
        <b>Nota:</b> {$acls[x].note}<br>
    </td>
  </tr>
    {/section}
    </table>
    <input type="hidden" name="return_page" value="{$return_page}">
</form>
{include file="phpgacl/footer.tpl"}

