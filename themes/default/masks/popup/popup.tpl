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
	<div flexy:if="title"><h2>{title}</h2></div>

	<div id="sheetContainerPopup" flexy:if="main">
		{main:h}
	</div>
</div>

</div>