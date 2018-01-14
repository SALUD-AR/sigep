<?php /* Smarty version 2.3.0, created on 2006-07-06 15:02:35
         compiled from phpgacl/pager.tpl */ ?>
<table width="100%" cellspacing="2" cellpadding="2" border="0">
        <tr>
                <td width="40" bgcolor="#cccccc" align="left">
                        <div align="left">
						<?php if ($this->_tpl_vars['paging_data']['atfirstpage']): ?>
							|&lt; &lt;&lt;
						<?php else: ?>
							<a href="<?php echo $this->_tpl_vars['link']; ?>
page=1">|&lt;</a>
							<a href="<?php echo $this->_tpl_vars['link']; ?>
page=<?php echo $this->_tpl_vars['paging_data']['prevpage']; ?>
">&lt;&lt;</a>
						<?php endif; ?>
                </td>
                <td bgcolor="#cccccc">
					<br>
                </td>
                <td width="40" bgcolor="#cccccc" align="right">
						<?php if ($this->_tpl_vars['paging_data']['atlastpage']): ?>
							&gt;&gt; &gt;|
						<?php else: ?>
							<a href="<?php echo $this->_tpl_vars['link']; ?>
page=<?php echo $this->_tpl_vars['paging_data']['nextpage']; ?>
">&gt;&gt;</a>
							<a href="<?php echo $this->_tpl_vars['link']; ?>
page=<?php echo $this->_tpl_vars['paging_data']['lastpageno']; ?>
">&gt;|</a>
						<?php endif; ?>
                </td>
        </tr>
</table>