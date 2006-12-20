<?xml version="1.0" encoding="{charset}"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset={charset}" />
<title>{application_title}</title>
<script type="text/javascript" src="{k}" flexy:foreach="javascript,k,v"></script>

{foreach:css,url,medias}
<link href="{url}" rel="stylesheet" type="text/css" media="{media}" flexy:foreach="medias,media,item"></link>
{end:}

<style type="text/css" media="screen">
#mainContainer {
<?php
	if (isset($t->menu) and isset($t->top)) {
		$t->top_margin = 70;
	} elseif (isset($t->menu)) {
		$t->top_margin = 25;
	} elseif (isset($t->top)) {
		$t->top_margin = 45;
	} else {
		$t->top_margin = 0;
	}
?>
	top: {top_margin}px;
}
</style>
</head>

<body onload="setFocus('{focus_id}');hideLoading();">
<div id='body'>
<div id='p4a_loading'><img src='{icons_path}/loading.gif' alt='' /> Loading... </div>
{mask_open:h}

<div id="sidebar" class="border_color4 background_box" flexy:if="sidebar">
	{sidebar:h}
</div>

<!-- TOP -->
<div id="topContainer">
	<div id="menu" flexy:if="menu">
		{menu:h}
		<div class="br"></div>
	</div>

	<div id="top" flexy:if="top">
		{top:h}
	</div>
</div>

<!-- MAIN  -->
<div id="mainContainer">
	<div flexy:if="title"><h2>{title}</h2></div>

	<div id="sheetContainer" flexy:if="main">
		{main:h}
	</div>

	<!-- The following line is a copyright note, you've to keep it as is, we think it's a small price for P4A. -->
	<div id="footerContainer">Powered by <a href="http://p4a.sourceforge.net/welcome">P4A - PHP For Applications</a></div>
</div>

<!-- POPUP -->
<div id="overlay" style="display:none"></div>
<div style="display:block"><div id="popup" style="display:none">{popup:h}</div></div>

{mask_close:h}
</div>

{if:popup}
<script type="text/javascript">
showPopup();
</script>
{end:}
</body>
</html>