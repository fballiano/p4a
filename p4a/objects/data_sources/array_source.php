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

class P4A_Array_Source extends P4A_Data_Source
{
	var $_array = array();

	function P4A_Array_Source($name){
		P4A_Data_Source::P4A_Data_Source($name);
	}

	function load($array)
	{
		$this->build("P4A_Collection", "fields");
		$this->_array = array();
		$this->_array[-1] = array();

		foreach($array as $value) {
			$this->_array[] = $value;
		}

		$first_row = $array[0];
		foreach ($first_row as $field_name=>$value) {
			$this->fields->build("p4a_data_field",$field_name);
			$this->_array[-1][$field_name] = "";
		}
	}

	function row($num_row = NULL, $move_pointer = TRUE)
	{
		if ($num_row !== NULL) {
			$row = $this->_array[$num_row-1];
		} else {
			$num_row = $this->_pointer;
			$row = $this->_array[$num_row-1];
		}

		if ($move_pointer) {
			if ($this->actionHandler('beforeMoveRow') == ABORT) return ABORT;
			
			if ($this->isActionTriggered('onMoveRow')) {
				if ($this->actionHandler('onMoveRow') == ABORT) return ABORT;
			} else {
				if (!empty($row)) {
					$this->_pointer = $num_row;
	
					foreach($row as $field=>$value){
						$this->fields->$field->setValue($value);
					}
				} elseif ($this->getNumRows() == 0) {
					$this->newRow();
				}
			}
			
			$this->actionHandler('afterMoveRow');
		}

		return $row;
	}

	function getAll($from = 0, $count = 0)
	{
		if ($this->getNumRows()==0) {
			return array();
		}

		if ($from == 0 and $count == 0) {
			$array = $this->_array;
			array_shift($array);
			return $array;
		} else {
			$array = $this->_array;
			array_shift($array);
			return array_slice($array, $from, $count);
		}
	}

	function getNumRows()
	{
		return count($this->_array) - 1;
	}

	function getPkRow($pk)
	{
		foreach ($this->_array as $row) {
			if ($row[$this->_pk] == $pk) {
				return $row;
			}
		}
		return FALSE;
	}

	function deleteRow()
	{
		$pointer = $this->getRowNumber();
		unset($this->_array[$pointer-1]);
		parent::deleteRow();
	}
}
?>