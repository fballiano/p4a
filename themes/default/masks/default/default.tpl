<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=[[$charset]]">
    <title>[[$application_title]]</title>

<script type="text/javascript">
function correctPNG() // correctly handle PNG transparency in Win IE 5.5 or higher.
   {
   var agt = navigator.userAgent.toLowerCase();
   var is_ie = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
   if (!is_ie) return;

   for(var i=0; i<document.images.length; i++)
      {
          var img = document.images[i]
          var imgName = img.src.toUpperCase()
          if (imgName.substring(imgName.length-3, imgName.length) == "PNG")
             {
                 var imgID = (img.id) ? "id='" + img.id + "' " : ""
                 var imgClass = (img.className) ? "class='" + img.className + "' " : ""
                 var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' "
                 var imgStyle = "display:inline-block;" + img.style.cssText
                 var imgAttribs = img.attributes;
                 for (var j=0; j<imgAttribs.length; j++)
                        {
                        var imgAttrib = imgAttribs[j];
                        if (imgAttrib.nodeName == "align")
                           {
                           if (imgAttrib.nodeValue == "left") imgStyle = "float:left;" + imgStyle
                           if (imgAttrib.nodeValue == "right") imgStyle = "float:right;" + imgStyle
                           break
                           }
            }
                 var strNewHTML = "<span " + imgID + imgClass + imgTitle
                 strNewHTML += " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";"
             strNewHTML += "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
                 strNewHTML += "(src=\'" + img.src + "\', sizingMethod='image');\" ></span>"
                 img.outerHTML = strNewHTML
                 i = i-1
             }
      }
   }
</script>


    <link href="[[$theme_path]]/screen.css" rel="stylesheet" type="text/css" media="all">
    <link href="[[$theme_path]]/print.css" rel="stylesheet" type="text/css" media="print">

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
  <body onLoad="setFocus('[[$focus_id]]'); correctPNG();">
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