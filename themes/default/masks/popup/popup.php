<div id="p4a_main_container" class="p4a_popup">
	<div id="p4a_main_inner_container" style='width:<?php echo $this->frame->getWidth() ?>'>
		<h2>
			<a style="float:right" href="#" <?php echo $this->close_popup_button->composeStringActions() ?> id="p4a_popup_close_handler"><img src="<?php echo P4A_ICONS_PATH ?>/32/exit.png" /></a>
			<?php echo P4A_Generate_Widget_Layout_Table($_icon, $_title) ?>
		</h2>
	
		<?php if (isset($main)) echo $main ?>
	</div>

	<!-- The following line is a copyright note, you've to keep it as is, it's a small price for P4A -->
	<div id="p4a_footer">Powered by <a href="http://p4a.sourceforge.net/welcome">P4A - PHP For Applications</a> <?php echo P4A_VERSION?></div>
</div>