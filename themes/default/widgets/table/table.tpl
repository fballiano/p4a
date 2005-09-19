<div class="border_box table_container" flexy:raw="{table_properties:h}">
	<table class="table" style="width: {table_width}" >
		<caption flexy:if="title">{title}</caption>
		<thead flexy:if="headers">
			<tr>
				<th class="select">&nbsp;</th>
				{foreach:headers,header}
				<th flexy:if="header[action]" class="font3 align_center clickable" flexy:raw="{header[properties]:h}" flexy:raw="{header[action]:h}">
					<img flexy:if="header[order]" style="float:right;padding:2px;" src="{theme_path}/widgets/table/images/{header[order]}.gif" alt="<?php print $t->i18n[$header['order'].'ending']; ?>" />
					<img flexy:if="!header[order]" style="float:right;padding:2px;" src="{theme_path}/widgets/table/images/spacer.gif" alt="" />
					<a href="#" flexy:raw="{header[action]:h}">{header[value]}</a>
				</th>
				<th flexy:if="!header[action]" class="font3 align_center" flexy:raw="{header[properties]:h}">
					<img flexy:if="header[order]" style="float:right;padding:2px;" src="{theme_path}/widgets/table/images/{header[order]}.gif" alt="<?php print $t->i18n[$header['order'].'ending']; ?>" />
					<img flexy:if="!header[order]" style="float:right;padding:2px;" src="{theme_path}/widgets/table/images/spacer.gif" alt="" />
					{header[value]}
				</th>
				{end:}
			</tr>
		</thead>
		
		<tbody flexy:raw="{table_rows_properties:h}" class="overflow" flexy:if="table_rows">
			<tr flexy:foreach="table_rows,row">
			    <td>
					<img flexy:if="row[row][active]" src="{theme_path}/widgets/table/images/select.gif" width="18" height="15" alt="{i18n[selected]}" />
					<img flexy:if="!row[row][active]" src="{theme_path}/widgets/table/images/spacer.gif" width="18" height="15" alt="" />
			    </td>

				{foreach:row[cells],cell}
				<td flexy:if="cell[row_even]" class="background1 clickable table_cell" flexy:raw="{cell[action]:h}"><a href="#" flexy:raw="{cell[action]:h}">{cell[value]}</a></td>
				<td flexy:if="!cell[row_even]" class="background2 clickable table_cell" flexy:raw="{cell[action]:h}"><a href="#" flexy:raw="{cell[action]:h}">{cell[value]}</a></td>
				{end:}
			</tr>
		</tbody>

	</table>
 	{navigation_bar:h}
</div>