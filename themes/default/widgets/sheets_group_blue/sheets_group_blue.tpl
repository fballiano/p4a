<table cellspacing="0" cellpadding="0" border="0" class="border border_color3" [[$properties]]>
[[if $type != 'modal']]
<tr>
<td align="left" style="height:19px; border-bottom:1px solid" class="border_color3" bgcolor="#F0F4FA"><table border="0" cellspacing="0" cellpadding="0"><tr>
	[[foreach item=item from=$items]]
	[[if $item.visible]]
		[[if $item.active]]
		<td height="19" nowrap="nowrap" bgcolor="#CFE2FB" class="font4">&nbsp;[[$item.label]]&nbsp;</td>
		<td height="19" background="[[$tpl_path]]/images/selected.gif"><img src="[[$tpl_path]]/images/spacer.gif" width="8" height="1"></td>
		[[else]]
		<td height="19" nowrap="nowrap" bgcolor="#F0F4FA" class="font4 no_print">&nbsp;[[$item.label]]&nbsp;</td>
			[[if $item.next_active]]
		<td height="19" background="[[$tpl_path]]/images/normal-selected.gif"><img src="[[$tpl_path]]/images/spacer.gif" width="8" height="1"></td>
			[[else]]
		<td height="19" background="[[$tpl_path]]/images/normal-normal.gif"><img src="[[$tpl_path]]/images/spacer.gif" width="8" height="1"></td>
			[[/if]]
		[[/if]]
	[[/if]]
	[[/foreach]]
	</tr></table>
</td>
<td bgcolor="#F0F4FA" align="right" style="height:19px; border-bottom:1px solid" class="border_color3">
	[[if $navigation_bar_visible]]
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td width="100%">&nbsp;</td>
			<td>[[$button_first]]</td>
			<td>[[$button_prev]]</td>
			<td>[[$button_next]]</td>
			<td>[[$button_last]]</td>
		</tr>
	</table>
	[[else]]
		&nbsp;
	[[/if]]
</td>
</tr>
[[/if]]
<tr>
	<td height="100%" valign="top" align="left" colspan="2" style="padding-left:10px;padding-right:10px;padding-top:10px;padding-bottom:10px;">[[$sheet]]</td>
</tr>
</table>