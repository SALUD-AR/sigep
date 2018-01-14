<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

{include file="phpgacl/header.tpl"}
    <meta name="generator" content="HTML Tidy, see www.w3.org">
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  </head>
  <body>
    <form method="post" name="edit_group" action="edit_group.php">
      <table cellpadding="2" cellspacing="2" border="2" width="100%">
        <tbody>
          <tr align="center">
            <td valign="top" colspan="3" bgcolor="#cccccc"><b>phpGACL - Administración de Grupos {$group_type|upper}</b>
            <b>[ <a href="group_admin.php?group_type={$group_type}&return_page={$return_page}">Grupos {$group_type|upper}</a> ] </b>
            <br>
             </td>
          </tr>
          <tr>
            <td valign="top" bgcolor="#d3dce3" align="center"><b>ID</b> </td>
            <td valign="top" bgcolor="#d3dce3" align="center"><b>Padre</b> </td>
            <td valign="top" bgcolor="#d3dce3" align="center"><b>Nombre</b> </td>
          </tr>
          <tr>
            <td valign="top" bgcolor="#cccccc" align="center">
                    {$id|default:"N/A"}
            </td>

            <td valign="top" bgcolor="#cccccc" align="center">
                <select name="parent_id" tabindex="0" multiple>
                    {html_options options=$options_groups selected=$parent_id}
                </select>
             </td>

            <td valign="top" bgcolor="#cccccc" align="center">
                <input type="text" size="50" name="name" value="{$name}">
             </td>

          </tr>

          <tr>
            <td valign="top" bgcolor="#999999" colspan="3">
              <div align="center">
                <input type="submit" name="action" value="Enviar"> <input type="reset" value="Deshacer"><br>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    <input type="hidden" name="group_id" value="{$id}">
    <input type="hidden" name="group_type" value="{$group_type}">
    <input type="hidden" name="return_page" value="{$return_page}">

    </form>
  </body>
{include file="phpgacl/footer.tpl"}

