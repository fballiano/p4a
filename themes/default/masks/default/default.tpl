<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $_charset?>" />
<title><?php echo $_title?></title>

<?php foreach ($_javascript as $_k=>$_v): ?>
<script type="text/javascript" src="<?php echo $_k?>"></script>
<?php endforeach; ?>

<?php foreach ($_css as $_url=>$_media): ?>
<link href="<?php echo $_url?>" rel="stylesheet" type="text/css" media="<?php echo join(', ', array_keys($_media))?>"></link>
<?php endforeach; ?>

<script type="text/javascript">

Ext.BLANK_IMAGE_URL = '<?php echo P4A_THEME_PATH ?>/extjs/resources/images/default/s.gif';

Ext.onReady(function() {
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
	Ext.QuickTips.init();
	
	<?php echo $main->getAsString() ?>
	
	new Ext.P4AViewport({
		layout:'border',
		items: [
			{
				region: 'center',
				id: 'p4a-main-region',
				autoScroll: true,
				items: [<?php echo $main->getId() ?>]
			},
			{region: 'north', tbar:<?php echo $menu->getAsString() ?>, margins:'0 0 5 0', border: false, height: 1},
			{region: 'west', html:'west region', split:true, margins:'0 0 0 5', width: 200},
			//{region: 'east', html:'east region', split:true, margins:'0 5 0 0', width: 200},
			new Ext.BoxComponent({region: 'south', el: 'p4a-footer'})
		]
	});

	Ext.get('<?php echo $main->getId() ?>').applyStyles('margin-top:10px');
});
</script>
</head>
<body>
	<?php echo $this->maskOpen() ?>
	<?php echo $this->maskClose() ?>
	<div id="p4a-footer">
		Powered by <a href="http://p4a.sourceforge.net">P4A - PHP For Applications</a>
		<?php echo P4A_VERSION ?>
	</div>
</body>
</html>