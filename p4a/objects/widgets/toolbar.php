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
	 * A toolbar is a buttons/images set.
	 * Every button/image can have an action handler.
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @package p4a
	 */
	class P4A_Toolbar extends P4A_Widget
	{
		/**
		 * Counts the number of separators/spacers in the toolbar.
		 * @var integer
		 * @access private
		 */
		var $separators_counter = 0;

		/**
		 * Buttons collection.
		 * @var array
		 * @access public
		 */
		var $buttons = NULL;

		var $_size = NULL;

		/**
		 * Class costructor.
		 * @param string				Mnemonic identifier for the object.
		 * @param mask					The mask on wich the toolbar will operate.
		 * @access private
		 */
		function &P4A_Toolbar($name)
		{
			parent::P4A_Widget($name);
			$this->build("p4a_collection", "buttons");

			$this->setOrientation('horizontal');
		}

		/**
		 * Istances a new p4a_button object and than adds it to the toolbar.
		 * @param string			Mnemonic identifier for the object.
		 * @param string			The icon taken from icon set (file name without extension).
		 * @access public
		 * @see P4A_Button
		 */
		function &addButton($button_name, $icon = NULL, $position = "left")
		{
			$this->buttons->build("p4a_button", $button_name);
			$this->buttons->$button_name->setIcon($icon);
			$this->buttons->$button_name->setStyleProperty("float", $position);
			if ($this->_size) {
				$this->buttons->$button_name->setSize($this->_size);
			}
			return $this->buttons->$button_name;
		}

		/**
		 * Adds a separator image.
		 * @access public
		 */
		function addSeparator($position = "left")
		{
			$name = 's' . $this->separators_counter++;
			$this->buttons->build("p4a_icon", $name);
			$this->buttons->$name->setIcon('separator');
			$this->buttons->$name->setStyleProperty("float", $position);
			return $this->buttons->$name;
		}

		/**
		 * Adds a spacer image of the desidered width.
		 * @param integer		Width in pixel from the spacer.
		 * @access public
		 */
		function addSpacer($width = 10, $position = "left")
		{
			$name = 's' . $this->separators_counter++;
			$this->buttons->build("p4a_image", $name);
			$this->buttons->$name->setStyleProperty("float", $position);
			$this->buttons->$name->setIcon('spacer');
			$this->buttons->$name->setWidth($width);
			return $this->buttons->$name;
		}

		/**
		 * Turns off the action handler for the desidered button.
		 * @param string		Button identifier.
		 * @access public
		 */
		function disable($button_name = NULL)
		{
			if ($button_name === NULL) {
				while ($button =& $this->buttons->nextItem()) {
					$button->disable();
				}
			} else {
				if (is_object($this->buttons->$button_name)) {
					$this->buttons->$button_name->disable();
				}
			}
		}

		/**
		 * Turns on the action handler for the desidered button.
		 * @param string		Button identifier.
		 * @access public
		 */
		function enable($button_name = NULL)
		{
			if ($button_name === NULL) {
				while ($button =& $this->buttons->nextItem()) {
					$button->enable();
				}
			} else {
				if (is_object($this->buttons->$button_name)) {
					$this->buttons->$button_name->enable();
				}
			}
		}

		function setSize($size)
		{
			$this->_size = $size;
		}

		function getSize()
		{
			return $this->_size;
		}

		/**
		 * Sets the rendering orientation for the toolbar.
		 * @param string		Orientation (horizontal|vertical).
		 * @access public
		 */
		function setOrientation($orientation)
		{
			$this->orientation = $orientation;
		}

		/**
		 * Returns the HTML rendered widget.
		 * @return string
		 * @access public
		 */
		function getAsString()
		{
			if (!$this->isVisible()) {
				return '';
			}

			$properties = $this->composeStringProperties();
			$string   = "<div class='toolbar' $properties >";
			while($button =& $this->buttons->nextItem()) {
  				$string .= $button->getAsString();
			}
 			$string .= "<div class='br'></div>\n";
			$string .= "</div>\n\n";
			return $string;
		}

	}
?>