<?php /* Smarty version 2.3.0, created on 2006-07-06 15:02:35
         compiled from phpgacl/acl_list.tpl */ ?>
<?php $this->_load_plugins(array(
array('function', 'html_options', 'phpgacl/acl_list.tpl', 67, false),)); ?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("phpgacl/header.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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
        <input type="text" name="filter_aco_section_name" size="15" value="<?php echo $this->_tpl_vars['filter_aco_section_name']; ?>
">
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <input type="text" name="filter_aro_section_name" size="15" value="<?php echo $this->_tpl_vars['filter_aro_section_name']; ?>
">
    </td>
    <td rowspan="2" valign="middle" bgcolor="#cccccc" align="center">
        <input type="text" name="filter_aro_group_name" size="15" value="<?php echo $this->_tpl_vars['filter_aro_group_name']; ?>
">
    </td>
    <td rowspan="2" valign="middle" bgcolor="#cccccc" align="center">
        <input type="text" name="filter_return_value" size="8" value="<?php echo $this->_tpl_vars['filter_return_value']; ?>
">
    </td>
    <td rowspan="2" valign="middle" bgcolor="#cccccc" align="center">
		 <select name="filter_allow" tabindex="0">
			<?php $this->_plugins['function']['html_options'][0](array('options' => $this->_tpl_vars['options_filter_allow'],'selected' => $this->_tpl_vars['filter_allow']), $this); if($this->_extract) { extract($this->_tpl_vars); $this->_extract=false; } ?>
		</select>
    </td>
    <td rowspan="2" valign="middle" bgcolor="#cccccc" align="center">
		 <select name="filter_enabled" tabindex="0">
			<?php $this->_plugins['function']['html_options'][0](array('options' => $this->_tpl_vars['options_filter_enabled'],'selected' => $this->_tpl_vars['filter_enabled']), $this); if($this->_extract) { extract($this->_tpl_vars); $this->_extract=false; } ?>
		</select>
    </td>
  </tr>
  <tr>
    <td valign="middle" bgcolor="#cccccc" align="center">
        <b>Objeto</b>
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <input type="text" name="filter_aco_name" size="15" value="<?php echo $this->_tpl_vars['filter_aco_name']; ?>
">
    </td>
    <td valign="top" bgcolor="#cccccc" align="center">
        <input type="text" name="filter_aro_name" size="15" value="<?php echo $this->_tpl_vars['filter_aro_name']; ?>
">
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
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("phpgacl/pager.tpl", array('pager_data' => $this->_tpl_vars['paging_data'],'link' => "?filter_aco_section_name=$filter_aco_section_name&filter_aco_name=$filter_aco_name&filter_aro_section_name=$filter_aro_section_name&filter_aro_name=$filter_aro_name&filter_axo_section_name=$filter_axo_section_name&filter_axo_name=$filter_axo_name&filter_aro_group_name=$filter_aro_group_name&filter_axo_group_name=$filter_axo_group_name&filter_return_value=$filter_return_value&filter_allow=$filter_allow&filter_enabled=$filter_enabled&"));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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

    <?php if (isset($this->_sections["x"])) unset($this->_sections["x"]);
$this->_sections["x"]['name'] = "x";
$this->_sections["x"]['loop'] = is_array($this->_tpl_vars['acls']) ? count($this->_tpl_vars['acls']) : max(0, (int)$this->_tpl_vars['acls']);
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
    <td valign="middle" rowspan="2" bgcolor="#cccccc" align="center">
            <?php echo $this->_tpl_vars['acls'][$this->_sections['x']['index']]['id']; ?>

    </td>

    <td valign="top" bgcolor="#cccccc" align="left" nowrap>
       <?php if (isset($this->_sections["y"])) unset($this->_sections["y"]);
$this->_sections["y"]['name'] = "y";
$this->_sections["y"]['loop'] = is_array($this->_tpl_vars['acls'][$this->_sections['x']['index']]['aco']) ? count($this->_tpl_vars['acls'][$this->_sections['x']['index']]['aco']) : max(0, (int)$this->_tpl_vars['acls'][$this->_sections['x']['index']]['aco']);
$this->_sections["y"]['show'] = true;
$this->_sections["y"]['max'] = $this->_sections["y"]['loop'];
$this->_sections["y"]['step'] = 1;
$this->_sections["y"]['start'] = $this->_sections["y"]['step'] > 0 ? 0 : $this->_sections["y"]['loop']-1;
if ($this->_sections["y"]['show']) {
    $this->_sections["y"]['total'] = $this->_sections["y"]['loop'];
    if ($this->_sections["y"]['total'] == 0)
        $this->_sections["y"]['show'] = false;
} else
    $this->_sections["y"]['total'] = 0;
if ($this->_sections["y"]['show']):

            for ($this->_sections["y"]['index'] = $this->_sections["y"]['start'], $this->_sections["y"]['iteration'] = 1;
                 $this->_sections["y"]['iteration'] <= $this->_sections["y"]['total'];
                 $this->_sections["y"]['index'] += $this->_sections["y"]['step'], $this->_sections["y"]['iteration']++):
$this->_sections["y"]['rownum'] = $this->_sections["y"]['iteration'];
$this->_sections["y"]['index_prev'] = $this->_sections["y"]['index'] - $this->_sections["y"]['step'];
$this->_sections["y"]['index_next'] = $this->_sections["y"]['index'] + $this->_sections["y"]['step'];
$this->_sections["y"]['first']      = ($this->_sections["y"]['iteration'] == 1);
$this->_sections["y"]['last']       = ($this->_sections["y"]['iteration'] == $this->_sections["y"]['total']);
?>
			<b><?php echo $this->_sections['y']['iteration']; ?>
.</b> <?php echo $this->_tpl_vars['acls'][$this->_sections['x']['index']]['aco'][$this->_sections['y']['index']]['aco']; ?>

			<br>
		<?php endfor; endif; ?>
       <br>
    </td>

    <td valign="top" bgcolor="#cccccc" align="left" nowrap>
       <?php if (isset($this->_sections["y"])) unset($this->_sections["y"]);
$this->_sections["y"]['name'] = "y";
$this->_sections["y"]['loop'] = is_array($this->_tpl_vars['acls'][$this->_sections['x']['index']]['aro']) ? count($this->_tpl_vars['acls'][$this->_sections['x']['index']]['aro']) : max(0, (int)$this->_tpl_vars['acls'][$this->_sections['x']['index']]['aro']);
$this->_sections["y"]['show'] = true;
$this->_sections["y"]['max'] = $this->_sections["y"]['loop'];
$this->_sections["y"]['step'] = 1;
$this->_sections["y"]['start'] = $this->_sections["y"]['step'] > 0 ? 0 : $this->_sections["y"]['loop']-1;
if ($this->_sections["y"]['show']) {
    $this->_sections["y"]['total'] = $this->_sections["y"]['loop'];
    if ($this->_sections["y"]['total'] == 0)
        $this->_sections["y"]['show'] = false;
} else
    $this->_sections["y"]['total'] = 0;
if ($this->_sections["y"]['show']):

            for ($this->_sections["y"]['index'] = $this->_sections["y"]['start'], $this->_sections["y"]['iteration'] = 1;
                 $this->_sections["y"]['iteration'] <= $this->_sections["y"]['total'];
                 $this->_sections["y"]['index'] += $this->_sections["y"]['step'], $this->_sections["y"]['iteration']++):
$this->_sections["y"]['rownum'] = $this->_sections["y"]['iteration'];
$this->_sections["y"]['index_prev'] = $this->_sections["y"]['index'] - $this->_sections["y"]['step'];
$this->_sections["y"]['index_next'] = $this->_sections["y"]['index'] + $this->_sections["y"]['step'];
$this->_sections["y"]['first']      = ($this->_sections["y"]['iteration'] == 1);
$this->_sections["y"]['last']       = ($this->_sections["y"]['iteration'] == $this->_sections["y"]['total']);
?>
			<b><?php echo $this->_sections['y']['iteration']; ?>
.</b> <?php echo $this->_tpl_vars['acls'][$this->_sections['x']['index']]['aro'][$this->_sections['y']['index']]['aro']; ?>

			<br>
		<?php endfor; endif; ?>
       <br>
    </td>
    <td valign="top" bgcolor="#cccccc" align="left" nowrap>
       <?php if (isset($this->_sections["y"])) unset($this->_sections["y"]);
$this->_sections["y"]['name'] = "y";
$this->_sections["y"]['loop'] = is_array($this->_tpl_vars['acls'][$this->_sections['x']['index']]['aro_groups']) ? count($this->_tpl_vars['acls'][$this->_sections['x']['index']]['aro_groups']) : max(0, (int)$this->_tpl_vars['acls'][$this->_sections['x']['index']]['aro_groups']);
$this->_sections["y"]['show'] = true;
$this->_sections["y"]['max'] = $this->_sections["y"]['loop'];
$this->_sections["y"]['step'] = 1;
$this->_sections["y"]['start'] = $this->_sections["y"]['step'] > 0 ? 0 : $this->_sections["y"]['loop']-1;
if ($this->_sections["y"]['show']) {
    $this->_sections["y"]['total'] = $this->_sections["y"]['loop'];
    if ($this->_sections["y"]['total'] == 0)
        $this->_sections["y"]['show'] = false;
} else
    $this->_sections["y"]['total'] = 0;
if ($this->_sections["y"]['show']):

            for ($this->_sections["y"]['index'] = $this->_sections["y"]['start'], $this->_sections["y"]['iteration'] = 1;
                 $this->_sections["y"]['iteration'] <= $this->_sections["y"]['total'];
                 $this->_sections["y"]['index'] += $this->_sections["y"]['step'], $this->_sections["y"]['iteration']++):
$this->_sections["y"]['rownum'] = $this->_sections["y"]['iteration'];
$this->_sections["y"]['index_prev'] = $this->_sections["y"]['index'] - $this->_sections["y"]['step'];
$this->_sections["y"]['index_next'] = $this->_sections["y"]['index'] + $this->_sections["y"]['step'];
$this->_sections["y"]['first']      = ($this->_sections["y"]['iteration'] == 1);
$this->_sections["y"]['last']       = ($this->_sections["y"]['iteration'] == $this->_sections["y"]['total']);
?>
			<b><?php echo $this->_sections['y']['iteration']; ?>
.</b> <?php echo $this->_tpl_vars['acls'][$this->_sections['x']['index']]['aro_groups'][$this->_sections['y']['index']]['group']; ?>

			<br>
		<?php endfor; endif; ?>
       <br>
    </td>

    <td valign="middle" bgcolor="#cccccc" align="center">
        <?php echo $this->_tpl_vars['acls'][$this->_sections['x']['index']]['return_value']; ?>
<br>
    </td>
    <td valign="middle" bgcolor="<?php if ($this->_tpl_vars['acls'][$this->_sections['x']['index']]['allow']): ?>green<?php else: ?>red<?php endif; ?>" align="center">
		<?php if ($this->_tpl_vars['acls'][$this->_sections['x']['index']]['allow']): ?>
			PERMITIDO
		<?php else: ?>
			DENEGADO
		<?php endif; ?>
    </td>
    <td valign="middle" bgcolor="<?php if ($this->_tpl_vars['acls'][$this->_sections['x']['index']]['enabled']): ?>green<?php else: ?>red<?php endif; ?>" align="center">
		<?php if ($this->_tpl_vars['acls'][$this->_sections['x']['index']]['enabled']): ?>
			Sí
		<?php else: ?>
			No
		<?php endif; ?>
    </td>
    <td valign="middle" bgcolor="#cccccc" align="center">
        <?php echo $this->_tpl_vars['acls'][$this->_sections['x']['index']]['updated_date']; ?>

     </td>
    <td valign="middle" rowspan="2" bgcolor="#cccccc" align="center">
        [ <a href="acl_admin.php?action=edit&acl_id=<?php echo $this->_tpl_vars['acls'][$this->_sections['x']['index']]['id']; ?>
&return_page=<?php echo $this->_tpl_vars['return_page']; ?>
">Editar</a> ]
        <input type="checkbox" name="delete_acl[]" value="<?php echo $this->_tpl_vars['acls'][$this->_sections['x']['index']]['id']; ?>
">
    </td>
  </tr>
  <tr>
    <td valign="middle" colspan="7" bgcolor="#cccccc" align="left">
        <b>Nota:</b> <?php echo $this->_tpl_vars['acls'][$this->_sections['x']['index']]['note']; ?>
<br>
    </td>
  </tr>
    <?php endfor; endif; ?>
  <tr>
	<td valign="top" colspan="9" bgcolor="#cccccc">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("phpgacl/pager.tpl", array('pager_data' => $this->_tpl_vars['paging_data'],'link' => "?filter_aco_section_name=$filter_aco_section_name&filter_aco_name=$filter_aco_name&filter_aro_section_name=$filter_aro_section_name&filter_aro_name=$filter_aro_name&filter_axo_section_name=$filter_axo_section_name&filter_axo_name=$filter_axo_name&filter_aro_group_name=$filter_aro_group_name&filter_axo_group_name=$filter_axo_group_name&filter_return_value=$filter_return_value&filter_allow=$filter_allow&filter_enabled=$filter_enabled&"));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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
    <input type="hidden" name="return_page" value="<?php echo $this->_tpl_vars['return_page']; ?>
">
</form>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("phpgacl/footer.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
