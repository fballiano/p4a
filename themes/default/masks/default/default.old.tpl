<?xml version="1.0" encoding="{_temp_vars[charset]}"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset={_temp_vars[charset]}" />
<title>{application_title}</title>
<script type="text/javascript" src="{k}" flexy:foreach="_temp_vars[javascript],k,v"></script>

{foreach:_temp_vars[css],url,medias}
<link href="{url}" rel="stylesheet" type="text/css" media="{media}" flexy:foreach="medias,media,item"></link>
{end:}

<style type="text/css" media="screen">
#mainContainer {
{if:_tpl_vars[menu]}
{if:_tpl_vars[top]}
	top: 70px;
{else:}
	top: 25px;
{end:}
{else:}
{if:_tpl_vars[top]}
	top: 45px;
{else:}
	top: 0px;
{end:}
{end:}
}
</style>
</head>

<body onload="setFocus('{focus_object.getID()}')">

{maskOpen():h}

<div id="sidebar" class="border_color4 background_box" flexy:if="_tpl_vars[sidebar]">
	{_tpl_vars[sidebar]:h}
</div>

<!-- TOP -->
<div id="topContainer">
	<div id="menu" flexy:if="_tpl_vars[menu]">
		{_tpl_vars[menu].getAsString():h}
	</div>
	
	<div id="top" flexy:if="_tpl_vars[top]">
		{_tpl_vars[top].getAsString():h}
	</div>
</div>

<!-- MAIN  -->
<div id="mainContainer">
	<div flexy:if="getTitle()"><h2>{getTitle()}</h2></div>

	<div id="sheetContainer" flexy:if="_tpl_vars[main]">
		{_tpl_vars[main].getAsString():h}
	</div>

	<!-- Please leave our links, can you think it's a small price for p4a? :-)) -->
	<div id="footerContainer">Powered by <a href="http://p4a.sourceforge.net">P4A - PHP For Applications</a></div>
</div>

{maskClose():h}
</body>
</html>