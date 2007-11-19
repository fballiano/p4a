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

<style type="text/css">

body {
 font-family: sans-serif;
}

#p4a-footer {
 text-align: center;
 font-size: 80%;
 padding: 5px;
}

#p4a-header {
 padding: 5px;
 text-align: center;
}

#p4a-main-region {

}

#p4a-main-form {
 height:100%;
}

.p4a_frame {
 margin: auto;
}

</style>

<script type="text/javascript">

Ext.BLANK_IMAGE_URL = '<?php echo P4A_THEME_PATH ?>/extjs/resources/images/default/s.gif';

Ext.onReady(function() {
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
	Ext.QuickTips.init();
	
	viewport = new Ext.P4AViewport({
		layout:'border',
		items: [
			{
				region: 'center',
				id: 'p4a-main-region',
				autoScroll: true,
				items: [<?php echo $main; ?>]
			},
			{region: 'north', tbar:<?php echo $menu; ?>},
			{region: 'west', html:'ciao', split:true},
			{region: 'south', html: '<div id="p4a-footer">Powered by P4A - PHP For Applications <?php echo P4A_VERSION ?></div>'}
		]
	});
});
</script>
</head>
<body>
	<?php echo $this->maskOpen() ?>
	<?php echo $this->maskClose() ?>
</body>
</html>