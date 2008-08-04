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
 * To contact the authors write to:                                     <br />
 * CreaLabs SNC                                                         <br />
 * Via Medail, 32                                                       <br />
 * 10144 Torino (Italy)                                                 <br />
 * Website: {@link http://www.crealabs.it}                              <br />
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
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
class P4A_Array_Source extends P4A_Data_Source
{
	/**
	 * @var array
	 */
	protected $_array = array();
	
	/**
	 * @var P4A_Collection
	 */
	public $fields = null;

	/**
	 * @param array $array
	 * @return P4A_Array_Source
	 */
	public function load(array $array)
	{
		$this->_array = array();
		$this->_array[-1] = array();

		if (empty($array)) return;

		$first_row = $array[0];
		if (!is_array($first_row)) {
			foreach($array as $value) {
				$this->_array[] = array('f0'=>$value);
			}
			$this->setPK('f0');
			$first_row = array('f0'=>$first_row);
		} else {
			foreach($array as $value) {
				$this->_array[] = $value;
			}
		}

		foreach ($first_row as $field_name=>$value) {
			if (!isset($this->fields->$field_name)) {
				$this->fields->build('P4A_Data_Field', $field_name);
			}
			$this->_array[-1][$field_name] = '';
		}

		return $this;
	}
	

	public function row($num_row = null, $move_pointer = true)
	{
		if ($num_row !== null) {
			$row = @$this->_array[$num_row-1];
		} else {
			$num_row = $this->_pointer;
			$row = @$this->_array[$num_row-1];
		}
		
		if ($row === null) $row = array();

		if ($move_pointer) {
			if ($this->actionHandler('beforemoverow') == ABORT) return ABORT;

			if ($this->isActionTriggered('onmoverow')) {
				if ($this->actionHandler('onmoverow') == ABORT) return ABORT;
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

			$this->actionHandler('aftermoverow');
		}

		return $row;
	}

	public function getAll($from = 0, $count = 0)
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

	public function getNumRows()
	{
		return count($this->_array) - 1;
	}

	public function getPkRow($pk)
	{
		foreach ($this->_array as $row) {
			if ($row[$this->_pk] == $pk) {
				return $row;
			}
		}
		return false;
	}

	public function deleteRow()
	{
		$pointer = $this->getRowNumber();
		unset($this->_array[$pointer-1]);
		parent::deleteRow();
	}
}