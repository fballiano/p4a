<div class="tab_pane" id="<?php echo $this->getId()?>" <?php echo $this->composeStringProperties()?>>

<ul class="tabs">
	<?php foreach ($tabs as $tab): ?>
	<li><a href="#" <?php echo $tab['actions']?> <?php if ($tab['active']): ?>class="active"<?php endif; ?>><?php echo $tab['label']?></a></li>
	<?php endforeach; ?>
</ul>

<div class="tab_pane_page" style="<?php echo $tab_pane_height?>">
	<?php echo $active_page?>
</div>

</div>