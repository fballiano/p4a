<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=[[$charset]]">
    <title>[[$application_title]]</title>
    <script src="[[$root_path]]/js/pngfix.js"></script>
    <link href="[[$theme_path]]/screen.css" rel="stylesheet" type="text/css" media="all">
    <link href="[[$theme_path]]/print.css" rel="stylesheet" type="text/css" media="print">
<!--	<style type="text/css">
		@import url("[[$theme_path]]/screen.css") all;
		@import url("[[$theme_path]]/print.css") print;
	</style>-->
	
    [[foreach from=$css key=uri item=media ]]
    <link href="[[$uri]]" rel="stylesheet" type="text/css" media="[[$media]]">
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


      #sheetContainer>.sheet{
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

      #titleContaineer {
      		text-align:left;
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
  <body onLoad="setFocus('[[$focus_id]]')">
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
		<div id="titleContainer">
			<h2>[[$title]]</h2>
		</div>
		[[/if]]

		<!-- SHEET -->
		<div id="sheetContainer">
		[[$main]]
		</div>

	</div>

  	[[$mask_close]]
  </body>
</html>