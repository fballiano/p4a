<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=[[$charset]]" />
<title>[[$application_title]]</title>

<script type="text/javascript" src="[[$tpl_path]]/p4a.js"></script>
[[foreach from=$css key=k item=v ]]
<link href="[[$v.0]]" rel="stylesheet" type="text/css" media="[[$v.1]]" />
[[/foreach]]

<style type="text/css">
    [[assign var="toppx" value="0"]]
    [[if isset($menu)]]
	     [[assign var="toppx" value="25"]]
    [[/if]]
    [[if isset($top)]]
	     [[assign var="toppx" value="55"]]
    [[/if]]
    [[if isset($menu) and isset($top)]]
	     [[assign var="toppx" value="80"]]
    [[/if]]

	body {
		height: 100%;
		width:100%;
		padding:0px;
		margin:0px;
		border:0px;
	}


	#sheetContainer>.sheet,#sheetContainer>fieldset{
		margin-left:auto;
		margin-right:auto;
	}

	#topContainer {
		width:100%;
		position:fixed;
		top: 0px;
		z-index:2;
	}

	#mainContainer {
		width:100%;
		position:absolute;
		text-align:center;
	}

	#footerContainer {
		clear: both;
		text-align: center;
		margin-top: 10px;
	}

	@media screen {
		#mainContainer {
			top:[[$toppx]]px;
		}
	}

	@media print {
		#mainContainer {
			top:0px;
		}

		#topContainer {
			visibility:hidden;
		}
	}
</style>
</head>

[[if $focus_id]]
<body onload="setFocus('[[$focus_id]]'); correctPNG();">
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
			[[$menu]]
		[[/if]]
		[[if $top]]
		<div style="background-color:#FAFAFA; border-bottom: 1px solid #CCC;padding:2px">
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

		<div id="footerContainer">Powered by <a href="http://p4a.sourceforge.net">P4A - PHP For Applications</a></div>
	</div>

[[$mask_close]]
</body>
</html>