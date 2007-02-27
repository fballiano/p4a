<div class="border_box table_container" id="<?php echo $this->getId()?>" <?php echo $this->composeStringProperties()?>>
	<table class="table" style="width: <?php echo $table_width?>" >
		<?php if ($this->getTitle()): ?>
		<caption><?php echo $this->getTitle()?></caption>
		<?php endif; ?>

		<col class="select" />
		<?php foreach ($table_cols as $col): ?>
		<col <?php echo $col['properties']?> />
		<?php endforeach; ?>

		<?php if (@$headers): ?>
		<thead>
			<tr>
				<th>&nbsp;</th>
				<?php foreach ($headers as $header): ?>
				<?php if ($header['action']): ?>
					<th class="font3 align_center clickable">
						<?php if ($header['order']): ?>
							<img style="float:right;padding:2px;" src="<?php echo P4A_THEME_PATH?>/widgets/table/images/<?php echo $header['order']?>.gif" alt="<?php echo $p4a->i18n->messages->get($header['order'].'ending')?>" />
						<?php else: ?>
							<img style="float:right;padding:2px;" src="<?php echo P4A_THEME_PATH?>/widgets/table/images/spacer.gif" alt="" />
						<?php endif; ?>
						<a href="#" <?php echo $header['action']?>><?php echo $header['value']?></a>
					</th>
				<?php else: ?>
					<th class="font3 align_center">
						<?php if ($header['order']): ?>
							<img style="float:right;padding:2px;" src="<?php echo P4A_THEME_PATH?>/widgets/table/images/<?php echo $header['order']?>.gif" alt="<?php echo $p4a->i18n->messages->get($header['order'].'ending')?>" />
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
		<tbody <?php echo $this->rows->composeStringProperties()?> class="overflow">
			<?php $i = 0; ?>
			<?php foreach ($table_rows as $row): ?>
				<?php $i++; ?>
				<tr>
				    <td>
				    	<?php if ($row['row']['active']): ?>
							<img src="<?php echo P4A_THEME_PATH?>/widgets/table/images/select.gif" width="18" height="15" alt="<?php echo $p4a->i18n->messages->get('selected')?>" />
						<?php else: ?>
							<img src="<?php echo P4A_THEME_PATH?>/widgets/table/images/spacer.gif" width="18" height="15" alt="" />
						<?php endif; ?>
				    </td>

					<?php foreach ($row['cells'] as $cell): ?>
						<td class="background<?php echo ($i%2)+1?> table_cell<?php echo $cell['clickable']?> <?php echo $cell['type']?>"><?php if ($cell['clickable']): ?><a href="#" <?php echo $cell['action']?>><?php echo $cell['value']?></a><?php else: ?><?php echo $cell['value']?><?php endif; ?></td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<?php endif; ?>
	</table>&nbsp;
 	<?php if (isset($navigation_bar)) echo $navigation_bar ?>
</div>