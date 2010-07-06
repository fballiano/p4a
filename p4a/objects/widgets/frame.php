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
 * @link http://www.crealabs.it
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package p4a
 */

/**
 * A frame is a panel where you anchor widgets.
 * It generates tableless HTML and is used for relative positioning, this means
 * that every anchored widget is floating next to the previous one.
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
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

	/**
	 * @param P4A_Widget $object
	 * @param string $margin
	 * @param string $float
	 * @return P4A_Frame
	 */
	protected function _anchor($object, $margin = "20px", $float = "left")
	{
		if (is_object($object)) {
			$to_add = array("id"=>$object->getId(), "margin" => $margin, "float" => $float);
			$this->_map[$this->_row][]  = $to_add;
		}
		return $this;
	}

	/**
	 * @param P4A_Widget $object
	 * @param string $margin
	 * @param string $float
	 * @return P4A_Frame
	 */
	public function anchor($object, $margin = "10px", $float="left")
	{
		$this->newRow();
		return $this->_anchor($object, $margin, $float);
	}

	/**
	 * @param P4A_Widget $object
	 * @param string $margin
	 * @return P4A_Frame
	 */
	public function anchorRight($object, $margin = "10px")
	{
		return $this->_anchor($object, $margin, "right");
	}

	/**
	 * @param P4A_Widget $object
	 * @param string $margin
	 * @return P4A_Frame
	 */
	public function anchorLeft($object, $margin = "10px")
	{
		return $this->_anchor($object, $margin, "left");
	}

	/**
	 * @param P4A_Widget $object
	 * @return P4A_Frame
	 */
	public function anchorCenter($object)
	{
		$this->newRow();
		return $this->_anchor($object, "auto", "none");
	}

	/**
	 * @return P4A_Frame
	 */
	public function clean()
	{
		$this->_map = array();
		return $this;
	}

	/**
	 * @return P4A_Frame
	 */
	public function newRow()
	{
		$this->_row++;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<div id='$id' class='hidden'></div>";
		}

		$properties = $this->composeStringProperties();
		$actions = $this->composeStringActions();
		$class = $this->composeStringClass();

		$string  = "<div id='{$id}' $class $properties $actions>";
		$string .= $this->getChildrenAsString();
		$string .= "<div class='br'></div>";
		$string .= "</div>\n\n";
		return $string;
	}
	
	protected function getChildrenAsString()
	{
		$string = "";
		$p4a = P4A::singleton();
		$handheld = p4a::singleton()->isHandheld();
		foreach($this->_map as $objs) {
			$one_visible = false;
			$row = "\n<div class='row'>";
			foreach ($objs as $obj) {
				$classes = array();
				$object = $p4a->getObject($obj["id"]);
				if (is_object($object)) {
					$as_string = $object->getAsString();
				} else {
					unset($p4a->objects[$obj["id"]]);
					unset($this->_map[$i][$j]);
					if (empty($this->_map[$i])) unset($this->_map[$i]);
					$as_string = '';
				}
				if (strlen($as_string) > 0) {
					if ($obj["float"] == "none") {
						$obj["float"] = "left";
						$obj["margin"] = 0;
						$classes[] = "p4a_frame_anchor_center";
					}

					$one_visible = true;
					$float = $obj["float"];
					$margin = "margin-" . $obj["float"];
					$margin_value = $obj["margin"];
					$as_string = "\n\t\t$as_string" ;

					if ($handheld) {
						$row .= $as_string;
					} else {
						$display = $object->isVisible() ? 'block' : 'none';
						$class = empty($classes) ? '' : 'class="' . implode(',', $classes) . '"';
						$row .= "\n\t<div $class style='padding:2px 0;display:$display;float:$float;$margin:$margin_value'>$as_string\n\t</div>";
					}
				}
			}

			$row .= "\n</div>\n";

			if ($one_visible) {
				$string .= $row;
			}
		}
		return $string;
	}
}