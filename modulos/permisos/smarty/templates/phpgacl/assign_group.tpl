<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

{include file="phpgacl/header.tpl"}
    <meta name="generator" content="HTML Tidy, see www.w3.org">
    <title>phpGACL Admin</title>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  </head>

<script LANGUAGE="JavaScript">
{$js_array}
</script>
{include file="phpgacl/acl_admin_js.tpl"}

  <body onload="populate(document.assign_group.{$group_type}_section,document.assign_group.elements['objects[]'], '{$js_array_name}')">
    <form method="post" name="assign_group" action="assign_group.php">
      <table cellpadding="2" cellspacing="2" border="2" width="100%">
        <tbody>
          <tr align="center">
            <td valign="top" rowspan="1" colspan="4" bgcolor="#cccccc"><b>phpGACL - Asignar {$group_type|upper}s [ <a href="group_admin.php?group_type={$group_type}">Admin. Grupos {$group_type|upper}</a> ] </b><br>
             </td>
          </tr>

          <tr>
            <td valign="top" align="center" bgcolor="#d3dce3"><b>Secciones</b><br>
             </td>

            <td valign="top" align="center" bgcolor="#d3dce3"><b>AROs</b><br>
             </td>

            <td valign="top" align="center" bgcolor="#d3dce3">&nbsp;<br>
             </td>

            <td valign="top" align="center" bgcolor="#d3dce3"><b>Seleccionado</b><br>
             </td>
          </tr>

          <tr>
            <td valign="middle" bgcolor="#cccccc" align="center">[ <a href="edit_object_sections.php?object_type={$group_type}&return_page={$return_page}">Editar</a> ]<br>
             <br>
             <select name="{$group_type}_section" tabindex="0" size="10" width="200" onclick="populate(document.assign_group.{$group_type}_section,document.assign_group.elements['objects[]'],'{$js_array_name}')">
                {html_options options=$options_sections selected=$section_value}
            </select> <br>
             </td>

            <td valign="middle" bgcolor="#cccccc" align="center">
            [ <a href="javascript: location.href = 'edit_objects.php?object_type={$group_type}&section_value=' + document.assign_group.aro_section.options[document.assign_group.aro_section.selectedIndex].value + '&return_page={$return_page}';">Editar</a> ]
             <br>
             <select name="objects[]" tabindex="0" size="10" width="200" multiple>
            </select> <br>
             </td>

            <td valign="middle" bgcolor="#cccccc" align="center">
                <input type="BUTTON" name="select" value="&nbsp;>>&nbsp;" onClick="select_item(document.assign_group.{$group_type}_section, document.assign_group.elements['objects[]'], document.assign_group.elements['selected_{$group_type}[]'])">
                <br>
                <br>
                <input type="BUTTON" name="deselect" value="&nbsp;<<&nbsp;" onClick="deselect_item(document.assign_group.elements['selected_{$group_type}[]'])">
             </td>

            <td valign="middle" bgcolor="#cccccc" align="center">
             <br>
             <select name="selected_{$group_type}[]" tabindex="0" size="10" width="200" multiple>
				{html_options options=$options_selected_objects selected=$selected_object}
            </select>
            <br>
             </td>

          <tr>
            <td valign="top" bgcolor="#999999" rowspan="1" colspan="4">
              <div align="center">
                <input type="submit" name="action" value="Enviar"> <input type="reset" value="Deshacer"><br>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    <br>
    <table cellpadding="2" cellspacing="2" border="2" width="100%">
  <tr align="center">
	<td valign="top" colspan="4" bgcolor="#cccccc"><b>{$group_type|upper}s asignados al Grupo: {$group_name}</b><br>
	 </td>
  </tr>
  <tr>
    <td valign="top" colspan="11" bgcolor="#cccccc">
        {include file="phpgacl/pager.tpl" pager_data=$paging_data link="?group_type=$group_type&group_id=$group_id&"}
    </td>
  </tr>
  <tr>
	<td valign="top" align="center" bgcolor="#d3dce3"><b>Valor</b><br>
	 </td>

	<td valign="top" align="center" bgcolor="#d3dce3"><b>Secciones</b><br>
	 </td>

	<td valign="top" align="center" bgcolor="#d3dce3"><b>AROs</b><br>
	 </td>

	<td valign="top" align="center" bgcolor="#d3dce3"><b>Funciones</b><br>
	 </td>

  </tr>

    {section name=x loop=$rows}
  <tr>
    <td valign="top" bgcolor="#cccccc" align="center">
            {$rows[x].value}
     </td>

    <td valign="top" bgcolor="#cccccc" align="center">
        {$rows[x].section}
     </td>

    <td valign="top" bgcolor="#cccccc" align="center">
        {$rows[x].name}
     </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <input type="checkbox" name="delete_assigned_object[]" value="{$rows[x].section_value}^{$rows[x].value}">
     </td>

  </tr>
    {/section}
    <tr>
        <td valign="top" colspan="11" bgcolor="#cccccc">
            {include file="phpgacl/pager.tpl" pager_data=$paging_data link="?"}
        </td>
    </tr>
	  <tr>
		<td valign="top" bgcolor="#999999" colspan="3">
		</td>
		<td valign="top" bgcolor="#999999">
		  <div align="center">
			<input type="submit" name="action" value="Borrar">
		  </div>
		</td>
	</tr>

    </table>
<input type="hidden" name="group_id" value="{$group_id}">
<input type="hidden" name="group_type" value="{$group_type}">
<input type="hidden" name="return_page" value="{$return_page}">
</form>
{include file="phpgacl/footer.tpl"}
