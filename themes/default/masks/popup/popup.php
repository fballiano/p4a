<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title><?php echo $_title?></title>

<?php foreach ($_javascript as $_k=>$_v): ?>
<script type="text/javascript" src="<?php echo $_k?>"></script>
<?php endforeach; ?>

<?php foreach ($_css as $_url=>$_media): ?>
<link href="<?php echo $_url?>" rel="stylesheet" type="text/css" media="<?php echo join(', ', array_keys($_media))?>"></link>
<?php endforeach; ?>

<?php echo $this->getP4AJavascript() ?>
</head>

<body class='p4a_browser_<?php echo P4A::singleton()->getBrowser() ?> p4a_popup'>
<div id='p4a_body' class='p4a_browser_<?php echo P4A::singleton()->getBrowserOS() ?>'>
<div id='p4a_loading'><img src='<?php echo P4A_ICONS_PATH?>/loading.gif' alt='' /> Loading... </div>
<div class='p4a_system_messages'>
	<?php foreach (P4A::singleton()->getRenderedMessages() as $message): ?>
	<div class='p4a_system_message'><?php echo $message ?></div>
	<?php endforeach; ?>
</div>
<?php echo $this->maskOpen()?>

<!-- MAIN  -->
<div id="p4a_main_container">
	<div id="p4a_main_inner_container" style='width:<?php echo $this->frame->getWidth() ?>'>
		<h2>
			<a style="float:right" href="#" <?php echo $this->close_popup_button->composeStringActions() ?> id="p4a_popup_close_handler"><img src="<?php echo P4A_ICONS_PATH ?>/32/exit.png" /></a>
			<?php echo P4A_Generate_Widget_Layout_Table($_icon, $_title) ?>
		</h2>
	
		<?php if (isset($main)) echo $main ?>
	</div>

	<!-- The following line is a copyright note, you've to keep it as is, it's a small price for P4A -->
	<div id="p4a_footer">Powered by <a href="http://p4a.sourceforge.net/welcome">P4A - PHP For Applications</a> <?php echo P4A_VERSION?></div>
</div>

<?php echo $this->maskClose() ?>
</div>

</body>
</html>