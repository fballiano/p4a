<table cellpadding="2" cellspacing="0" [[$table_properties]]>
	[[if $title_bar]]
	<thead>
	<tr>
		<td class="border_box background_box font4 align_left">&nbsp;[[$title_bar]]</td>
	</tr>
	</thead>
	[[/if]]

	[[if $expand]]

		[[if $toolbar]]
	<tr>
		<td class="border_box align_left">[[$toolbar]]</td>
	</tr>
		[[/if]]
		[[if (( $table_rows or $row_headers ) and ( $table_cols ))]]
	<tr>
		<td class="border_box" align="center" [[$table_properties]]><table border="0" style="padding-bottom:10px; padding-right:15px;" width="100%">
				[[if $headers]]
				<thead>
				<tr>
				    <td>&nbsp;</td>
					[[foreach from=$headers item=header]]
				    <td class="font3 align_center clickable" [[$header.properties]] [[$header.action]]>[[$header.value]]</td>
				    <td width="20" class="align_right clickable" [[$header.action]]><img src="[[$tpl_path]]/images/[[if $header.order]][[$header.order|lower]][[else]]spacer[[/if]].gif" border="0" /></td>
					[[/foreach]]
				</tr>
				</thead>
				[[/if]]
				[[if $table_rows]]
				<tbody [[$table_rows_properties]] class="overflow">
					[[foreach from=$table_rows item=row]]
				<tr>
			        	[[if $row.row.active]]
			        <td width="19"><img src="[[$tpl_path]]/images/select.gif" width="18" height="15" /></td>
			        	[[else]]
			        <td width="19"><img src="[[$tpl_path]]/images/spacer.gif" width="18" height="15" /></td>
			        	[[/if]]
			        	[[foreach from=$row.cells item=cell]]
			        		[[if $cell.row_number is odd]]
			        <td colspan="2" class="background1 clickable table_cell" [[$cell.action]]>[[$cell.value]]</td>
				        	[[else]]
					<td colspan="2" class="background2 clickable table_cell" [[$cell.action]]>[[$cell.value]]</td>
				        	[[/if]]
				        [[/foreach]]
				</tr>
					[[/foreach]]
				</tbody>
				[[/if]]
			</table></td>
	</tr>
		[[/if]]
		[[if $navigation_bar]]
    <tr>
        <td class="border_box background_box font4 no_print" align="right">[[$navigation_bar]]</td>
	</tr>
		[[/if]]
	[[/if]]
</table>