<?php /* Smarty version 2.3.0, created on 2006-08-02 10:40:08
         compiled from phpgacl/assign_group.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'upper', 'phpgacl/assign_group.tpl', 19, false),
array('function', 'html_options', 'phpgacl/assign_group.tpl', 41, false),)); ?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("phpgacl/header.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <meta name="generator" content="HTML Tidy, see www.w3.org">
    <title>phpGACL Admin</title>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  </head>

<script LANGUAGE="JavaScript">
<?php echo $this->_tpl_vars['js_array']; ?>

</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("phpgacl/acl_admin_js.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

  <body onload="populate(document.assign_group.<?php echo $this->_tpl_vars['group_type']; ?>
_section,document.assign_group.elements['objects[]'], '<?php echo $this->_tpl_vars['js_array_name']; ?>
')">
    <form method="post" name="assign_group" action="assign_group.php">
      <table cellpadding="2" cellspacing="2" border="2" width="100%">
        <tbody>
          <tr align="center">
            <td valign="top" rowspan="1" colspan="4" bgcolor="#cccccc"><b>phpGACL - Asignar <?php echo $this->_run_mod_handler('upper', true, $this->_tpl_vars['group_type']); ?>
s [ <a href="group_admin.php?group_type=<?php echo $this->_tpl_vars['group_type']; ?>
">Admin. Grupos <?php echo $this->_run_mod_handler('upper', true, $this->_tpl_vars['group_type']); ?>
</a> ] </b><br>
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
            <td valign="middle" bgcolor="#cccccc" align="center">[ <a href="edit_object_sections.php?object_type=<?php echo $this->_tpl_vars['group_type']; ?>
&return_page=<?php echo $this->_tpl_vars['return_page']; ?>
">Editar</a> ]<br>
             <br>
             <select name="<?php echo $this->_tpl_vars['group_type']; ?>
_section" tabindex="0" size="10" width="200" onclick="populate(document.assign_group.<?php echo $this->_tpl_vars['group_type']; ?>
_section,document.assign_group.elements['objects[]'],'<?php echo $this->_tpl_vars['js_array_name']; ?>
')">
                <?php $this->_plugins['function']['html_options'][0](array('options' => $this->_tpl_vars['options_sections'],'selected' => $this->_tpl_vars['section_value']), $this); if($this->_extract) { extract($this->_tpl_vars); $this->_extract=false; } ?>
            </select> <br>
             </td>

            <td valign="middle" bgcolor="#cccccc" align="center">
            [ <a href="javascript: location.href = 'edit_objects.php?object_type=<?php echo $this->_tpl_vars['group_type']; ?>
&section_value=' + document.assign_group.aro_section.options[document.assign_group.aro_section.selectedIndex].value + '&return_page=<?php echo $this->_tpl_vars['return_page']; ?>
';">Editar</a> ]
             <br>
             <select name="objects[]" tabindex="0" size="10" width="200" multiple>
            </select> <br>
             </td>

            <td valign="middle" bgcolor="#cccccc" align="center">
                <input type="BUTTON" name="select" value="&nbsp;>>&nbsp;" onClick="select_item(document.assign_group.<?php echo $this->_tpl_vars['group_type']; ?>
_section, document.assign_group.elements['objects[]'], document.assign_group.elements['selected_<?php echo $this->_tpl_vars['group_type']; ?>
[]'])">
                <br>
                <br>
                <input type="BUTTON" name="deselect" value="&nbsp;<<&nbsp;" onClick="deselect_item(document.assign_group.elements['selected_<?php echo $this->_tpl_vars['group_type']; ?>
[]'])">
             </td>

            <td valign="middle" bgcolor="#cccccc" align="center">
             <br>
             <select name="selected_<?php echo $this->_tpl_vars['group_type']; ?>
[]" tabindex="0" size="10" width="200" multiple>
				<?php $this->_plugins['function']['html_options'][0](array('options' => $this->_tpl_vars['options_selected_objects'],'selected' => $this->_tpl_vars['selected_object']), $this); if($this->_extract) { extract($this->_tpl_vars); $this->_extract=false; } ?>
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
	<td valign="top" colspan="4" bgcolor="#cccccc"><b><?php echo $this->_run_mod_handler('upper', true, $this->_tpl_vars['group_type']); ?>
s asignados al Grupo: <?php echo $this->_tpl_vars['group_name']; ?>
</b><br>
	 </td>
  </tr>
  <tr>
    <td valign="top" colspan="11" bgcolor="#cccccc">
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("phpgacl/pager.tpl", array('pager_data' => $this->_tpl_vars['paging_data'],'link' => "?group_type=$group_type&group_id=$group_id&"));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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

    <?php if (isset($this->_sections["x"])) unset($this->_sections["x"]);
$this->_sections["x"]['name'] = "x";
$this->_sections["x"]['loop'] = is_array($this->_tpl_vars['rows']) ? count($this->_tpl_vars['rows']) : max(0, (int)$this->_tpl_vars['rows']);
$this->_sections["x"]['show'] = true;
$this->_sections["x"]['max'] = $this->_sections["x"]['loop'];
$this->_sections["x"]['step'] = 1;
$this->_sections["x"]['start'] = $this->_sections["x"]['step'] > 0 ? 0 : $this->_sections["x"]['loop']-1;
if ($this->_sections["x"]['show']) {
    $this->_sections["x"]['total'] = $this->_sections["x"]['loop'];
    if ($this->_sections["x"]['total'] == 0)
        $this->_sections["x"]['show'] = false;
} else
    $this->_sections["x"]['total'] = 0;
if ($this->_sections["x"]['show']):

            for ($this->_sections["x"]['index'] = $this->_sections["x"]['start'], $this->_sections["x"]['iteration'] = 1;
                 $this->_sections["x"]['iteration'] <= $this->_sections["x"]['total'];
                 $this->_sections["x"]['index'] += $this->_sections["x"]['step'], $this->_sections["x"]['iteration']++):
$this->_sections["x"]['rownum'] = $this->_sections["x"]['iteration'];
$this->_sections["x"]['index_prev'] = $this->_sections["x"]['index'] - $this->_sections["x"]['step'];
$this->_sections["x"]['index_next'] = $this->_sections["x"]['index'] + $this->_sections["x"]['step'];
$this->_sections["x"]['first']      = ($this->_sections["x"]['iteration'] == 1);
$this->_sections["x"]['last']       = ($this->_sections["x"]['iteration'] == $this->_sections["x"]['total']);
?>
  <tr>
    <td valign="top" bgcolor="#cccccc" align="center">
            <?php echo $this->_tpl_vars['rows'][$this->_sections['x']['index']]['value']; ?>

     </td>

    <td valign="top" bgcolor="#cccccc" align="center">
        <?php echo $this->_tpl_vars['rows'][$this->_sections['x']['index']]['section']; ?>

     </td>

    <td valign="top" bgcolor="#cccccc" align="center">
        <?php echo $this->_tpl_vars['rows'][$this->_sections['x']['index']]['name']; ?>

     </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <input type="checkbox" name="delete_assigned_object[]" value="<?php echo $this->_tpl_vars['rows'][$this->_sections['x']['index']]['section_value']; ?>
^<?php echo $this->_tpl_vars['rows'][$this->_sections['x']['index']]['value']; ?>
">
     </td>

  </tr>
    <?php endfor; endif; ?>
    <tr>
        <td valign="top" colspan="11" bgcolor="#cccccc">
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("phpgacl/pager.tpl", array('pager_data' => $this->_tpl_vars['paging_data'],'link' => "?"));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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
<input type="hidden" name="group_id" value="<?php echo $this->_tpl_vars['group_id']; ?>
">
<input type="hidden" name="group_type" value="<?php echo $this->_tpl_vars['group_type']; ?>
">
<input type="hidden" name="return_page" value="<?php echo $this->_tpl_vars['return_page']; ?>
">
</form>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("phpgacl/footer.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>