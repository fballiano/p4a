<div class="border_box table_container" [[$table_properties]]>	
	<table class="table" >
		
		[[if $title]]
		<caption>
			[[$title]]
		</caption>
		[[/if]]
		
		[[if $headers]]
		<thead>
			<tr>
				<th>&nbsp;</th>
				[[foreach from=$headers item=header]]
				<th class="font3 align_center clickable" [[$header.properties]] [[$header.action]]><img style="float:right;padding:2px;" src="[[$tpl_path]]/images/[[if $header.order]][[$header.order|lower]][[else]]spacer[[/if]].gif" border="0" />
				[[$header.value]]
				</th>
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
			    <td class="background1 clickable table_cell" [[$cell.action]]>[[$cell.value]]</td>
					[[else]]
				<td class="background2 clickable table_cell" [[$cell.action]]>[[$cell.value]]</td>
				    [[/if]]
				[[/foreach]]
			</tr>
			[[/foreach]]
		</tbody>
		[[/if]]
		
	</table>
	[[if $navigation_bar]]
 		[[$navigation_bar]]
	[[/if]]
</div>