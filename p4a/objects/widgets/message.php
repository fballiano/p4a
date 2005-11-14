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

 	class P4A_Message extends P4A_Widget
	{

		var $icon = 'warning';
		var $size = 48;
		var $auto_clear = true;

		function P4A_Message($name)
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
			if ($this->isVisible() and strlen($this->getValue())) {
				$properties = $this->composeStringProperties();
				$actions = $this->composeStringActions();
				$value = $this->getValue();
				$icon = $this->getIcon();
				$size = $this->getSize();
				$margin = $size + 5;

				$string  = "<dl class='message' $properties $actions>\n";
				$string .= "<dt>";
				if (!empty($icon)) {
					$string .= "<img src='" . P4A_ICONS_PATH . "/$size/" . $this->getIcon()  . "." . P4A_ICONS_EXTENSION . "' width='$size' height='$size' alt='' />";
				}
				$string .= "</dt>\n";
				$string .= "<dd>$value</dd>\n";
				$string .= "</dl>\n\n";
			} else {
				$string =  "";
			}

			if ($this->auto_clear) {
				$this->setValue("");
			}

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

		function autoClear($enable = true)
		{
			$this->auto_clear = $enable;
		}
	}
?>
