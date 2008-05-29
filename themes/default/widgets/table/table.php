<table id="<?php echo $this->getId() ?>" <?php echo $this->composeStringClass() ?> <?php echo $this->composeStringProperties() ?>>
	<?php if ($this->getLabel()): ?>
	<caption><?php echo $this->getLabel() ?></caption>
	<?php endif; ?>

	<col class="select" />
	<?php foreach ($table_cols as $col): ?>
	<col <?php echo $col['properties'] ?> />
	<?php endforeach; ?>

	<?php if (@$headers): ?>
	<thead>
		<tr>
			<th class="p4a_row_indicator">&nbsp;</th>
			<?php foreach ($headers as $header): ?>
			<?php if ($header['action']): ?>
				<th>
					<?php if ($header['order'] == 'asc'): ?>
						<div style="float:right">&#x25BC;</div>
					<?php elseif ($header['order'] == 'desc'): ?>
						<div style="float:right">&#x25B2;</div>
					<?php endif; ?>
					<a href="#" <?php echo $header['action']?>><?php echo $header['value']?></a>
				</th>
			<?php else: ?>
				<th>
					<?php if ($header['order'] == 'asc'): ?>
						<div style="float:right">&#x25BC;</div>
					<?php elseif ($header['order'] == 'desc'): ?>
						<div style="float:right">&#x25B2;</div>
					<?php endif; ?>
					<?php echo $header['value']?>
				</th>
			<?php endif; ?>
			<?php endforeach; ?>
		</tr>
	</thead>
	<?php endif; ?>

	<?php if (!empty($table_rows)): ?>
	<tbody <?php echo $this->rows->composeStringProperties()?> <?php echo $this->rows->composeStringClass()?>>
		<?php $i = 0; ?>
		<?php foreach ($table_rows as $row): ?>
			<?php $i++; ?>
			<tr>
			    <th class="p4a_row_indicator">
			    	<?php if ($row['row']['active']): ?>
						&#x25BA;
					<?php else: ?>
						&nbsp;
					<?php endif; ?>
			    </th>

				<?php foreach ($row['cells'] as $cell): ?>
					<td class="p4a_table_rows<?php echo ($i%2)+1?> <?php echo $cell['type']?>"><?php if ($cell['clickable']): ?><a href="#" <?php echo $cell['action']?>><?php echo $cell['value']?></a><?php else: ?><?php echo $cell['value']?><?php endif; ?></td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<?php endif; ?>
	<?php if (isset($navigation_bar)): ?>
		<tr><th colspan='99' class="navigation_bar"><?php echo $navigation_bar ?></th></tr>
	<?php endif; ?>
</table>