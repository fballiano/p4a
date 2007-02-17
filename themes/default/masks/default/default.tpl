<?=$_xml_header?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=<?=$_charset?>" />
<title><?=$_title?></title>

<?php foreach ($_javascript as $_k=>$_v): ?>
<script type="text/javascript" src="<?=$_k?>"></script>
<?php endforeach; ?>

<?php foreach ($_css as $_url=>$_media): ?>
<link href="<?=$_url?>" rel="stylesheet" type="text/css" media="<?=join(', ', array_keys($_media))?>"></link>
<?php endforeach; ?>

<style type="text/css" media="screen">
#mainContainer {
<?php
	if (isset($menu) and isset($top)) {
		$_top_margin = 70;
	} elseif (isset($menu)) {
		$_top_margin = 25;
	} elseif (isset($top)) {
		$_top_margin = 45;
	} else {
		$_top_margin = 0;
	}
?>
	top: <?=$_top_margin?>px;
}
</style>
</head>

<body onload="setFocus('<?=$_focus_id?>');hideLoading();">
<div id='body'>
<div id='p4a_loading'><img src='<?=P4A_ICONS_PATH?>/loading.gif' alt='' /> Loading... </div>
<?=$this->maskOpen()?>

<?php if (isset($sidebar)): ?>
<div id="sidebar" class="border_color4 background_box">
	<?=$sidebar?>
</div>
<?php endif; ?>

<!-- TOP -->
<div id="topContainer">
	<?php if (isset($menu)): ?>
	<div id="menu">
		<?=$menu?>
		<div class="br"></div>
	</div>
	<?php endif; ?>

	<?php if (isset($top)): ?>
	<div id="top" flexy:if="top">
		<?=$top?>
	</div>
	<?php endif; ?>
</div>

<!-- MAIN  -->
<div id="mainContainer">
	<?php if (strlen($_title)): ?>
	<h2><?=$_title?></h2>
	<?php endif; ?>

	<?php if (isset($main)): ?>
	<div id="sheetContainer">
		<?=$main?>
	</div>
	<?php endif; ?>

	<!-- The following line is a copyright note, you've to keep it as is, we think it's a small price for P4A. -->
	<div id="footerContainer">Powered by <a href="http://p4a.sourceforge.net/welcome">P4A - PHP For Applications</a></div>
</div>

<!-- POPUP -->
<div id="overlay" style="display:none"></div>
<div style="display:block"><div id="popup" style="display:none"><?=$_popup?></div></div>

<?=$this->maskClose()?>
</div>

<?php if (strlen($_popup)): ?>
<script type="text/javascript">
showPopup();
</script>
<?php endif; ?>

</body>
</html>