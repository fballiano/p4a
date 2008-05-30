<script type="text/javascript">

p4a_load_js('<?php echo P4A_THEME_PATH ?>/widgets/rich_textarea/fckeditor.js', function () {
	var rte = new FCKeditor('<?php echo $this->getId() ?>input', '<?php echo $this->getWidth() ?>', '<?php echo $this->getHeight() ?>', '<?php echo $this->getRichTextareaTheme() ?>');
	rte.BasePath = '<?php echo P4A_THEME_PATH ?>/widgets/rich_textarea/';
	
	rte.Config['P4ACustomCSS'] = "<style type='text/css'>.TB_Button_On,.TB_Button_Off,.TB_Button_Disabled{border-color:<?php echo P4A_THEME_BG ?>}.SC_FieldCaption,.TB_ToolbarSet{background-color:<?php echo P4A_THEME_BG ?>}</style>";
	rte.Config['CustomConfigurationsPath'] = '<?php echo P4A_THEME_PATH ?>/widgets/rich_textarea/p4aconfig.js';
	rte.Config['DefaultLanguage'] = '<?php echo P4A::singleton()->i18n->getLanguage() ?>';
	
	<?php if ($this->isUploadEnabled()): ?>
	rte.Config['LinkBrowserURL'] = rte.BasePath + 'editor/filemanager/browser/default/browser.html?Connector=<?php echo $connector ?>';
	rte.Config['ImageBrowserURL'] = rte.Config['LinkBrowserURL'];
	rte.Config['FlashBrowserURL'] = rte.Config['LinkBrowserURL'];
	<?php else: ?>
	rte.Config['LinkBrowser'] = false;
	rte.Config['ImageBrowser'] = false;
	rte.Config['FlashBrowser'] = false;
	<?php endif; ?>
	
	rte.ReplaceTextarea();
});

</script>