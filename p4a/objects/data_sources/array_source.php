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
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
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

	public function load(array $array)
	{
		$this->_array = array();
		$this->_array[-1] = array();
		
		if (empty($array)) return;
		
		$first_row = $array[0];
		if (!is_array($first_row)) {
			foreach($array as $value) {
				$this->_array[] = array('0'=>$value);
			}
			$this->setPK('0');
			$first_row = array('0'=>$first_row);
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
	}

	public function row($num_row = null, $move_pointer = true)
	{
		if ($num_row !== null) {
			$row = $this->_array[$num_row-1];
		} else {
			$num_row = $this->_pointer;
			$row = $this->_array[$num_row-1];
		}

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