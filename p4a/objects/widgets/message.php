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
 * Viale dei Mughetti 13/A											<br>
 * 10151 Torino (Italy)												<br>
 * Tel.:   (+39) 011 735645											<br>
 * Fax:    (+39) 011 735645											<br>
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

 	class P4A_Message extends P4A_Widget
	{

		var $icon = 'warning';
		var $size = 48;


		function &P4A_Message($name)
		{
			parent::P4A_Widget($name);
		}

		function getValue()
		{
			return $this->value;
		}

		function setValue($value=NULL)
		{
			$this->value = $value;
		}

		function getAsString()
		{
			if ($this->isVisible() and $this->getValue()) {
				$properties = $this->composeStringProperties();
				$actions = $this->composeStringActions();
				$value = $this->getValue();
				$size = $this->getSize();
				$margin = $size + 5;

				$string  = "<div class='message' $properties $actions>\n";
				$string .= "<img src='" . P4A_ICONS_PATH . "/$size/" . $this->getIcon()  . "." . P4A_ICONS_EXTENSION . "' style='margin-right: 5px;' width='$size' height='$size' />\n";
				$string .= "<div style='margin-left: {$margin}px; vertical-align:middle; height:{$size}px; display:table-cell'>$value</div>\n";
				$string .= "</div>\n\n";
			} else {
				$string =  "";
			}

			$this->setValue("");
			return $string;
		}


		function setIcon($type = 'warning')
		{
			$this->icon = $type;
		}

		function getIcon()
		{
			return $this->icon;
		}

		function setSize($size)
		{
			$this->size = $size;
		}

		function getSize()
		{
			return $this->size;
		}
	}
?>
