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
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
abstract class P4A_Data_Source extends P4A_Object
{
	/**
	 * @var integer
	 */
	protected $_pointer = null;
	
	/**
	 * @var string
	 */
	protected $_pk = null;
	
	/**
	 * @var integer
	 */
	protected $_limit = null;
	
	/**
	 * @var integer
	 */
	protected $_offset = null;
	
	/**
	 * @var integer
	 */
	protected $_num_rows = null;
	
	/**
	 * @var integer
	 */
	protected $_num_pages = null;
	
	/**
	 * @var integer
	 */
	protected $_page_limit = 10;
	
	/**
	 * @var boolean
	 */
	protected $_is_read_only = false;
	
	/**
	 * @var boolean
	 */
	protected $_is_sortable = false;
	
	/**
	 * @var array
	 */
	protected $_order = array();
	
	/**
	 * @var P4A_Collection
	 */
	public $fields = null;

	public function __construct($name)
	{
		parent::__construct($name);
		$this->build("P4A_Collection", "fields");
	}

	public function load() {
		return;
	}

	public function row($num_row = null, $move_pointer = true) {
		return ;
	}

	/**
	 * @return P4A_Data_Source
	 */
	public function newRow()
	{
		if ($this->actionHandler('beforeMoveRow') == ABORT) return ABORT;

		$this->_pointer = 0;
		while ($field = $this->fields->nextItem()) {
			$field->setValue(null);
			$field->setDefaultValue();
		}

		$this->actionHandler('afterMoveRow');
		
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isNew()
	{
		if ($this->_pointer === 0) return true;
		return false;
	}

	/**
	 * gets/sets sortable state
	 * @param boolean $value
	 * @return boolean
	 */
	public function isSortable($value = null)
	{
		if ($value === null) return $this->_is_sortable;
		$this->_is_sortable = $value;
		return $this;
	}

	/**
	 * @param string $field
	 * @param string $direction
	 * @return P4A_Data_Source
	 */
	public function addOrder($field, $direction = P4A_ORDER_ASCENDING)
	{
		$this->_order[$field] = strtoupper($direction);
		return $this;
	}

	/**
	 * alias for addOrder()
	 * @param string $field
	 * @param string $direction
	 * @return P4A_Data_Source
	 */
	public function setOrder($field, $direction = P4A_ORDER_ASCENDING)
	{
		$this->_order = array();
		$this->addOrder($field, $direction);
		return $this;
	}

	/**
	 * @return array
	 */
	public function getOrder()
	{
		$pk = $this->getPk();
		$order = $this->_order;
		if (is_string($pk)) {
			if (!array_key_exists($pk,$order)) {
				$order[$pk] = P4A_ORDER_ASCENDING;
			}
		} elseif (is_array($pk)) {
			foreach ($pk as $p) {
				if (!array_key_exists($p,$order)) {
					$order[$p] = P4A_ORDER_ASCENDING;
				}
			}
		}
		return $order;
	}

	/**
	 * @return boolean
	 */
	public function hasOrder()
	{
		return (sizeof($this->_order) > 0);
	}

	/**
	 * @param string $field
	 * @return P4A_Data_Source
	 */
	public function dropOrder($field = null)
	{
		if ($field === null) {
			$this->_order = array();
		} else {
			unset($this->_order[$field]);
		}
		return $this;
	}

	public function getAll($from = 0, $count = 0) {
		return;
	}

	public function getNumRows()
	{
		return;
	}

	public function getRowNumber()
	{
		return $this->_pointer;
	}

	/**
	 * @return P4A_Data_Source
	 */
	public function updateRowPosition()
	{
	   return $this;
	}

	public function firstRow()
	{
		$num_rows = $this->getNumRows();

		if ($num_rows >= ($this->_pointer-1)) {
			$this->_pointer = 1;
			return $this->row();
		} elseif( $this->_pointer !== $num_rows) {
			$this->newRow();
		}
	}

	public function prevRow()
	{
		$num_rows = $this->getNumRows();

		if ($this->_pointer > 1){
			$this->_pointer--;
			return $this->row();
		} elseif ($this->_pointer !== $num_rows) {
			$this->firstRow();
		}
	}

	public function nextRow()
	{
		$num_rows = $this->getNumRows();

		if ($num_rows > $this->_pointer) {
			$this->_pointer++;
			return $this->row();
		} elseif (($num_rows == 0) and (!$this->isNew())) {
			return $this->newRow();
		}
	}

	public function lastRow()
	{
		$num_rows = $this->getNumRows();

		if ($num_rows > $this->_pointer or $num_rows < $this->_pointer) {
			$this->_pointer = $num_rows;
			return $this->row();
		} elseif ($this->_pointer !== $num_rows) {
			$this->newRow();
		}
	}

	/**
	 * @return integer
	 */
	public function getOffset()
	{
		$limit = $this->getPageLimit();
		return ($this->getNumPage() * $limit) - $limit;
	}

	/**
	 * @param integer $page_limit
	 * @return P4A_Data_Source
	 */
	public function setPageLimit($page_limit)
	{
		$this->_page_limit = $page_limit;
		return $this;
	}

	/**
	 * @return integer
	 */
	public function getPageLimit()
	{
		return $this->_page_limit;
	}

	/**
	 * Returns the number of pages in the data source
	 * @return integer
	 */
	public function getNumPages()
	{
		$num_rows = $this->getNumRows();
		$page_limit = $this->getPageLimit();

		if ($num_rows == 0) {
			return 0;
		}
		if ($page_limit)  {
			return intval(($num_rows - 1) / $page_limit) + 1;
		}
		return 1;
	}

	/**
	 * Returns the number of the current page
	 * @return integer
	 */
	public function getNumPage()
	{
		$row_number = $this->_pointer;
		$page_limit = $this->_page_limit;

		if ($page_limit)  {
			return intval(($row_number - 1) / $page_limit) + 1;
		}
		return 1;
	}

	/**
	 * Returns a page of date (some rows)
	 * @return array
	 */
	public function page($num_page = null, $move_pointer=true)
	{
		$limit = $this->getPageLimit();
		$num_pages = $this->getNumPages();

		if ($num_page === null) {
			$num_page = $this->getNumPage();
		} elseif (($num_page < 1) or ($num_page > $num_pages)) {
			return;
		}

		$offset = ($num_page * $limit) - $limit;
		$rows = $this->getAll($offset, $limit);

		if ($move_pointer) {
			if ($this->actionHandler('beforemoverow') == ABORT) return ABORT;

			if ($this->isActionTriggered('onmoverow')) {
				if ($this->actionHandler('onmoverow') == ABORT) return ABORT;
			} else {
				$this->_pointer = $offset + 1;
				$row = $rows[0];
				foreach($row as $field=>$value) {
					$this->fields->$field->setValue($value);
				}
			}

			$this->actionHandler('aftermoverow');
		}
		return $rows;
	}

	public function firstPage($move_pointer = true)
	{
		return $this->page(1, $move_pointer);
	}

	public function prevPage($move_pointer = true)
	{
		$current_page = $this->getNumPage();
		return $this->page($current_page - 1, $move_pointer);
	}

	public function nextPage($move_pointer = true)
	{
		$current_page = $this->getNumPage();
		return $this->page($current_page + 1, $move_pointer);
	}

	public function lastPage($move_pointer = true)
	{
		$num_pages = $this->getNumPages();
		return $this->page($num_pages, $move_pointer);
	}

	/**
	 * @param string $pk
	 * @return P4A_Data_Source
	 */
	public function setPk($pk)
	{
		$this->_pk = $pk;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPk()
	{
		return $this->_pk;
	}

	public function getPkValues()
	{
		$pks = $this->getPk();

		if (is_string($pks)) {
			return $this->fields->$pks->getValue();
		} elseif (is_array($pks)) {
			$return = array();
			foreach ($pks as $pk) {
				$return[$pk] = $this->fields->$pk->getValue();
			}
			return $return;
		} else {
			trigger_error("P4A_Data_Source::getPkValues(): no primary key defined");
		}
	}

	public function getPkRow($pk)
	{
		return;
	}

	public function getAsCSV($separator = ',', $fields_names = null)
	{
		if ($fields_names === true or is_array($fields_names)) {
			$insert_header = true;
		} else {
			$insert_header = false;
		}

		if ($fields_names === null or $fields_names === false or $fields_names === true) {
			$fields_names = array();
			while ($field = $this->fields->nextItem()) {
				$name = $field->getName();
				$fields_names[$name] = $name;
			}
		}

		$csv = "";
		$rows = $this->getAll();

		if ($insert_header) {
			array_unshift($rows, $fields_names);
		}

		foreach ($rows as $row) {
			$strrow = "";
			foreach ($row as $key=>$col) {
				if (in_array($key, array_keys($fields_names))) {
					$col = str_replace("\n","",$col);
					$col = str_replace("\r","",$col);
					$strrow .= '"' . str_replace('"','""',$col) . "\"{$separator}";
				}
			}
			$csv .= substr($strrow,0,-1) . "\n";
		}
		return 	$csv;
	}

	public function exportToCSV($filename = '', $separator = ',', $fields_names = null)
	{
		$this->exportAsCSV($filename, $separator, $fields_names);
	}

	public function exportAsCSV($filename = '', $separator = ',', $fields_names = null)
	{
		if (!strlen($filename)) {
			$filename = $this->getName() . ".csv";
		}
		
		P4A_Output_File($this->getAsCSV($separator, $fields_names), $filename);
	}

	public function deleteRow()
	{
		$num_rows = $this->getNumRows();

		if ($this->isNew() and $num_rows > 0) {
			$this->firstRow();
		} elseif (!$this->isNew() and $num_rows == 0) {
			$this->newRow();
		} elseif ($this->_pointer > $this->getNumRows()) {
			$this->firstRow();
		} else {
			$this->row();
		}
	}
}