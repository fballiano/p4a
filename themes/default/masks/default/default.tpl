<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=[[$charset]]" />
<title>[[$application_title]]</title>

[[foreach from=$javascript key=k item=v]]
<script type="text/javascript" src="[[$k]]"></script>
[[/foreach]]

[[foreach from=$css key=url item=medias]]
[[foreach from=$medias key=media item=item]]
<link href="[[$url]]" rel="stylesheet" type="text/css" media="[[$media]]"></link>
[[/foreach]]
[[/foreach]]

<style type="text/css" media="screen">
[[if isset($menu) and isset($top)]]
	[[assign var="toppx" value="70"]]
[[elseif isset($menu)]]
	[[assign var="toppx" value="25"]]
[[elseif isset($top)]]
	[[assign var="toppx" value="45"]]
[[else]]
	[[assign var="toppx" value="0"]]
[[/if]]

#mainContainer {
	top: [[$toppx]]px;
}
</style>
</head>

[[if $focus_id]]
<body onload="setFocus('[[$focus_id]]')">
[[else]]
<body>
[[/if]]

[[$mask_open]]

[[if isset($sidebar)]]
<div id="sidebar" class="border_color4 background_box">
[[$sidebar]]
</div>
[[/if]]

<!-- TOP -->
<div id="topContainer">
[[if isset($menu)]]
<div id="menu">[[$menu]]</div>
[[/if]]

[[if $top]]
<div id="top">
[[$top]]
</div>
[[/if]]
</div>

<!-- MAIN  -->
<div id="mainContainer">

<!-- TITLE -->
[[if $title]]
<div><h2>[[$title]]</h2></div>
[[/if]]

<!-- SHEET -->
<div id="sheetContainer">
[[$main]]
</div>

<!-- Please leave our links, can you think it's a small price for p4a? :-)) -->
<div id="footerContainer">Powered by <a href="http://p4a.sourceforge.net">P4A - PHP For Applications</a></div>
</div>

[[$mask_close]]
</body>
</html>