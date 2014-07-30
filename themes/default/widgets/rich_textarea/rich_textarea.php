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
 * Fabrizio Balliano <fabrizio@fabrizioballiano.it>                     <br />
 * Andrea Giardina <andrea.giardina@crealabs.it>
 *
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package p4a
 */
?>

<script type="text/javascript" class="parse_before_html_replace">
require(['<?php echo P4A_THEME_PATH ?>/widgets/rich_textarea/ckeditor.js']);
</script>

<script type="text/javascript">
<?php echo $this->getId() ?>post = function () {
	if (typeof CKEDITOR == "undefined") {
		setTimeout(<?php echo $this->getId() ?>post, 200);
		return;
	}

	CKEDITOR.replace('<?php echo $this->getId() ?>input', {
		p4a_instance_id: '<?php echo $this->getId() ?>input',
		autoUpdateElement: false,
		language: '<?php echo P4A::singleton()->i18n->getLanguage() ?>',
		width: '<?php echo $this->getWidth() ?>',
		height: '<?php echo $this->getHeight() ?>',
		resize_enabled: false,
		toolbarCanCollapse: false,
		coreStyles_strike: {element: 'span', attributes: {'style': 'text-decoration:line-through'}},
		coreStyles_underline: {element: 'span', attributes: {'style': 'text-decoration:underline'}},
		forcePasteAsPlainText: true,
		<?php if ($this->rich_textarea_templates): ?>
		templates_files: ['<?php echo implode("','", array_keys($this->rich_textarea_templates)) ?>'],
		<?php endif; ?>
		on: {
			instanceReady: function (e) {
				e.editor.p4a_loaded = true;
			}
		},
		<?php
			$toolbars = $this->getRichTextareaToolbars();
			if (empty($toolbars)) {
				$toolbar = $this->getRichTextareaTheme();
				if ($toolbar == "Default") {
					$templates = $this->rich_textarea_templates ? ",'Templates'" : "";
					$toolbar = "[
				      	['Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat','-','Source','Maximize','Preview','Print','-','About'],
				    	'/',
				    	['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','NumberedList','BulletedList','-','Outdent','Indent','-','Format'],
				    	'/',
				    	['Link','Unlink','Anchor','-','Image','Flash','-','Table','HorizontalRule','SpecialChar'$templates]
			    	]";
				} else {
					$toolbar = "'$toolbar'";
				}
			} else {
				foreach ($toolbars as $k=>$v) {
					$toolbars[$k] = "['" . implode("','", $v) . "']";
				}
				$toolbar = "[" . implode(",'/',", $toolbars) . "]";
			}
		?>
	    toolbar: <?php echo $toolbar ?>,
		<?php if ($this->isUploadEnabled()): ?>
		filebrowserBrowseUrl: '<?php echo P4A_THEME_PATH ?>/widgets/rich_textarea/filemanager/browser/default/browser.html?Connector=<?php echo $connector ?>'
		<?php endif; ?>
	});
};

<?php echo $this->getId() ?>post();
</script>
