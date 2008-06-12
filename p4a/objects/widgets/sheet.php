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
 * The sheet widget
 * A canvas is a panel where you anchor widgets in a grid way.
 * It generates an HTML table with all widgets in cells, it
 * supports row/col spanning.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
class P4A_Sheet extends P4A_Widget
{
	/**
	 * @var array
	 */
	protected $_map = array();
	
	/**
	 * @var integer
	 */
	protected $_rows = 0;
	
	/**
	 * @var integer
	 */
	protected $_cols = 0;
	
	public function __construct($name, $rows = 0, $cols = 0)
	{
		parent::__construct($name = null);
		$this->_rows = $rows;
		$this->_cols = $cols;
		
		for ($i = 1; $i <= $rows; $i++) {
			$this->_map[$i] = array();
			for ($j = 1; $j <= $cols; $j++) {
				$this->_map[$i][$j] = null;
			}
		}
	}
	
	/**
	 * @return integer
	 */
	public function getRows()
	{
		return $this->_rows;
	}
	
	/**
	 * @return integer
	 */
	public function getCols()
	{
		return $this->_cols;
	}

	/**
	 * @param P4A_Widget|string $object
	 * @param integer $row
	 * @param integer $column
	 * @param integer $rowspan
	 * @param integer $colspan
	 * @return P4A_Sheet
	 */
	public function anchor($widget, $row, $col, $rowspan = 1, $colspan = 1)
	{
		if (!($widget instanceof P4A_Widget) and !is_string($widget)) {
			trigger_error("P4A_Sheet accepts only P4A_Widgets or strings", E_USER_ERROR);
		}
		
		if ($row + $rowspan - 1 > $this->_rows) {
			trigger_error("P4A_Sheet accepts only P4A_Widgets or strings", E_USER_ERROR);
		}
		
		if ($col + $colspan - 1 > $this->_cols) {
			trigger_error("cell margins are out of the grid", E_USER_ERROR);
		}
		
		for ($i = $row; $i < $row+$rowspan; $i++) {
			for ($j = $col; $j < $col+$colspan; $j++) {
				if ($this->_map[$i][$j] !== null) {
					trigger_error("cell is occupied", E_USER_ERROR);
				}
				$this->_map[$i][$j] = '-';
			}
		}

		if ($widget instanceof P4A_Widget) {
			$widget = $widget->getId();
		}
		
		$this->_map[$row][$col] = array($widget, $rowspan, $colspan);
		return $this;
	}
	
	/**
	 * @param integer $row
	 * @param integer $col
	 * @return P4A_Sheet
	 */
	public function setFree($row, $col)
	{
		if (isset($this->_map[$row]) and isset($this->_map[$row][$col]) and is_array($this->_map[$row][$col])) {
			$rowspan = $this->_map[$row][$col][1];
			$colspan = $this->_map[$row][$col][2];
			
			for ($i = $row; $i < $row+$rowspan; $i++) {
				for ($j = $col; $j < $col+$colspan; $j++) {
					$this->_map[$i][$j] = null;
				}
			}
		}
		return $this;
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
	 * @return string
	 */
	public function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<div id='$id' class='hidden'></div>";
		}

		$p4a = P4A::singleton();
		$properties = $this->composeStringProperties();
		$actions = $this->composeStringActions();
		$class = $this->composeStringClass();
		
		$string = "<table id='$id' $properties $actions $class>\n";
		for ($i = 1; $i <= $this->_rows; $i++) {
			$string .= "<tr>\n";
			for ($j = 1; $j <= $this->_cols;) {
				if (is_string($this->_map[$i][$j])) {
					$j++;
					continue;
				}
				
				$rowspan = 1;
				$colspan = 1;
				$obj_as_string = "&nbsp;";
				if (is_array($this->_map[$i][$j])) {
					$cell = $this->_map[$i][$j];
					$obj = $p4a->getObject($cell[0]);
					if ($obj === null) {
						$obj_as_string = $cell[0];
					} else {
						$obj_as_string = $obj->getAsString();
					}
					$rowspan = $cell[1];
					$colspan = $cell[2];
				}
				$string .= "<td rowspan='$rowspan' colspan='$colspan'>$obj_as_string</td>\n";
				$j += $colspan;
			}
			$string .= "</tr>\n";
		}
		$string .= "</table>\n";
		return $string;
	}
}