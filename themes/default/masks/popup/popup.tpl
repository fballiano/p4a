<a href="#" onClick="hidePopup()" id="popupCloseHandler"><img src="<?php echo P4A_ICONS_PATH ?>/32/exit.png" /></a>

<?php if (strlen($_title)): ?>
<h2><?php echo $_title?></h2>
<?php endif; ?>

<?php if (isset($main)): ?>
<div id="sheetContainerPopup">
	<?php echo $main?>
</div>
<?php endif; ?>