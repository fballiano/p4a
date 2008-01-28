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
 * To contact the authors write to:									<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)												<br>
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
 * @package p4a
 */

/**
 * p4a menu system
 * As in every big IDE such as Sun ONE or Microsoft Visual Studio
 * you have the possibility to add the top menu for simple
 * organization of masks.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_Menu extends P4A_Widget
{
	/**
	 * Menu elements
	 * @var P4A_Collection
	 */
	public $items = null;

	/**
	 * The element/subelement currently active
	 * @var P4A_Menu_Item
	 */
	protected $_active_item = null;
	
	/**
	 * @param string $name Mnemonic identifier for the object
	 */
	public function __construct($name)
	{
		parent::__construct($name);
		$this->build("P4A_Collection", "items");
	}

	/**
	 * Adds an element to the menu
	 * @param string $name Mnemonic identifier for the element
	 * @param string $label
	 */
	public function addItem($name, $label = null)
	{
		$item = $this->items->build("P4A_Menu_Item", $name);
		if ($label !== null) $item->setLabel($label);
		return $item;
	}

	/**
	 * Removes an element from the menu
	 * @param string $name
	 */
	public function dropItem($name)
	{
		if (isset($name, $this->items->$name)) {
			$this->items->$name->destroy();
			unset($this->items->$name);
		}
	}

	/**
	 * @return boolean
	 */
	public function hasItems()
	{
		if ($this->items->getNumItems()) {
			return true;
		}
		return false;
	}

	/**
	 * @param string $name
	 */
	protected function setActiveItem($name)
	{
		$this->_active_item = $name;
	}

	/**
	 * Returns the active item name
	 * @return string
	 */
	public function getActiveItem()
	{
		return $this->_active_item;
	}

	/**
	 * Returns the HTML rendered widget
	 * @return string
	 */
	public function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<div id='$id' class='hidden'></div>";
		}

		$sReturn = "";
		if ($this->hasItems()) {
			$sReturn .= "<ul id='$id' class='p4a_menu'>";
			while ($item = $this->items->nextItem()) {
				$sReturn .= $item->getAsString();
			}
			$sReturn .= "</ul>";
		}
		return $sReturn;
	}
}

/**
 * Rapresents every menu item
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_Menu_Item extends P4A_Widget
{
	/**
	 * Subelements collection
	 * @var P4A_Collection
	 */
	public $items = null;

	/**
	 * The element/subelement currently active
	 * @var P4A_Menu_Item
	 */
	protected $_active_item = null;

	/**
	 * Icon associated to the element
	 * @var string
	 * @access private
	 */
	protected $_icon = null;

	/**
	 * @param string		Mnemonic identifier for the object.
	 */
	public function __construct($name)
	{
		parent::__construct($name);
		$this->setDefaultLabel();
		$this->addAction('onclick');
		$this->build("P4A_Collection", "items");
	}

	/**
	 * Adds an element to the element
	 * @param string $name Mnemonic identifier for the element
	 * @param string $label
	 * @return P4A_Menu_Item
	 */
	public function addItem($name, $label = null)
	{
		$item = $this->items->build("P4A_Menu_Item", $name);
		if ($label !== null) $item->setLabel($label);
		return $item;
	}

	/**
	 * Removes an element from the current element
	 * @param string $name Mnemonic identifier for the element
	 */
	public function dropItem($name)
	{
		if (isset($this->items->$name)) {
			$this->items->$name->destroy();
			unset($this->items->$name);
		}
	}

	/**
	 * @return boolean
	 */
	public function hasItems()
	{
		if ($this->items->getNumItems()) {
			return true;
		}
		return false;
	}

	/**
	 * Sets the desidered subelement as active
	 * @param string $name
	 */
	protected function setActiveItem($name)
	{
		$this->_active_item = $name;
		$this->setActive();
	}

	/**
	 * Returns the active item name
	 * @return string
	 */
	public function getActiveItem()
	{
		return $this->_active_item;
	}

	/**
	 * @param string $icon
	 */
	public function setIcon($icon)
	{
		$this->_icon = $icon;
	}

	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->_icon;
	}

	/**
	 * What is executed on a click on the element.
	 * If the current element has subitems,
	 * than we pass the action to the subitem.
	 */
	public function onClick()
	{
		return $this->actionHandler('onclick');
	}

	/**
	 * Renders HTML
	 * @access public
	 * @return string
	 */
	public function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<div id='$id' class='hidden'></div>";
		}

		$properties = $this->composeStringProperties();
		if (P4A::singleton()->isHandheld()) {
			$icon = '';
		} else {
			$icon = $this->getIcon();
			if (strlen($icon)) {
				if (strpos($this->_icon, '.') === false) {
					$icon_disabled = '';
					if (!$this->isEnabled()) {
						$icon_disabled = '_disabled';
					}
					$icon = P4A_ICONS_PATH . "/16/{$icon}{$icon_disabled}." . P4A_ICONS_EXTENSION;
				}
				$icon = "<img src='$icon' alt='' />";
			}
		}

		if (empty($this->_map_actions["onclick"]["method"]) or !$this->isEnabled()) {
			$sReturn = "<li>" . P4A_Generate_Widget_Layout_Table($icon, "<div $properties>" . $this->getLabel() . "</div>");
		} else {
			$actions = $this->composeStringActions();
			$sReturn = "<li>" . P4A_Generate_Widget_Layout_Table($icon, "<a href='#' $actions $properties>" . P4A_Highlight_AccessKey($this->getLabel(), $this->getAccessKey()) . "</a>");
		}

		if ($this->hasItems()) {
			$sReturn .= "<ul>";
			while ($item = $this->items->nextItem()) {
				$sReturn .= $item->getAsString();
			}
			$sReturn .= "</ul>";
		}
		$sReturn .= "</li>";
		return $sReturn;
	}
}