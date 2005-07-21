<?php

/**
 * P4A - PHP For Applications.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * To contact the authors write to:								<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)											<br>
 * Web:    {@link http://www.crealabs.it}							<br>
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * The latest version of p4a can be obtained from:
 * {@link http://p4a.sourceforge.net}
 *
 * @link http://p4a.sourceforge.net
 * @link http://www.crealabs.it
 * @link mailto:info@crealabs.it info@crealabs.it
 * @copyright CreaLabs
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package P4A_Base_Mask
 */

/**
 * 
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package P4A_Filesystem_Navigator
 */
class P4A_Filesystem_Navigator extends P4A_Frame
{
	var $folders = null;
	var $f_folders = null;
	
	var $files = null;
	var $f_files = null;
	
	function P4A_Filesystem_Navigator($name)
	{
		parent::P4A_Frame($name);
		$this->build("P4A_Filesystem_Navigator_Folders", "folders");
		$this->build("P4A_Filesystem_Navigator_Files", "files");
		
		$this->build("p4a_fieldset", "f_folders");
		$this->f_folders->anchor($this->folders);
		$this->f_folders->anchor($this->folders->message);
		$this->f_folders->anchor($this->folders->create_folder_field);
		$this->f_folders->anchorLeft($this->folders->create_folder_button);
		
		$this->build("p4a_fieldset", "f_files");
		$this->f_files->anchor($this->files);
		$this->f_files->anchor($this->files->message);
		$this->f_files->anchor($this->files->upload_field);
		
		$this->anchor($this->f_folders);
		$this->anchorLeft($this->f_files);
	}
	
	function getAsString()
	{
		$p4a =& p4a::singleton();
		$p4a->active_mask->addTempCSS(P4A_APPLICATION_PATH . "/p4a_filesystem_navigator.css");
		
		return parent::getAsString();
	}
}

/**
 * 
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package P4A_Filesystem_Navigator
 */
class P4A_Filesystem_Navigator_Folders extends P4A_Widget
{
	var $_base = P4A_UPLOADS_DIR;
	var $_current = "";
	var $create_folder_field = null;
	var $create_folder_button = null;
	var $delete_folder_button = null;
	var $message = null;
	var $no_folders_message = "";

	function P4A_Filesystem_Navigator_Folders($name)
	{
		parent::P4A_Widget($name);
		$this->addAction("onClick");
		$this->intercept($this, "onClick", "onClick");
		
		$this->build("P4A_Field", "create_folder_field");
		$this->create_folder_field->label->setVisible(false);
		
		$this->build("P4A_Button", "create_folder_button");
		$this->intercept($this->create_folder_button, "onClick", "createFolder");
		
		$this->build("P4A_Button", "delete_folder_button");
		$this->delete_folder_button->requireConfirmation("onClick");
		$this->intercept($this->delete_folder_button, "onClick", "deleteFolder");
		
		$this->build("P4A_Message", "message");
	}
	
	function setBase($folder)
	{
		$this->_base = $folder;
	}
	
	function getBase()
	{
		return $this->_base;
	}
	
	function getCurrent()
	{
		return $this->_current;
	}
	
	function setCurrent($folder)
	{
		if ((strpos($folder, $this->getBase()) === 0) and is_dir($folder)) {
			$this->_current = $folder;
		}
	}
	
	function resetCurrent()
	{
		$this->_current = "";
	}
	
	function hasDirectories()
	{
		$base = $this->getBase();
		$handle = opendir($base);
		while (false !== ($file = readdir($handle))) {
			if (is_dir("$base/$file") and ($file != ".") and ($file != "..") and ($file != "CVS") and ("$base/$file" != P4A_UPLOADS_TMP_DIR)) {
				return true;
			}
		}
		closedir($handle);
		return false;
	}
	
	function getAsString($folder = null)
	{
		if (!$this->isVisible()) {
			return "";
		}
		
		$obj_id = $this->getId();
		$return = "";
		$class = "";
		
		if (empty($folder)) {
			$folder = $this->getBase();
		}
		
		$handle = opendir($folder);
		$return .= "<ul class=\"p4a_filesystem_navigator\" style=\"list-style-image:url('" . P4A_ICONS_PATH . "/16/folder." . P4A_ICONS_EXTENSION . "')\">";
		while (false !== ($file = readdir($handle))) {
			if (is_dir("$folder/$file") and ($file != ".") and ($file != "..") and ($file != "CVS") and ("$folder/$file" != P4A_UPLOADS_TMP_DIR)) {
				$current = $this->getCurrent();
				
				if ($this->getCurrent() == "$folder/$file") {
					$return .= "<li class='active_node' style='list-style-image:url(" . P4A_ICONS_PATH . "/16/folder_open." . P4A_ICONS_EXTENSION . ")'>{$file}";
				} else {
					$actions = $this->composeStringActions("$folder/$file");
					$return .= "<li><a href='#' $actions>{$file}</a>";
				}
			
				$return .= $this->getAsString("$folder/$file");
				$return .= "</li>\n";
			}
		}
		$return .= "</ul>";
		closedir($handle);
		return $return;
	}
	
	function getFiles()
	{
		$return = array();
		$folder = $this->getCurrent();
		if (!empty($folder)) {
			$handle = opendir($folder);
			while (false !== ($file = readdir($handle))) {
				if (is_file("$folder/$file")) {
					$return[] = $file;
				}
			}
			closedir($handle);
		}
		
		return $return;
	}
	
	function onClick($params)
	{
		$p4a =& p4a::singleton();
		$parent =& $p4a->getObject($this->getParentID());
		$folder = $params[0];
		$this->setCurrent($folder);
		$parent->files->setCurrent(null);
	}
	
	function createFolder()
	{
		$folder = $this->create_folder_field->getNewValue();
		
		$current = $this->getCurrent();
		if (empty($current)) {
			$current = $this->getDirectory();
		}
		
		mkdir("$current/$folder");
		$this->create_folder_field->setNewValue(null);
	}
	
	function deleteFolder()
	{
		$folder = $this->getCurrent();
		System::rm(array("-r", $folder));
		$this->resetCurrent();
	}
}

/**
 * 
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package P4A_Filesystem_Navigator
 */
class P4A_Filesystem_Navigator_Files extends P4A_Widget
{
	var $_current = "";
	var $message = null;
	var $upload_field = null;
	var $delete_file_button = null;
	var $no_files_message = "";
	
	function P4A_Filesystem_Navigator_Files($name)
	{
		parent::P4A_Widget($name);
		$this->addAction("onClick");
		$this->intercept($this, "onClick", "onClick");
		
		$this->build("P4A_Message", "message");
		
		$this->build("P4A_Field", "upload_field");
		$this->upload_field->setType("file");
		$this->intercept($this->upload_field, "afterUpload", "afterUpload");
		
		$this->build("P4A_Button", "delete_file_button");
		$this->delete_file_button->requireConfirmation("onClick");
		$this->intercept($this->delete_file_button, "onClick", "deleteFile");
	}
	
	function getCurrent()
	{
		return $this->_current;
	}
	
	function setCurrent($file)
	{
		$p4a =& p4a::singleton();
		$parent =& $p4a->getObject($this->getParentID());
		$folder = $parent->folders->getCurrent();
		
		if (file_exists("$folder/$file")) {
			$this->_current = $file;
		}
	}
	
	function resetCurrent()
	{
		$this->_current = "";
	}
	
	function getAsString()
	{
		if (!$this->isVisible()) {
			return "";
		}
		
		$p4a =& p4a::singleton();
		$parent =& $p4a->getObject($this->getParentID());
		$files = $parent->folders->getFiles();
		$return = "";
		
		if (empty($files)) {
			$this->message->setValue($this->no_files_message);
		} else {
			$return .= "<ul class=\"p4a_filesystem_navigator\" style=\"list-style-image:url('" . P4A_ICONS_PATH . "/16/generic_file." . P4A_ICONS_EXTENSION . "')\">";
			$current = $this->getCurrent();
			
			foreach ($files as $file) {
				if ($file == $current) {
					$return .= "<li class='active_node'>$file</li>";
				} else {
					$actions = $this->composeStringActions($file);
					$return .= "<li><a href='#' $actions>$file</a></li>";
				}
			}
			$return .= "</ul>";
		}
		
		return $return;
	}
	
	function onClick($params)
	{
		$file = $params[0];
		$this->setCurrent($file);
		$this->actionHandler("afterClick");
	}
	
	function afterUpload()
	{
		$p4a =& p4a::singleton();
		$parent =& $p4a->getObject($this->getParentID());
		$current = $parent->folders->getCurrent();
		$name = $this->upload_field->getNewValue(0);
		rename(P4A_UPLOADS_TMP_DIR . "/$name", "$current/$name");
		$this->upload_field->setNewValue(null);
	}
	
	function deleteFile()
	{
		$p4a =& p4a::singleton();
		$parent =& $p4a->getObject($this->getParentID());
		$file = $parent->folders->getCurrent() . "/" . $this->getCurrent();
		unlink($file);
		$this->resetCurrent();
	}
}

?>