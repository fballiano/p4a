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
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package p4a
 */

/**
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
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
	
	/**
	 * @return boolean
	 */
	public function isFirstRow()
	{
		return ($this->_pointer < 2);
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
	 * @return boolean
	 */
	public function isLastRow()
	{
		return ($this->_pointer == $this->getNumRows());
	}
	
	/**
	 * Moves uploaded files from P4A_UPLOADS_TMP_DIR to P4A_UPLOADS_DIR
	 * @throws P4A_Exception
	 */
	public function saveUploads()
	{
		while ($field = $this->fields->nextItem()) {
			$field_type = $field->getType();
			if ($field_type=='file') {
				$new_value  = $field->getNewValue();
				$old_value  = $field->getValue();
				$target_dir = P4A_UPLOADS_DIR . '/' . $field->getUploadSubpath();

				if (!is_dir($target_dir)) {
					if (!P4A_Mkdir_Recursive($target_dir)) {
						throw new P4A_Exception("Cannot create directory \"$target_dir\"", P4A_FILESYSTEM_ERROR);
					}
				}

				$a_new_value = explode(',', substr($new_value, 1, -1 ));
				$a_old_value = explode(',', substr($old_value, 1, -1 ));

				if ($old_value === null) {
					if ($new_value !== null) {
						$a_new_value[0] = P4A_Get_Unique_File_Name($a_new_value[6], $target_dir);
						unset($a_new_value[6]);
						$new_path = $target_dir . '/' . $a_new_value[0];
						$old_path = P4A_UPLOADS_DIR . '/' . $a_new_value[1];
						if (!rename($old_path, $new_path)) {
							throw new P4A_Exception("Cannot rename file \"$old_path\" to \"$new_path\"", P4A_FILESYSTEM_ERROR);
						}
						$a_new_value[1] = P4A_Strip_Double_Slashes(str_replace(P4A_UPLOADS_DIR , '', $new_path));
						$field->setNewValue('{' . join($a_new_value, ',') . '}');
					} else {
						$field->setNewValue(null);
					}
				} else {
					if ($new_value === null) {
						$path = $target_dir . '/' . $a_old_value[0];
						if (!@unlink($path) and @file_exists($path)) {
							throw new P4A_Exception("Cannot delete file \"$path\"", P4A_FILESYSTEM_ERROR);
						}
						$field->setNewValue(null);
					} elseif ($new_value!=$old_value) {
						$path = $target_dir . '/' . $a_old_value[0];
						if (!@unlink($path) and @file_exists($path)) {
							throw new P4A_Exception("Cannot delete file \"$path\"", P4A_FILESYSTEM_ERROR);
						}
						$a_new_value[0] = P4A_Get_Unique_File_Name($a_new_value[6], $target_dir);
						unset($a_new_value[6]);
						$new_path = $target_dir . '/' . $a_new_value[0];
						$old_path = P4A_UPLOADS_DIR . '/' . $a_new_value[1];
						if (!@rename($old_path, $new_path)) {
							throw new P4A_Exception("Cannot rename file \"$old_path\" to \"$new_path\"", P4A_FILESYSTEM_ERROR);
						}
						$a_new_value[1] = str_replace(P4A_UPLOADS_DIR , '', $new_path);
						$field->setNewValue('{' . join($a_new_value, ',') . '}');
					}
				}
			}
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
		
		if (is_array($fields_names)) {
			$tmp = array_keys($fields_names);
			if (is_numeric($tmp[0])) {
				$tmp = $fields_names;
				$fields_names = array();
				foreach ($tmp as $colname) {
					$fields_names[$colname] = P4A_Generate_Default_Label($colname);
				}
			}
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
			foreach ($fields_names as $col=>$tmp) {
				$tmp = str_replace("\n","",$row[$col]);
				$tmp = str_replace("\r","",$tmp);
				$strrow .= '"' . str_replace('"','""',$tmp) . "\"{$separator}";
			}
			$csv .= substr($strrow,0,-1) . "\n";
		}
		return 	$csv;
	}

	public function exportAsCSV($filename = '', $separator = ',', $fields_names = null)
	{
		if (!is_string($filename) or !strlen($filename)) {
			$filename = $this->getName() . ".csv";
		}
		
		if (!is_string($separator)) {
			$separator = ',';
		}
		
		P4A_Output_File($this->getAsCSV($separator, $fields_names), $filename);
	}

	/**
	 * @return void
	 */
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