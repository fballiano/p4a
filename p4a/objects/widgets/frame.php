<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/agpl.html>.
 * 
 * To contact the authors write to:									<br />
 * CreaLabs SNC														<br />
 * Via Medail, 32													<br />
 * 10144 Torino (Italy)												<br />
 * Website: {@link http://www.crealabs.it}							<br />
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @link http://www.crealabs.it
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/agpl.html GNU Affero General Public License
 * @package p4a
 */

/**
 * The frame widget
 * A frame is a panel where we anchor widgets.
 * It generates tableless HTML and is used for relative positioning.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_Frame extends P4A_Widget
{
	/**
	 * @var array
	 */
	protected $_map = array();
	
	/**
	 * @var integer
	 */
	protected $_row = 1;

	protected function _anchor(&$object, $margin = "20px", $float = "left")
	{
		if (is_object($object)) {
			$to_add = array("id"=>$object->getId(), "margin" => $margin, "float" => $float);
			$this->_map[$this->_row][]  = $to_add;
		}
	}

	public function anchor(&$object, $margin = "10px", $float="left")
	{
		$this->newRow();
		$this->_anchor($object, $margin, $float);
	}

	public function anchorRight(&$object, $margin = "10px")
	{
		$this->_anchor($object, $margin, "right");
	}

	public function anchorLeft(&$object, $margin = "10px")
	{
		$this->_anchor($object, $margin, "left");
	}

	public function anchorCenter(&$object, $margin = "auto")
	{
		$this->newRow();
		$this->_anchor($object, $margin, "none");
	}

	public function clean()
	{
		$this->_map = array();
	}

	public function newRow()
	{
		$this->_row++;
	}

	public function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<div id='$id' class='hidden'></div>";
		}

		$p4a = P4A::singleton();
		$handheld = $p4a->isHandheld();
		$properties = $this->composeStringProperties();
		$actions = $this->composeStringActions();
		$class = $this->composeStringClass();

		$string  = "<div id='{$id}' $class $properties $actions>";
		foreach($this->_map as $objs) {
			$one_visible = false;
			$row = "\n<div class='row'>";
			foreach ($objs as $obj) {
				$object =& $p4a->getObject($obj["id"]);
				if (is_object($object)) {
					$as_string = $object->getAsString();
				} else {
					unset($p4a->objects[$obj["id"]]);
					unset($this->_map[$i][$j]);
					if (empty($this->_map[$i])) unset($this->_map[$i]);
					$as_string = '';
				}
				if (strlen($as_string) > 0) {
					$one_visible = true;
					$float = $obj["float"];
					if ($obj["float"] != "none") {
						$margin = "margin-" . $obj["float"];
					} else {
						$margin = "margin";
					}
					$margin_value = $obj["margin"];
					$as_string = "\n\t\t$as_string" ;

					if ($handheld) {
						$row .= $as_string;
					} else {
						$display = $object->isVisible() ? 'block' : 'none';
						$row .= "\n\t<div style='padding:2px 0px;display:$display;float:$float;$margin:$margin_value'>$as_string\n\t</div>";
					}
				}
			}

			$row .= "\n</div>\n";

			if ($one_visible) {
				$string .= $row;
			}
		}
		$string .= "<div class='br'></div>";
		$string .= "</div>\n\n";
		return $string;
	}
}