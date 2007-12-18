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
 * HTML "button".
 * It's useful to trigger actions in easy way (with/without graphics).
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_Button extends P4A_Widget
{
	/**
	* The icon used by button, if null standard html button is used.
	* @access private
	* @var string
	*/
	var $_icon = null;
	var $_size = 32;
	var $_label_visible = false;

	/**
	 * @param string			Mnemonic identifier for the object.
	 * @param string			The icon taken from icon set (file name without extension).
	 */
	public function __construct($name, $icon = null)
	{
		parent::__construct($name);
		$this->addAction('onclick');
		$this->setIcon($icon);
		$this->setLabel(P4A_Generate_Default_Label($name));
	}

	/**
	 * Sets the icon for the button.
	 * @param string		The icon taken from icon set (file name without extension).
	 * @access public
	 */
	function setIcon($icon)
	{
		$this->_icon = $icon;
	}

	/**
	 * Returns the icon for the button.
	 * @access public
	 * @return string
	 */
	function getIcon()
	{
		return $this->_icon;
	}

	function setSize($size)
	{
		$this->_size = strtolower($size);
	}

	function getSize()
	{
		return $this->_size;
	}
	
	/**
	 * Sets the label and its visibility
	 * When a label is visible it will be rendered next to the icon (if there's an icon),
	 * otherwise you'll see the lable as a tooltip.
	 *
	 * @param string $label
	 * @param boolean $visible
	 */
	function setLabel($label, $visible = false)
	{
		parent::setLabel($label);
		$this->_label_visible = $visible;
	}

	/**
	 * Retuns the HTML rendered button.
	 * @access public
	 * @return string
	 */
	function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<span id='$id' class='hidden'></span>";
		}

		$p4a =& P4A::singleton();
		$label = __($this->getLabel());
		$title = $label;
		$accesskey = $this->getAccessKey();
		if (strlen($accesskey) > 0) $title = "[$accesskey] $title";
		if ($this->_label_visible or !$this->_icon) {
			$label = P4A_Highlight_AccessKey($label, $accesskey);
		} else {
			$label = null;
		}
		
		$return = "<button class='p4a_button' title='$title' " . $this->composeStringProperties();
		if ($this->isEnabled()) $return .= $this->composeStringActions();
		$return .= ">";
		
		$icon = "";
		if ($this->_icon != null and !$p4a->isHandheld()) {
			$size = $this->getSize();
			if (strpos($size, 'x') !== false) {
				list($width, $size) = explode('x', $size);
			}
			
			if (strpos($this->_icon, '.') !== false) {
				$icon = $this->_icon;
			} else {
				$icon = P4A_ICONS_PATH . "/{$size}/{$this->_icon}";
				if (!$this->isEnabled()) $icon .= "_disabled";
				$icon .= '.' . P4A_ICONS_EXTENSION;
			}
			$icon = "<img src='$icon' alt=''>";
		}
		
		$spacer = ($icon and $label) ? $spacer = "<span style='margin-left: 5px'></span>" : '';
		$return .= $icon . $spacer . $label . "</button>";
		return $return;
	}

	/**
	 * Composes a string containing all the HTML properties of the widget.
	 * Note: it will also contain the name and the value.
	 * @return string
	 * @access public
	 */
	function composeStringProperties()
	{
		$sReturn = "";
		$p4a =& p4a::singleton();
		$properties = $this->properties;

		if (isset($properties['value'])) {
			unset($properties['value']);
		}

		foreach ($properties as $property_name=>$property_value) {
			$sReturn .= $property_name . '="' . htmlspecialchars($property_value) . '" ' ;
		}

		$sReturn .= $this->composeStringStyle();
		return $sReturn;
	}
}