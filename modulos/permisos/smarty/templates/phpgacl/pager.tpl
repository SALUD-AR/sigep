<table width="100%" cellspacing="2" cellpadding="2" border="0">
        <tr>
                <td width="40" bgcolor="#cccccc" align="left">
                        <div align="left">
						{if $paging_data.atfirstpage}
							|&lt; &lt;&lt;
						{else}
							<a href="{$link}page=1">|&lt;</a>
							<a href="{$link}page={$paging_data.prevpage}">&lt;&lt;</a>
						{/if}
                </td>
                <td bgcolor="#cccccc">
					<br>
                </td>
                <td width="40" bgcolor="#cccccc" align="right">
						{if $paging_data.atlastpage}
							&gt;&gt; &gt;|
						{else}
							<a href="{$link}page={$paging_data.nextpage}">&gt;&gt;</a>
							<a href="{$link}page={$paging_data.lastpageno}">&gt;|</a>
						{/if}
                </td>
        </tr>
</table>
