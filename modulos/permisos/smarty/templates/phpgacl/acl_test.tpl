<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

{include file="phpgacl/header.tpl"}
    <meta name="generator" content="HTML Tidy, see www.w3.org">
    <title>phpGACL Admin</title>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  </head>

<form method="post" name="acl_list" action="acl_list.php">
<table cellpadding="2" cellspacing="2" border="2" width="100%">
  <tr align="center">
	<td valign="top" colspan="10" bgcolor="#cccccc"><b>phpGACL - Prueba de ACLs
		[ <a href="acl_list.php">Lista de ACLs</a> ]
		</b>
		<br>
	 </td>
  </tr>
  <tr>
	<td valign="top" colspan="11" bgcolor="#cccccc">
		{include file="phpgacl/pager.tpl" pager_data=$paging_data link="?"}
	</td>
  </tr>

  <tr>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>#</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Sección > ACO</b>
     </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Sección > ARO</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Valor de retorno</b>
     </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Codigo de ACL_CHECK()</b>
     </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Depurar</b>
     </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Tiempo (ms)</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <b>Acceso</b>
    </td>
  </tr>

  {section name=x loop=$acls}
  <tr>
    <td valign="middle" bgcolor="#cccccc" align="center">
		{$smarty.section.x.iteration}
    </td>
    <td valign="middle" bgcolor="#cccccc" align="center">
		{$acls[x].display_aco_name}
    </td>
    <td valign="top" bgcolor="#cccccc" align="left">
        {$acls[x].aro_section_name} > {$acls[x].aro_name}
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        {$acls[x].return_value}<br>
     </td>
    <td valign="top" bgcolor="#cccccc" align="left">
		acl_check('{$acls[x].aco_section_value}', '{$acls[x].aco_value}', '{$acls[x].aro_section_value}', '{$acls[x].aro_value}')
    </td>
    <td valign="top" bgcolor="#cccccc" align="center" nowrap>
		 [ <a href="acl_debug.php?aco_section_value={$acls[x].aco_section_value}&aco_value={$acls[x].aco_value}&aro_section_value={$acls[x].aro_section_value}&aro_value={$acls[x].aro_value}&action=Submit">depurar</a> ]
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
		{$acls[x].acl_check_time}
    </td>
    <td valign="middle" bgcolor="{if $acls[x].access}green{else}red{/if}" align="center">
		{if $acls[x].access}
			PERMITIDO
		{else}
			DENEGADO
		{/if}
    </td>
  </tr>
  {/section}
  <tr>
	<td valign="top" colspan="11" bgcolor="#cccccc">
		{include file="phpgacl/pager.tpl" pager_data=$paging_data link="?"}
	</td>
  </tr>

</form>
</table>

<br>
<table align="center" cellpadding="2" cellspacing="2" border="2" width="30%">
  <tr align="center">
	<td colspan="2" valign="top" bgcolor="#cccccc">
		<b>Sumario</b>
	</td>
  </tr>
  <tr align="center">
	<td valign="top" bgcolor="#cccccc">
		<b>Total de ACLs chequeados</b>
	</td>
	<td valign="top" bgcolor="#cccccc">
		{$total_acl_checks}
	</td>
  </tr>
  <tr align="center">
	<td valign="top" bgcolor="#cccccc">
		<b>Tiempo promedio / Chequeo</b>
	</td>
	<td valign="top" bgcolor="#cccccc">
		{$avg_acl_check_time}ms
	</td>
  </tr>
</table>
<br>
{include file="phpgacl/footer.tpl"}

