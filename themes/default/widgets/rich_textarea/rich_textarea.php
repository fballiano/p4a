<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 * 
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with P4A.  If not, see <http://www.gnu.org/licenses/lgpl.html>.
 * 
 * To contact the authors write to:                                     <br />
 * CreaLabs SNC                                                         <br />
 * Via Medail, 32                                                       <br />
 * 10144 Torino (Italy)                                                 <br />
 * Website: {@link http://www.crealabs.it}                              <br />
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @link http://www.crealabs.it
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package p4a
 */
?>

<?php if (!p4a::singleton()->isOpera()): ?>
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
<?php endif; ?>