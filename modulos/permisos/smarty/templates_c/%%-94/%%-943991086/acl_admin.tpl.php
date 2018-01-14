<?php /* Smarty version 2.3.0, created on 2006-08-02 12:04:44
         compiled from phpgacl/acl_admin.tpl */ ?>
<?php $this->_load_plugins(array(
array('function', 'html_options', 'phpgacl/acl_admin.tpl', 65, false),)); ?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("phpgacl/header.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <meta name="generator" content="HTML Tidy, see www.w3.org">
    <title>phpGACL Admin</title>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  </head>
<?php echo '
<style type="text/css">
        tr.hide {
        display: none;
        }
        table.hide {
        display: none;
        }

        td.tabon {
                background: #438EC5;
        }

        td.taboff {
                background: #ABC3D4;
        }
</style>
'; ?>


<script LANGUAGE="JavaScript">
<?php echo $this->_tpl_vars['js_aco_array']; ?>


<?php echo $this->_tpl_vars['js_aro_array']; ?>


<?php echo $this->_tpl_vars['js_axo_array']; ?>

</script>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("phpgacl/acl_admin_js.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<body onload="populate(document.acl_admin.aco_section,document.acl_admin.elements['aco[]'], '<?php echo $this->_tpl_vars['js_aco_array_name']; ?>
');populate(document.acl_admin.aro_section,document.acl_admin.elements['aro[]'], '<?php echo $this->_tpl_vars['js_aro_array_name']; ?>
')">
    <form method="post" name="acl_admin" action="acl_admin.php">
      <table cellpadding="2" cellspacing="2" border="0" width="100%">
        <tbody>
          <tr align="center">
            <td valign="top" colspan="4" bgcolor="#cccccc"><b>phpGACL - Administración de ACLs[ <a href="acl_list.php?return_page=<?php echo $this->_tpl_vars['return_page']; ?>
">Lista de ACLs</a> ] </b><br>
             </td>
          </tr>

          <tr>
            <td width="32%" valign="top" align="center" bgcolor="#d3dce3">&nbsp;<b>Secciones</b><br>
             </td>

            <td width="32%" valign="top" align="center" bgcolor="#d3dce3"><b>ACOs</b> <br>
             </td>

            <td width="4%" valign="top" align="center" bgcolor="#d3dce3">&nbsp;<br>
             </td>

            <td width="32%" valign="top" align="center" bgcolor="#d3dce3"><b>Seleccionados</b><br>
             </td>

          </tr>

          <tr>
            <td valign="middle" bgcolor="#cccccc" align="center">[ <a href="edit_object_sections.php?object_type=aco&return_page=<?php echo $this->_tpl_vars['return_page']; ?>
">Editar</a> ]<br>
             <br>
             &nbsp; <select name="aco_section" tabindex="0" size="10" width="200" onclick="populate(document.acl_admin.aco_section,document.acl_admin.elements['aco[]'], '<?php echo $this->_tpl_vars['js_aco_array_name']; ?>
')">
                <?php $this->_plugins['function']['html_options'][0](array('options' => $this->_tpl_vars['options_aco_sections'],'selected' => $this->_tpl_vars['aco_section_value']), $this); if($this->_extract) { extract($this->_tpl_vars); $this->_extract=false; } ?>
            </select> <br>
             </td>
            <td valign="middle" bgcolor="#cccccc" align="center">
            [ <a href="javascript: location.href = 'edit_objects.php?object_type=aco&section_value=' + document.acl_admin.aco_section.options[document.acl_admin.aco_section.selectedIndex].value + '&return_page=<?php echo $this->_tpl_vars['return_page']; ?>
';">Editar</a> ]<br>
             <br>
             <select name="aco[]" tabindex="0" size="10" width="200" multiple>
            </select>
            <br>
             </td>

            <td valign="middle" bgcolor="#cccccc" align="center">
                <input type="BUTTON" name="select" value="&nbsp;>>&nbsp;" onClick="select_item(document.acl_admin.aco_section, document.acl_admin.elements['aco[]'], document.acl_admin.elements['selected_aco[]'])">
                <br>
                <br>
                <input type="BUTTON" name="deselect" value="&nbsp;<<&nbsp;" onClick="deselect_item(document.acl_admin.elements['selected_aco[]'])">
             </td>

            <td valign="middle" bgcolor="#cccccc" align="center">
             <br>
             <select name="selected_aco[]" tabindex="0" size="10" width="200" multiple>
				<?php $this->_plugins['function']['html_options'][0](array('options' => $this->_tpl_vars['options_selected_aco'],'selected' => $this->_tpl_vars['selected_aco']), $this); if($this->_extract) { extract($this->_tpl_vars); $this->_extract=false; } ?>
            </select>
            <br>
             </td>

          </tr>

          <tr>
            <td valign="top" align="center" bgcolor="#d3dce3"><b>Secciones</b><br>
             </td>

            <td valign="top" align="center" bgcolor="#d3dce3"><b>AROs</b><br>
             </td>

            <td valign="top" align="center" bgcolor="#d3dce3">&nbsp;<br>
             </td>

            <td valign="top" align="center" bgcolor="#d3dce3"><b>Seleccionados</b><br>
             </td>
          </tr>

          <tr>
            <td valign="middle" bgcolor="#cccccc" align="center">[ <a href="edit_object_sections.php?object_type=aro&return_page=<?php echo $this->_tpl_vars['return_page']; ?>
">Editar</a> ]<br>
             <br>
             <select name="aro_section" tabindex="0" size="10" width="200" onclick="populate(document.acl_admin.aro_section,document.acl_admin.elements['aro[]'],'<?php echo $this->_tpl_vars['js_aro_array_name']; ?>
')">
                <?php $this->_plugins['function']['html_options'][0](array('options' => $this->_tpl_vars['options_aro_sections'],'selected' => $this->_tpl_vars['aro_section_value']), $this); if($this->_extract) { extract($this->_tpl_vars); $this->_extract=false; } ?>
            </select> <br>
             </td>

            <td valign="middle" bgcolor="#cccccc" align="center">
            [ <a href="javascript: location.href = 'edit_objects.php?object_type=aro&section_value=' + document.acl_admin.aro_section.options[document.acl_admin.aro_section.selectedIndex].value + '&return_page=<?php echo $this->_tpl_vars['return_page']; ?>
';">Editar</a> ]
             <br>
             <select name="aro[]" tabindex="0" size="10" width="200" multiple>
            </select> <br>
             </td>

            <td valign="middle" bgcolor="#cccccc" align="center">
                <input type="BUTTON" name="select" value="&nbsp;>>&nbsp;" onClick="select_item(document.acl_admin.aro_section, document.acl_admin.elements['aro[]'], document.acl_admin.elements['selected_aro[]'])">
                <br>
                <br>
                <input type="BUTTON" name="deselect" value="&nbsp;<<&nbsp;" onClick="deselect_item(document.acl_admin.elements['selected_aro[]'])">
             </td>

            <td valign="middle" bgcolor="#cccccc" align="center">
             <br>
             <select name="selected_aro[]" tabindex="0" size="10" width="200" multiple>
				<?php $this->_plugins['function']['html_options'][0](array('options' => $this->_tpl_vars['options_selected_aro'],'selected' => $this->_tpl_vars['selected_aro']), $this); if($this->_extract) { extract($this->_tpl_vars); $this->_extract=false; } ?>
            </select>
            <br>
             </td>
          </tr>

          <tr>
            <td valign="top" align="center" bgcolor="#d3dce3"><b>Acceso</b><br>
            </td>
            <td valign="top" align="center" bgcolor="#d3dce3"><b>Grupos</b><br>
             </td>
            <td colspan="2" valign="top" align="center" bgcolor="#d3dce3">&nbsp;<br>
            </td>
          </tr>
          <tr>
            <td valign="middle" bgcolor="#cccccc">
              <div align="center">
                <input type="radio" name="allow" value="1" <?php if ($allow==1): ?>checked<?php endif; ?>>Permitido<br>
                 <input type="radio" name="allow" value="0" <?php if ($allow==0): ?>checked<?php endif; ?>>Denegado<br>
                <br>
                <br>
                 <input type="checkbox" name="enabled" value="1" <?php if ($enabled==1): ?>checked<?php endif; ?>>Habilitado
              </div>
            </td>
            <td valign="middle" bgcolor="#cccccc" align="center">
				[ <a href="group_admin.php?group_type=aro&return_page=<?php echo $this->_tpl_vars['SCRIPT_NAME']; ?>
?action=<?php echo $this->_tpl_vars['action']; ?>
&acl_id=<?php echo $this->_tpl_vars['acl_id']; ?>
">Editar</a> ]<br>
				 <br>
				 <select name="aro_groups[]" tabindex="0" multiple>
					<?php $this->_plugins['function']['html_options'][0](array('options' => $this->_tpl_vars['options_aro_groups'],'selected' => $this->_tpl_vars['selected_aro_groups']), $this); if($this->_extract) { extract($this->_tpl_vars); $this->_extract=false; } ?>
				</select>
				<br>
				<br>
				<input type="BUTTON" name="Un-Select" value="Deseleccionar" onClick="unselect_all(document.acl_admin.elements['aro_groups[]'])">
            </td>
            <td colspan="2" valign="middle" bgcolor="#cccccc" align="center">&nbsp;
            </td>
          </tr>
          <tr>
            <td valign="top" align="right" bgcolor="#d3dce3" rowspan="1" colspan="1">
                <b>Valor de retorno:</b>
            </td>
            <td valign="top" align="left" bgcolor="#cccccc" rowspan="1" colspan="3">
                <input type="text" name="return_value" size="50" value="<?php echo $this->_tpl_vars['return_value']; ?>
">
            </td>
          </tr>

          <tr>
            <td valign="top" align="right" bgcolor="#d3dce3" rowspan="1" colspan="1">
                <b>Nota:</b>
            </td>
            <td valign="top" align="left" bgcolor="#cccccc" rowspan="1" colspan="3">
                <textarea name="note" rows="2" cols="50"><?php echo $this->_tpl_vars['note']; ?>
</textarea>
            </td>
          </tr>
          <tr>
            <td valign="top" bgcolor="#999999" rowspan="1" colspan="4">
              <div align="center">
                <input type="submit" name="action" value="Enviar"> <input type="reset" value="Deshacer"><br>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
	<input type="hidden" name="acl_id" value="<?php echo $this->_tpl_vars['acl_id']; ?>
">
	<input type="hidden" name="return_page" value="<?php echo $this->_tpl_vars['return_page']; ?>
">
    </form>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("phpgacl/footer.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
