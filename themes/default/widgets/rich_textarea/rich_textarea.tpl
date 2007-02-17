<script type="text/javascript">

var rte = new FCKeditor('<?=$this->getId()?>input', '<?=$this->getWidth()?>', '<?=$this->getHeight()?>', '<?=$this->getRichTextareaTheme()?>');
rte.BasePath = '<?=P4A_THEME_PATH?>/widgets/rich_textarea/';

rte.Config['CustomConfigurationsPath'] = '<?=P4A_THEME_PATH?>/widgets/rich_textarea/p4aconfig.js';
rte.Config['DefaultLanguage'] = '<?=$p4a->i18n->getLanguage()?>';

<?php if ($this->isUploadEnabled()): ?>
rte.Config['LinkBrowserURL'] = rte.BasePath + 'editor/filemanager/browser/default/browser.html?Connector=<?=$connector?>';
rte.Config['ImageBrowserURL'] = rte.Config['LinkBrowserURL'];
rte.Config['FlashBrowserURL'] = rte.Config['LinkBrowserURL'];
<?php else: ?>
rte.Config['LinkBrowser'] = false;
rte.Config['ImageBrowser'] = false;
rte.Config['FlashBrowser'] = false;
<?php endif; ?>

rte.ReplaceTextarea();

</script>