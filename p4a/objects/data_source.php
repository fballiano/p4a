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

class P4A_Data_Source extends P4A_Object
{
	var $_pointer = NULL;

	var $_pk = NULL;

	var $_limit = NULL;
	var $_offset = NULL;

	var $_num_rows = NULL;
	var $_num_pages = NULL;
	var $_page_limit = 10;

	var $_fields = NULL;

	var $_is_read_only = FALSE;
	var $_is_sortable = TRUE;

	var $fields = NULL; //P4A_Collection

	function P4A_Array_Source($name) {
		parent::P4A_Object($name);
		$this->build("P4A_Collection", "fields");
	}

	function load() {
		return;
	}

	function row($num_row = NULL, $move_pointer = TRUE) {
		return ;
	}

	function newRow()
	{
		$this->_pointer = 0;
		while($field =& $this->fields->nextItem()) {
			$field->setDefaultValue();
		}
	}

	function isNew()
	{
		if ($this->_pointer === 0) {
			return true;
		} else {
			return false;
		}
	}

	function getAll($from = 0, $count = 0) {
		return;
	}

	function getNumRows()
	{
		return;
	}

	function getRowNumber()
	{
		return $this->_pointer;
	}

	function firstRow()
	{
		$num_rows = $this->getNumRows();

		if ($num_rows > ($this->_pointer-1)) {
			$this->_pointer = 1;
			return $this->row();
		} elseif($this->_pointer !== $num_rows) {
			$this->newRow();
		}
		return;
	}

	function prevRow()
	{
		$num_rows = $this->getNumRows();

		if ($this->_pointer > 1){
			$this->_pointer--;
			return $this->row();
		} elseif($this->_pointer !== $num_rows) {
			$this->firstRow();
		}
		return;
	}

	function nextRow()
	{
		$num_rows = $this->getNumRows();

		if ($num_rows > $this->_pointer) {
			$this->_pointer++;
			return $this->row();
		}elseif(($num_rows == 0) and (!$this->isNew())){
			return $this->newRow();
		}
		return;
	}

	function lastRow()
	{
		$num_rows = $this->getNumRows();

		if ($num_rows > $this->_pointer or $num_rows < $this->_pointer) {
			$this->_pointer = $num_rows;
			return $this->row();
		} elseif($this->_pointer !== $num_rows) {
			$this->newRow();
		}
		return;
	}

	function getOffset()
	{
		$limit = $this->getPageLimit();
		return ($this->getNumPage() * $limit) - $limit;
	}

	function setPageLimit($page_limit)
	{
		$this->_page_limit = $page_limit;
	}

	function getPageLimit()
	{
		return $this->_page_limit;
	}

	function getNumPages()
	{
		$num_rows = $this->getNumRows();
		$page_limit = $this->getPageLimit();

		if ($num_rows == 0) {
			return 0;
		} else {
 			return intval( ($num_rows - 1) / $page_limit ) + 1;
		}
	}

	function getNumPage()
	{
		$row_number = $this->_pointer;
		$page_limit = $this->_page_limit;

		return intval(($row_number - 1) / $page_limit) + 1;
	}

	function page($num_page = NULL, $move_pointer=TRUE)
	{
		$limit = $this->getPageLimit();
		$num_pages = $this->getNumPages();

		if ($num_page === NULL) {
			$num_page = $this->getNumPage();
		} elseif (($num_page < 1) or ($num_page > $num_pages)) {
			return;
		}

		$offset = ($num_page * $limit) - $limit;
		$rows = $this->getAll($offset, $limit);

		if ($move_pointer) {
			$this->_pointer = $offset + 1;
			$row = $rows[0];
			foreach($row as $field=>$value) {
				$this->fields->$field->setValue($value);
			}
		}
		return $rows;
	}

	function firstPage()
	{
		return $this->page(1);
	}

	function prevPage()
	{
		$current_page = $this->getNumPage();
		return $this->page($current_page - 1);
	}

	function nextPage()
	{
		$current_page = $this->getNumPage();
		return $this->page($current_page + 1);
	}

	function lastPage()
	{
		$num_pages = $this->getNumPages();
		return $this->page($num_pages);
	}

	function setPk($pk)
	{
		$this->_pk = $pk;
	}

	function getPk()
	{
		return $this->_pk;
	}

	function getPkValues(){
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
			P4A_Error("NO PK");
		}
	}

	function getPkRow($pk)
	{
		return;
	}

	function getAsCSV($separator = ',', $fields_names = null)
	{
		if ($fields_names === true or is_array($fields_names)) {
			$insert_header = true;
		} else {
			$insert_header = false;
		}

		if ($fields_names === null or $fields_names === false or $fields_names === true) {
			$fields_names = array();
			while ($field =& $this->fields->nextItem()) {
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

	function exportToCSV($filename = "", $separator = ',', $fields_names = null)
	{
		$this->exportAsCSV($filename, $separator, $fields_names);
	}

	function exportAsCSV($filename = "", $separator = ',', $fields_names = null)
	{
		$p4a =& P4A::singleton();

		if (!strlen($filename)) {
			$filename = $this->getName() . ".csv";
		}

		$output = $this->getAsCSV($separator, $fields_names);

		header("Cache-control: private");
		header("Content-Type: text/comma-separated-values; charset=" . $p4a->i18n->getCharset());
		header("Content-Disposition: attachment; filename=" . $filename);
		header("Content-Length: " . strlen($output));
		echo $output;
		die();
	}

	function deleteRow()
	{
		$num_rows = $this->getNumRows();

		if ($this->isNew() and $num_rows > 0) {
			$this->lastRow();
		} elseif (!$this->isNew() and $num_rows == 0) {
			$this->newRow();
		} elseif ($this->_pointer > $this->getNumRows()) {
			$this->lastRow();
		} else {
			$this->row();
		}
	}
}
?>