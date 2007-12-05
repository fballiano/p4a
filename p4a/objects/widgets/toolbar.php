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
		 * Items collection.
		 * @var array
		 * @access public
		 */
		var $items = NULL;

		var $_size = NULL;

		/**
		 * Class costructor.
		 * @param string				Mnemonic identifier for the object.
		 * @param mask					The mask on wich the toolbar will operate.
		 * @access private
		 */
		function P4A_Toolbar($name)
		{
			parent::P4A_Widget($name);
			$this->build("p4a_collection", "items");
		}

		/**
		 * Istances a new p4a_button object and than adds it to the toolbar.
		 * @param string			Mnemonic identifier for the object.
		 * @param string			The icon taken from icon set (file name without extension).
		 * @access public
		 * @see P4A_Button
		 */
		function addButton($button_name, $icon = null)
		{
			$this->items->build("p4a_button", $button_name);
			$this->items->$button_name->setIcon($icon);
			return $this->items->$button_name;
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
		 * Adds a label object.
		 * @access public
		 */
		function addLabel($name, $text, $position = "left")
		{
			$this->buttons->build('p4a_box', $name);
			$this->buttons->$name->setValue($text);
			$this->buttons->$name->setStyleProperty("float", $position);
		}
		
		function addField($name, $label = null)
		{
			$this->items->build('p4a_field', $name);
			$this->items->$name->setLabel($label);
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
		 * Returns the HTML rendered widget.
		 * @return string
		 * @access public
		 */
		function getAsString()
		{
			$id = $this->getId();
			$items = array();
			$return = "";
			
			while($item = $this->items->nextItem()) {
				if (is_string($item)) {
					$items[] = $item;
				} else {
  					$return .= $item->getAsString();
  					$items[] = $item->getId();
				}
			}
			$items = join(',', $items);
			
			$return .= "$id = new Ext.Toolbar({id:'$id',items:[$items]});\n";
			return $return;
			
			/*
			$id = $this->getId();
			if (!$this->isVisible()) {
				return "<div id='$id' class='hidden'></div>";
			}

			$properties = $this->composeStringProperties();
			$string   = "<div id='$id' class='toolbar' $properties >";
			while($button =& $this->buttons->nextItem()) {
  				$string .= $button->getAsString();
			}
 			$string .= "<div class='br'></div>\n";
			$string .= "</div>\n\n";
			return $string;
			*/
		}

	}