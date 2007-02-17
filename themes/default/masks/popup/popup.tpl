<div id='bodyPopup'>

<!-- TOP -->
<!-- COMMENTED AT THE MOMENT, NEED SOME WORK
<div id="topContainerPopup">
	<div id="menuPopup" flexy:if="menu">
		{menu:h}
		<div class="br"></div>
	</div>

	<div id="topPopup" flexy:if="top">
		{top:h}
	</div>
</div>
-->
<!-- MAIN  -->
<div id="mainContainerPopup">

	<?php if (strlen($_title)): ?>
	<div><h2><?=$_title?></h2></div>
	<?php endif; ?>

	<?php if (isset($main)): ?>
	<div id="sheetContainerPopup" flexy:if="main">
		<?=$main?>
	</div>
	<?php endif; ?>
</div>

</div>