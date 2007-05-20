<div id='bodyPopup'>

	<div id="mainContainerPopup">

		<?php if (strlen($_title)): ?>
		<div><h2><?php echo $_title?></h2></div>
		<?php endif; ?>

		<?php if (isset($main)): ?>
		<div id="sheetContainerPopup">
			<?php echo $main?>
		</div>
		<?php endif; ?>

	</div>

</div>