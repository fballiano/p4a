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
 * The fieldset
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 * @see P4A_Frame
 */
class P4A_Fieldset extends P4A_Frame
{
	/**
	 * @return string
	 */
	function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<div id='$id' class='hidden'></div>";
		}

		$p4a = P4A::singleton();
		$properties = $this->composeStringProperties();
		$actions = $this->composeStringActions();

		$string  = "<fieldset id='$id' class='frame' $properties $actions >";
		if ($this->getLabel()) {
			$string  .= "<legend>" . $this->getLabel() . "</legend>";
		}
		foreach($this->_map as $i=>$row){
			$row_html = "\n<div class='row'>";
			$one_visible = false;

			foreach ($row as $j=>$obj) {
				$object =& $p4a->getObject($obj["id"]);
				if (is_object($object)) {
					$as_string = $object->getAsString();
				} else {
					unset($p4a->objects[$obj["id"]]);
					unset($this->_map[$i][$j]);
					if (empty($this->_map[$i])) unset($this->_map[$i]);
					$as_string = '';
				}

				if (strlen($as_string)>0) {
					$one_visible = true;
					$float = $obj["float"];
					if ($obj["float"] != "none") {
						$margin = "margin-" . $obj["float"];
					} else {
						$margin = "margin";
					}
					$margin_value = $obj["margin"];
					$display = $object->isVisible() ? 'block' : 'none';
					$row_html .= "\n\t<div style='padding:2px 0px;display:$display;float:$float;$margin:$margin_value'>";
					$row_html .= "\n\t\t$as_string";
					$row_html .= "\n\t</div>";
				}
			}

			$row_html .= "\n</div>\n";

			if ($one_visible) {
				$string .= $row_html;
			}
		}
		$string .= "<div class='br'></div>";
		$string .= "</fieldset>\n\n";
		return $string;
	}

}