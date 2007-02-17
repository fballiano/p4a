<div class="border_box table_container" id="<?=$this->getId()?>" <?=$this->composeStringProperties()?>>
	<table class="table" style="width: <?=$table_width?>" >
		<?php if ($this->getTitle()): ?>
		<caption><?=$this->getTitle()?></caption>
		<?php endif; ?>

		<col class="select" />
		<?php foreach ($table_cols as $col): ?>
		<col <?=$col['properties']?> />
		<?php endforeach; ?>

		<thead flexy:if="headers">
			<tr>
				<th>&nbsp;</th>
				<?php foreach ($headers as $header): ?>
				<?php if ($header['action']): ?>
					<th class="font3 align_center clickable">
						<?php if ($header['order']): ?>
							<img style="float:right;padding:2px;" src="<?=P4A_THEME_PATH?>/widgets/table/images/<?=$header['order']?>.gif" alt="<?=$p4a->i18n->messages->get($header['order'].'ending')?>" />
						<?php else: ?>
							<img style="float:right;padding:2px;" src="<?=P4A_THEME_PATH?>/widgets/table/images/spacer.gif" alt="" />
						<?php endif; ?>
						<a href="#" <?=$header['action']?>><?=$header['value']?></a>
					</th>
				<?php else: ?>
					<th class="font3 align_center">
						<?php if ($header['order']): ?>
							<img style="float:right;padding:2px;" src="<?=P4A_THEME_PATH?>/widgets/table/images/<?=$header['order']?>.gif" alt="<?=$p4a->i18n->messages->get($header['order'].'ending')?>" />
						<?php else: ?>
							<img style="float:right;padding:2px;" src="<?=P4A_THEME_PATH?>/widgets/table/images/spacer.gif" alt="" />
						<?php endif; ?>
						<?=$header['value']?>
					</th>
				<?php endif; ?>
				<?php endforeach; ?>
			</tr>
		</thead>

		<?php if (!empty($table_rows)): ?>
		<tbody <?=$this->rows->composeStringProperties()?> class="overflow">
			<?php $i = 0; ?>
			<?php foreach ($table_rows as $row): ?>
				<?php $i++; ?>
				<tr>
				    <td>
				    	<?php if ($row['row']['active']): ?>
							<img src="<?=P4A_THEME_PATH?>/widgets/table/images/select.gif" width="18" height="15" alt="<?=$p4a->i18n->messages->get('selected')?>" />
						<?php else: ?>
							<img src="<?=P4A_THEME_PATH?>/widgets/table/images/spacer.gif" width="18" height="15" alt="" />
						<?php endif; ?>
				    </td>

					<?php foreach ($row['cells'] as $cell): ?>
						<td class="background<?=($i%2)+1?> table_cell<?=$cell['clickable']?> <?=$cell['type']?>" <?=$cell['action']?>><?php if ($cell['clickable']): ?><a href="#" <?=$cell['action']?>><?=$cell['value']?></a><?php else: ?><?=$cell['value']?><?php endif; ?></td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<?php endif; ?>
	</table>
 	<?php if (isset($navigation_bar)) echo $navigation_bar ?>
</div>