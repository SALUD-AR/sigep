<?php /* Smarty version 2.3.0, created on 2006-08-02 10:38:00
         compiled from phpgacl/group_admin.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'upper', 'phpgacl/group_admin.tpl', 12, false),)); ?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("phpgacl/header.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <meta name="generator" content="HTML Tidy, see www.w3.org">
    <title>phpGACL Admin</title>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  </head>
  <body>
    <form method="post" name="edit_group" action="edit_group.php">
      <table align="center" cellpadding="2" cellspacing="2" border="2" width="100%">
        <tbody>
          <tr align="center">
            <td valign="top" colspan="4" bgcolor="#cccccc"><b>phpGACL - Administración de grupos <?php echo $this->_run_mod_handler('upper', true, $this->_tpl_vars['group_type']); ?>
 [ <a href="acl_list.php">Lista de ACLs</a> ]</b><br>
             </td>
          </tr>

          <tr>
            <td valign="top" bgcolor="#d3dce3" align="center"><b>ID</b> </td>
            <td valign="top" bgcolor="#d3dce3" align="center"><b>Nombre</b> </td>
            <td valign="top" bgcolor="#d3dce3" align="center"><b>Objetos</b> </td>
            <td valign="top" bgcolor="#d3dce3" align="center"><b>Funciones</b> </td>
          </tr>
            <?php if (isset($this->_sections["x"])) unset($this->_sections["x"]);
$this->_sections["x"]['name'] = "x";
$this->_sections["x"]['loop'] = is_array($this->_tpl_vars['groups']) ? count($this->_tpl_vars['groups']) : max(0, (int)$this->_tpl_vars['groups']);
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
                    <?php echo $this->_tpl_vars['groups'][$this->_sections['x']['index']]['id']; ?>

             </td>
            <td valign="top" bgcolor="#cccccc" align="left">
                    <?php echo $this->_tpl_vars['groups'][$this->_sections['x']['index']]['name']; ?>

             </td>
            <td valign="top" bgcolor="#cccccc" align="center">
                    <?php echo $this->_tpl_vars['groups'][$this->_sections['x']['index']]['object_count']; ?>

             </td>
            <td valign="top" bgcolor="#cccccc" align="center">
                [ <a href="assign_group.php?group_type=<?php echo $this->_tpl_vars['group_type']; ?>
&group_id=<?php echo $this->_tpl_vars['groups'][$this->_sections['x']['index']]['id']; ?>
&return_page=<?php echo $this->_tpl_vars['return_page']; ?>
">Asignar <?php echo $this->_run_mod_handler('upper', true, $this->_tpl_vars['group_type']); ?>
</a> ]
                [ <a href="edit_group.php?group_type=<?php echo $this->_tpl_vars['group_type']; ?>
&parent_id=<?php echo $this->_tpl_vars['groups'][$this->_sections['x']['index']]['id']; ?>
&return_page=<?php echo $this->_tpl_vars['return_page']; ?>
">Agregar Hijo</a> ]
                [ <a href="edit_group.php?group_type=<?php echo $this->_tpl_vars['group_type']; ?>
&group_id=<?php echo $this->_tpl_vars['groups'][$this->_sections['x']['index']]['id']; ?>
&return_page=<?php echo $this->_tpl_vars['return_page']; ?>
">Editar</a> ]
                <input type="checkbox" name="delete_group[]" value="<?php echo $this->_tpl_vars['groups'][$this->_sections['x']['index']]['id']; ?>
">
             </td>

          </tr>
            <?php endfor; endif; ?>

          <tr>
            <td valign="top" bgcolor="#999999" colspan="3">
                &nbsp;
            </td>
            <td valign="top" bgcolor="#999999">
              <div align="center">
                <input type="submit" name="action" value="Agregar">
                <input type="submit" name="action" value="Borrar">
              </div>
            </td>

          </tr>
        </tbody>
      </table>
    <input type="hidden" name="group_type" value="<?php echo $this->_tpl_vars['group_type']; ?>
">
    <input type="hidden" name="return_page" value="<?php echo $this->_tpl_vars['return_page']; ?>
">
    </form>
  </body>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("phpgacl/footer.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
