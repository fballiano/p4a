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
 * Viale dei Mughetti 13/A											<br>
 * 10151 Torino (Italy)												<br>
 * Tel.:   (+39) 011 735645											<br>
 * Fax:    (+39) 011 735645											<br>
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
	 * Data source class.
	 * A data source is a generic data source... This means
	 * that you have the same methods for retreaving data from
	 * databases or whatever you may need.
	 * The data is stored as it would be in a database, with
	 * primary keys in tabular mode.
	 * This class manages data in arrays, database sources are
	 * managed by DB_SOURCE, text files sources are managed by
	 * TXT_SOURCE.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 * @link DB_SOURCE
	 * @link TXT_SOURCE
	 */
	class P4A_DATA_SOURCE extends P4A_OBJECT
	{
		/**
		 * The collection of all data browsers associated with the current data source.
		 * @access public
		 * @var array
		 * @see DATA_BROWSER
		 */
		var $data_browsers = array();

		/**
		 * Array containing the stored data.
		 * @access private
		 * @var array
		 */
		var $_data = array();

		/**
		 * Primary key name of the data source.
		 * A data source MUST have a primary key.
		 * A data source MUST have ONLY ONE primary key (could/should be changed in future).
		 * @access public
		 * @var string
		 */
		var $pk = NULL;

		/**
		 * Maps the position of every primary key's value into the data source ($_data).
		 * @access private
		 * @var array
		 */
		var $_map_pk = array();

		/**
		 * Define if data_source uses autoincrement for pk default value
		 * @access private
		 * @var boolean
		 */
		var $use_auto_increment = FALSE;

		/**
		 * Array containing structure of data.
		 * @access public
		 * @var array
		 */
		var $structure = array();

		/**
		 * A blank new record attending to be added.
		 * Used in new_row() state.
		 * The new_row has "-1" as index in the rows array.
		 * @access public
		 * @var array
		 */
		var $new_row = array();

		/**
		 * Defines if the data source is child of another data source.
		 * @access public
		 * @var boolean
		 */
		var $is_child = FALSE;

		/**
		 * Keeps the relation between this data source and the master data source.
		 * Applicable only if the currente data source is set as child.
		 * @access public
		 * @var string
		 */
		var $master_relation = NULL;

		/**
		 * In a master-detail data source this is the row in the master data source.
		 * @access private
		 * @var array
		 */
		var $_master_row = array();

		/**
		 * Class constructor.
		 * @param string	The name that will be used as identifier.
		 * @param array		The array to be loaded
		 * @access private
		 */
		function &p4a_data_source($name, $array_data = NULL)
		{
			parent::p4aObject($name);
			$this->addDataBrowser('default');
			if ($array_data !== NULL){
				$this->load($array_data);
			}
		}

		/**
		 * Sets the data source's primary key.
		 * @param string	The name of the primary key field.
		 * @access public
		 */
		function setPk($name)
		{
			$this->pk = $name;
		}

		/**
		 * Returns the data source's primary key.
		 * @access public
		 */
		function getPk()
		{
			return $this->pk;
		}

		/**
		 * Sets the structure of the data source.
		 * This mean that you set all the fields that data source will manage.
		 * @access public
		 * @param mixed		(string|array) Field name in a string or an indexed array with all field names.
		 * @see DATA_FIELD
		 */
		function setFields($fields)
		{
			if( is_string( $fields ) )
			{
				$fields = array($fields);
			}

			foreach($fields as $field_name){
				$this->structure[$field_name] = array('type' => 'text');
			}
		}

		/**
		 * Return the structure of the data browser.
		 * This means that returns an array with the name of every managed field.
		 * @return array
		 * @access public
		 */
		function getFields()
		{
			return array_keys($this->structure);
		}

		/**
		 * Removes all the db_source structure fields.
		 * @access public
		 * @see DATA_FIELD
		 * @see unset_structure()
		 */
		function unsetFields()
		{
			$this->unsetStructure();
		}

		/**
		 * Sets the db_source structure.
		 * @access public
		 */
		function setStructure($structure)
		{
			$this->structure = $structure;
		}

		/**
		 * Retrives the db_source structure
		 * @access public
		 * @return array
		 */
		function getStructure()
		{
			return $this->structure;
		}

		/**
		 * Removes all the db_source structure fields.
		 * @access public
		 * @see DATA_FIELD
		 */
		function unsetStructure()
		{
			$this->structure = array();
		}

		/**
		 * Sets the data type for a field.
		 * @access public
		 * @param string		The field name.
		 * @param string		The type.
		 */
		function setFieldType($field, $type)
		{
			if (! array_key_exists($field, $this->structure)){
				ERROR("Field $field doesn't exists");
			}
			$array_field = $this->structure[$field];
			$array_field['type'] = $type;
			$this->structure[$field] = $array_field;
		}

		/**
		 * Returns the data type of a field.
		 * @access public
		 * @return string
		 */
		function getFieldType($field)
		{
			return $this->structure[$field]['type'];
		}

		/**
		 * Sets the default value for a field.
		 * @access public
		 * @param string		The field name
		 * @param string		The default value
		 */
		function setFieldDefaultValue($field, $default_value)
		{
			$array_field = $this->structure[$field];
			$array_field['default_value'] = $default_value;
			$this->structure[$field] = $array_field;
		}

		/**
		 * Returns the default value for a field.
		 * @access public
		 * @return string
		 */
		function getFieldDefaultValue($field)
		{
			if (array_key_exists('default_value', $this->structure[$field])
			 	and $this->structure[$field]['default_value'] !== NULL)
			{
				return $this->structure[$field]['default_value'];
			}elseif($field == $this->pk){
				return $this->nextPk();
			}else{
				return NULL;
			}
		}

		/**
		 * Imports data into the data source checking and mapping the primary key.
		 * The imported data must be a dictionary array.
		 * E.g.: $array_dict[0] = array( "field1" => "value1", "field2" => "value2" )
		 * If the field structure is not set yet, that it will be extracted from
		 * the dictionary.
		 * @access public
		 * @param array		Data to import as dictionary array.
		 * @param string	Primaty key's name.
		 */
		function load($array_dict, $pk = NULL)
		{

			//Set pk
			if ( $pk !== NULL ){
				$this->setPk($pk);
			}

			//Array2dictionary
			$array_keys = array_keys($array_dict);
			if (! is_array($array_dict[$array_keys[0]]))
			{
				$new_array_dict = array();
				foreach($array_dict as $value){
					$new_array_dict[] = array('value'=>$value);
				}
				$array_keys = array_keys($new_array_dict);
				$array_dict = $new_array_dict;
			}

			// Setting structure if not yet done.
			if (count($this->getFields()) === 0){
				$this->setFields(array_keys($array_dict[$array_keys[0]]));
			}

			//Set pk
			if (! $this->getPk())
			{
				$array_fields = $this->getFields();
				$this->setPk($array_fields[0]);
			}

			if (! in_array($this->pk, $this->getFields()))
			{
				error("PK NOT FOUND");
			}
			else
			{
				$i = 0;
				foreach($array_dict as $key=>$row)
				{
					$this->_map_pk[$row[$this->pk]] = $i;
					$this->_data[$i] = $row;
					$i++;
				}
			}

			//Set fields for data_browsers
			foreach(array_keys($this->data_browsers) as $browser_name)
			{
    			if (! $this->data_browsers[$browser_name]->getFields()){
    				$this->data_browsers[$browser_name]->setFields($this->getFields());
    			}

    			$this->data_browsers[$browser_name]->moveFirst();
			}
		}

		/**
		 * Wrapper for load().
		 * @access public
		 * @deprecated
		 * @param array		Data to import as dictionary array.
		 * @param string	Primaty key's name.
		 * @see load()
		 */
		function loadWithPk($array_dict, $pk = NULL)
		{
			$this->load($array_dict, $pk);
		}

		/**
		 * Returns a row searching for it by index.
		 * @param integer	Row number.
		 * @access public
		 */
		function getRow($num_row)
		{
			if ($num_row == -1){
				return $this->getNewRow();
			}elseif( $num_row == 0 or !array_key_exists(($num_row -1), $this->_data) ){
				return NULL;
			}else{
				return $this->_data[$num_row -1];
			}
		}

		/**
		 * Returns a row searching for it by primary key's value.
		 * @param string	The primary key's value.
		 * @access public
		 */
		function getPkRow($pk)
		{
			if (array_key_exists($pk, $this->_map_pk)){
				return $this->getRow($this->_map_pk[$pk] + 1);
			}else{
				return NULL;
			}
		}

		/**
		 * Returns a row number searching for it by primary key's value.
		 * @param string	The primary key's value.
		 * @access public
		 */
		function getPkRowNumber($pk)
		{
			if (array_key_exists($pk, $this->_map_pk)){
				return $this->_map_pk[$pk] + 1;
			}else{
				return NULL;
			}
		}

		/**
		 * Returns the number of rows in the data source.
		 * @access public
		 */
		function getNumRows()
		{
			return sizeof($this->_map_pk);
		}

		/**
		 * Tells the data source what is the sequence of the primary key.
		 * @param string				Sequence name.
		 * @access public
		 */
		function setPkSequence($pk_sequence = NULL)
		{
			if ($pk_sequence){
				$this->setAutoIncrement(FALSE);
			}
			$this->pk_sequence = $pk_sequence;
		}

		/**
		 * Returns the data source sequence.
		 * @return string
		 * @access public
		 */
		function getPkSequence()
		{
			return $this->pk_sequence;
		}

		/**
		 * Set the data_source for use autoincrement
		 * @access public
		 */

		function setAutoIncrement($use = TRUE)
		{
			if ($use){
				$this->setPkSequence(NULL);
			}
			$this->use_auto_increment = $use;
		}

		/**
		 * Set the data_source for use autoincrement
		 * @return string
		 * @access public
		 */

		function getAutoIncrement()
		{
			return $this->use_auto_increment;
		}

		/**
		 * Increments the primary key's sequence.
		 * @return string
		 * @access public
		 */

		function nextPk()
		{
			$db =& P4A_DB::singleton();
			if ($this->getPkSequence()){
				return $db->nextId($this->getPkSequence());
			}elseif($this->getAutoIncrement()){
				return max(array_keys($this->_map_pk)) + 1;
			}else{
				//ERROR('NEXT PK IMPOSSIBLE');
				return NULL;
			}
		}

		/**
		 * Updates a row replacing its data with the passed one.
		 * The row is crawled by index.
		 * @param integer	The row index.
		 * @param array		Array containing all data.
		 * @access public
		 */
		function updateRow($row_number, $row)
		{
			$pk = $this->_data[$row_number -1][$this->pk];
			$new_pk = $row[$this->pk];
			if (! array_key_exists($pk, $this->_map_pk)){
				ERROR('PK NOT FOUND');
			}elseif($new_pk != $pk AND
				array_key_exists($new_pk, $this->_map_pk)){
				ERROR('DUPLICATE PK');
			}elseif($new_pk != $pk){
				$this->_map_pk[$new_pk] = $this->_map_pk[$pk];
				unset($this->_map_pk[$pk]);
			}

			//The row can also have a subset of the record's fields
			foreach($row as $key=>$value){
				$this->_data[$this->_map_pk[$new_pk]][$key] = $value;
			}

		}

		/**
		 * Deletes a row from the data source.
		 * The $row parameter is useful when
		 * an onDelete event is trigger by the data source.
		 * @param integer	The row's index
		 * @param array		The row that will be deleted.
		 * @access public
		 */
		function deleteRow($row_number, $row=NULL)
		{
			if ($row_number != -1)
			{
	    		$pk = $this->_data[$row_number -1][$this->pk];
	    		unset($this->_map_pk[$pk]);
	    		unset($this->_data[$row_number -1]);

	    		if (count($this->_data))
	    		{
		    		$max_pk = max(array_keys($this->_data));

		    		$pk_key = $this->pk;
		    		for($i = $row_number; $i<=$max_pk; $i++)
		    		{
		    			// Data compatting
		    			$old_row_number = $i;
		    			$new_row_number = $i-1;
		    			$pk_value = $this->_data[$old_row_number][$pk_key];

		    			// Copying old row in new row
		    			$this->_data[$new_row_number] = $this->_data[$old_row_number];
		    			// Destroying old row
		    			unset($this->_data[$old_row_number]);
		    			// Rimapping pk to new row
		    			$this->_map_pk[$pk_value] = $new_row_number;
		    		}
	    		}
			}
			else //new row
			{
	    		unset($this->_data[$row_number]);
			}
		}

		/**
		 * Inserts a row in the data source.
		 * @param array		The row as dictionary.
		 * @access public
		 * @return integer	The index of the new row.
		 */
		function insertRow($row = NULL)
		{
			// Default insert new row
			if ($row === NULL){
				$row = $this->new_row;
			}

			// If PK already exist error
			$pk = $row[$this->pk];
			if(array_key_exists($pk, $this->_map_pk)){
				ERROR('DUPLICATE PK');
			}
			else
			{
				//Getting row number
				if(is_array($this->_data) && count($this->_data)){
					$num_row = max(array_keys($this->_data)) + 1 ;
				} else {
					$num_row = 0;
				}
				$this->_map_pk[$pk] = $num_row;
				$this->_data[$num_row] = $row;
				return $num_row + 1;
			}
		}

		/**
		 * Sets the current data source as a detail of a data browser.
		 * It's a data browser's detail because you always need reference to current position.
		 * This method make the master data browser move to the first row.
		 * @param databrowser	The master data browser.
		 * @param string		The master->detail relationship.
		 * @access public
		 */
		function setAsDetail(&$data_browser, $relation=NULL)
		{
			$this->is_child = TRUE;
			if ($relation !== NULL){
				$this->master_relation = $relation;
			}
			$data_browser->addChild($this);
			$this->setMasterRow($data_browser->getCurrentRow());
		}

		/**
		 * Sets the master row from the master data source.
		 * @param array		The master row.
		 * @access public
		 */
		function setMasterRow($row)
		{
			$this->_master_row = $row;
			$this->load();
		}

		/**
		 * Returns the current row number of the specified data source.
		 * @param string	The data browser's name.
		 * @return integer
		 * @access public
		 * @see DATA_BROWSER::getRowNumber()
		 */
		function getRowNumber($name='default')
		{
			return $this->data_browsers[$name]->getRowNumber();
		}

		/**
		 * Moves the specified data browser to a row.
		 * @param integer	The row number.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::moveRow()
		 */
		function moveRow($num_row, $name = 'default')
		{
			$this->data_browsers[$name]->moveRow($num_row);
		}

		/**
		 * Moves the specified data browser to a row searching for it by primary key's value.
		 * @param integer	The pk row.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::moveRow()
		 */
		function movePkRow($pk, $name = 'default')
		{
			$this->data_browsers[$name]->movePkRow($pk);
		}

		/**
		 * Moves the specified data browser to the next row.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::moveNext()
		 */
		function moveNext($name = 'default')
		{
			$this->data_browsers[$name]->moveNext();
		}

		/**
		 * Moves the specified data browser to the previous row.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::movePrev()
		 */
		function movePrev($name = 'default')
		{
			$this->data_browsers[$name]->movePrev();
		}

		/**
		 * Moves the specified data browser to the first row.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::moveFirst()
		 */
		function moveFirst($name = 'default')
		{
			$this->data_browsers[$name]->moveFirst();
		}

		/**
		 * Moves the specified data browser to the last row.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::moveLast()
		 */
		function moveLast($name = 'default')
		{
			$this->data_browsers[$name]->moveLast();
		}

		/**
		 * Moves the specified data browser to the new row.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::moveNew()
		 */
		function moveNew($name = 'default')
		{
			$this->data_browsers[$name]->moveNew();
		}

		/**
		 * Returns the currently pointed row by the passed data browser.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::getCurrentRow()
		 */
		function getCurrentRow($name = 'default')
		{
			return $this->data_browsers[$name]->getCurrentRow();
		}

		/**
		 * Returns the row next to the currently pointer row by the passed data browser.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::getNextRow()
		 */
		function getNextRow($name = 'default')
		{
			return $this->data_browsers[$name]->getNextRow();
		}

		/**
		 * Returns the row previous to the currently pointer row by the passed data browser.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::getPrevRow()
		 */
		function getPrevRow($name = 'default')
		{
			return $this->data_browsers[$name]->getPrevRow();
		}

		/**
		 * Returns the first row pointed by data browser.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::getFirstRow()
		 */
		function getFirstRow($name = 'default')
		{
			return $this->data_browsers[$name]->getFirstRow();
		}

		/**
		 * Returns the last row pointed by data browser.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::getLastRow()
		 */
		function getLastRow($name = 'default')
		{
			return $this->data_browsers[$name]->getLastRow();
		}

		/**
		 * Sets the page limit for the passed data browser.
		 * @param integer	How many rows can be stored in a page.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::setPageLimit()
		 */
		function setPageLimit($limit, $name = 'default')
		{
			$this->data_browsers[$name]->setPageLimit($limit);
		}

		/**
		 * Moves the passed data browser to a page.
		 * @param integer	The page number.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::movePage()
		 */
		function movePage($num_page, $name = 'default')
		{
			$this->data_browsers[$name]->movePage($num_page);
		}

		/**
		 * Moves the passed data browser to the next page.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::moveNextPage()
		 */
		function moveNextPage($name = 'default')
		{
			$this->data_browsers[$name]->moveNextPage();
		}

		/**
		 * Moves the passed data browser to the previous page.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::movePrevPage()
		 */
		function movePrevPage($name = 'default')
		{
			$this->data_browsers[$name]->movePrevPage();
		}

		/**
		 * Moves the passed data browser to the next page.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::moveFirstPage()
		 */
		function moveFirstPage($name = 'default')
		{
			$this->data_browsers[$name]->moveFirstPage();
		}

		/**
		 * Moves the passed data browser to the last page.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::moveLastPage()
		 */
		function moveLastPage($name = 'default')
		{
			$this->data_browsers[$name]->moveLastPage();
		}

		/**
		 * Returns a page form the passed data browser.
		 * @param integer	The page number.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::getPage()
		 */
		function getPage($num_page, $name = 'default')
		{
			return $this->data_browsers[$name]->getPage($num_page);
		}

		/**
		 * Returns the page next to the currently poiter one form the passed data browser.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::getNextPage()
		 */
		function getNextPage($name = 'default')
		{
			return $this->data_browsers[$name]->getNextPage();
		}

		/**
		 * Returns the page previous to the currently poiter one form the passed data browser.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::getPrevPage()
		 */
		function getPrevPage($name = 'default')
		{
			return $this->data_browsers[$name]->getPrevPage();
		}

		/**
		 * Returns the last page of the passed data browser.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::getLastPage()
		 */
		function getLastPage($name = 'default')
		{
			return $this->data_browsers[$name]->getLastPage();
		}

		/**
		 * Returns the first page of the passed data browser.
		 * @param string	The data browser's name.
		 * @access public
		 * @see DATA_BROWSER::getFirstPage()
		 */
		function getFirstPage($name = 'default')
		{
			return $this->data_browsers[$name]->getFirstPage();
		}

		/**
		 * Returns all the rows between the two passed index.
		 * @param integer	The first row that you want.
		 * @param integer	The last row that you want.
		 * @access public
		 */
		function getRowsFromTo($from, $to)
		{
			$array_return = array();
			$i = $from;
			//Asc Order
			if ($to > $from )
			{
				while ($i <= $to and array_key_exists(($i - 1),$this->_data))
				{
					$array_return[$i] = $this->_data[$i -1];
					$i++;
				}
			}//Desc Order
			else
			{
				while ($i >= $to and array_key_exists(($i - 1),$this->_data))
				{
					$array_return[$i] = $this->_data[$i -1];
					$i--;
				}
			}

			return $array_return;
		}

		/**
		 * Returns all the rows between in the data source.
		 * @access public
		 */
		function getAll()
		{
			return $this->getRowsFromTo( 1, $this->getNumRows() ) ;
		}
	}
?>