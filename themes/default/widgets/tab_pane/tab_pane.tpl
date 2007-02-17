<div class="tab_pane" id="<?=$this->getId()?>" <?=$this->composeStringProperties()?>>

<ul class="tabs">
	<?php foreach ($tabs as $tab): ?>
	<li><a href="#" <?=$tab['actions']?> <?php if ($tab['active']): ?>class="active"<?php endif; ?>><?=$tab['label']?></a></li>
	<?php endforeach; ?>
</ul>

<div class="tab_pane_page" style="<?=$tab_pane_height?>">
	<?=$active_page?>
</div>

</div>