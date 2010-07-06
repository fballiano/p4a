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

/**
 * This widget allows a tree navigation within a directory.
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class P4A_Dir_Navigator extends P4A_Widget
{
	/**
	 * @var string
	 */
	protected $base_dir = null;
	
	/**
	 * @var string
	 */
	protected $current_subdir = null;

	/**
	 * Expand whole tree or collapse?
	 * @var boolean
	 */
	protected $expand_all = true;

	/**
	 * Trim after this number of characters
	 * @var integer
	 */
	protected $trim = 0;

	/**
	 * Is selected element clickable?
	 * @var boolean
	 */
	protected $enable_selected_element = false;

	/**
	 * @param string $name
	 */
	public function __construct($name)
	{
		parent::__construct($name);
		$this->addAction('onclick');
		$this->intercept($this, 'onclick', 'onClick');
	}

	public function setBaseDir($base_dir)
	{
		$this->base_dir = $base_dir;
		return $this;
	}
	
	public function getBaseDir()
	{
		return $this->base_dir;
	}
	
	public function setCurrentSubdir($current_subdir)
	{
		$this->current_subdir = $current_subdir;
		return $this;
	}
	
	public function getCurrentSubdir()
	{
		return $this->current_subdir;
	}
	
	public function getCurrentAbsoluteDir()
	{
		return P4A_Strip_Double_Slashes("{$this->getBaseDir()}/{$this->getCurrentSubdir()}");
	}

	/**
	 * Trims description after x chars (0 = disabled)
	 * @param integer $chars Num of chars
	 * @return P4A_DB_Navigator
	 */
	public function setTrim($chars)
	{
		$this->trim = $chars;
		return $this;
	}

	/**
	 * Is selected element clickable?
	 * @param boolean
	 * @return P4A_DB_Navigator
	 */
	public function enableSelectedElement($enable = true)
	{
		$this->enable_selected_element = $enable;
		return $this;
	}

	/**
	 * Renders the widget as HTML.
	 * Triggers the "beforeRenderElement" event, passing the absolute dir
	 * of the single element to the handle, if the handler return the ABORT
	 * constant the element won't be displayed, otherwise if the return value
	 * is a string it will be added to the CSS classes of the element's "li"
	 * HTML tag.
	 * @return string
	 */
	public function getAsString()
	{
		$obj_id = $this->getId();
		if (!$this->isVisible()) {
			return "<div id='$obj_id' class='hidden'></div>";
		}
		
		return $this->_getAsString($this->base_dir);
	}

	private function _getAsString($base_dir)
	{
		$return = "<ul class='p4a_dir_navigator'>";
		$current = $this->base_dir . _DS_ . $this->current_subdir;
		
		foreach (scandir($base_dir) as $dir) {
			$absolute_dir = $base_dir . _DS_ . $dir;
			if (!is_dir($absolute_dir) or $absolute_dir == P4A_UPLOADS_TMP_DIR or substr($dir, 0, 1) == '.' or $dir == 'CVS') continue;
			
			$handler_return = $this->actionHandler('beforeRenderElement', $absolute_dir);
			if ($handler_return === ABORT) continue;
			if (!is_string($handler_return)) $handler_return = "";
			
			if (P4A_OS == "linux") {
				$actions = $this->composeStringActions(str_replace(P4A_Strip_Double_Slashes("{$this->base_dir}/"), "", P4A_Strip_Double_Slashes($absolute_dir)));
			} else {
				$actions = $this->composeStringActions(str_replace(P4A_Strip_Double_Backslashes("{$this->base_dir}\\"), "", P4A_Strip_Double_Backslashes($absolute_dir)));
			}
			
			$description = $this->_trim($dir);
			
			if ($absolute_dir == $current) {
				$selected = "class='active_node $handler_return'";
				if ($this->enable_selected_element) {
					$link_prefix = "<a href='#' {$actions}>";
					$link_suffix = "</a>";
				} else {
					$link_prefix = "<span>";
					$link_suffix = "</span>";
				}
			} else {
				$selected = "class='$handler_return'";
				$link_prefix = "<a href='#' {$actions}>";
				$link_suffix = "</a>";
			}

			$return .= "<li {$selected}>{$link_prefix}{$description}{$link_suffix}\n";
			if (strpos($current, $absolute_dir) !== false) {
				$return .= $this->_getAsString($absolute_dir);
			}
			$return .= "</li>\n";
		}
		$return .= "</ul>";
		return $return;
	}

	/**
	 * OnClick event interceptor
	 * @param array $params
	 */
	public function onClick($params)
	{
		$this->redesign();
		$current_subdir = $params[0];
		$this->setCurrentSubdir($current_subdir);
		return $this->actionHandler('afterClick', $current_subdir);
	}

	/**
	 * Trims a text after a fixed number of characters
	 * @param string $text
	 */
	protected function _trim($text)
	{
		if ($this->trim > 0) {
			$len = strlen($text);
			$text = substr($text, 0, $this->trim);
			if ($len > $this->trim) {
				$text .= "...";
			}
		}
		return $text;
	}
	
	/**
	 * Returns all directories at root level.
	 * @return array
	 */
	public function getRootDirectories()
	{
		$return = array();
		$base_dir = $this->base_dir;
		
		foreach (scandir($base_dir) as $dir) {
			$absolute_dir = $base_dir . _DS_ . $dir;
			if (!is_dir($absolute_dir) or $absolute_dir == P4A_UPLOADS_TMP_DIR
				or substr($dir, 0, 1) == '.' or $dir == 'CVS') continue;
			$return[] = $dir;
		}
		
		return $return;
	}
}