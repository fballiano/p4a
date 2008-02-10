<table id="<?php echo $this->getId() ?>" <?php echo $this->composeStringClass() . $this->composeStringProperties() ?> >
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
			<th>&nbsp;</th>
			<?php foreach ($headers as $header): ?>
			<?php if ($header['action']): ?>
				<th>
					<?php if ($header['order']): ?>
						<img style="float:right;padding:2px;" src="<?php echo P4A_THEME_PATH?>/widgets/table/images/<?php echo $header['order']?>.gif" alt="<?php echo __(ucfirst($header['order']).'ending')?>" />
					<?php else: ?>
						<img style="float:right;padding:2px;" src="<?php echo P4A_THEME_PATH?>/widgets/table/images/spacer.gif" alt="" />
					<?php endif; ?>
					<a href="#" <?php echo $header['action']?>><?php echo $header['value']?></a>
				</th>
			<?php else: ?>
				<th>
					<?php if ($header['order']): ?>
						<img style="float:right;padding:2px;" src="<?php echo P4A_THEME_PATH?>/widgets/table/images/<?php echo $header['order']?>.gif" alt="<?php echo __(ucfirst($header['order']).'ending')?>" />
					<?php else: ?>
						<img style="float:right;padding:2px;" src="<?php echo P4A_THEME_PATH?>/widgets/table/images/spacer.gif" alt="" />
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
			    <th width="19">
			    	<?php if ($row['row']['active']): ?>
						<img src="<?php echo P4A_THEME_PATH?>/widgets/table/images/select.gif" width="18" height="15" alt="<?php echo __('Selected')?>" />
					<?php else: ?>
						<img src="<?php echo P4A_THEME_PATH?>/widgets/table/images/spacer.gif" width="18" height="15" alt="" />
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