<script type="text/javascript">

var rte = new FCKeditor('<?php echo $this->getId()?>input', '<?php echo $this->getWidth()?>', '<?php echo $this->getHeight()?>', '<?php echo $this->getRichTextareaTheme()?>');
rte.BasePath = '<?php echo P4A_THEME_PATH?>/widgets/rich_textarea/';

rte.Config['CustomConfigurationsPath'] = '<?php echo P4A_THEME_PATH?>/widgets/rich_textarea/p4aconfig.js';
rte.Config['DefaultLanguage'] = '<?php echo $p4a->i18n->getLanguage()?>';

<?php if ($this->isUploadEnabled()): ?>
rte.Config['LinkBrowserURL'] = rte.BasePath + 'editor/filemanager/browser/default/browser.html?Connector=<?php echo $connector?>';
rte.Config['ImageBrowserURL'] = rte.Config['LinkBrowserURL'];
rte.Config['FlashBrowserURL'] = rte.Config['LinkBrowserURL'];
<?php else: ?>
rte.Config['LinkBrowser'] = false;
rte.Config['ImageBrowser'] = false;
rte.Config['FlashBrowser'] = false;
<?php endif; ?>

rte.ReplaceTextarea();

</script>