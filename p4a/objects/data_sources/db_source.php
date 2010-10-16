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
 * Read/write data from/to a database.
 * 
 * Note that P4A_DB_Source is case sensitive when handling
 * schemas/tables/columns names, thus if your table name
 * is "products" (lowercase) then type it lowercase in P4A too,
 * if it is "PRODUCTS" (uppercase) then type it uppercase
 * in P4A too and so on...
 * 
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class P4A_DB_Source extends P4A_Data_Source
{
	/**
	 * @var string
	 */
	protected $_DSN = null;
	
	/**
	 * @var string
	 */
	protected $_pk = null;
	
	/**
	 * @var string
	 */
	protected $_table = null;
	
	/**
	 * @var string
	 */
	protected $_table_alias = null;
	
	/**
	 * @var string
	 */
	protected $_schema = null;
	
	/**
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * @var array
	 */
	protected $_join = array();
	
	/**
	 * @var string
	 */
	protected $_where = null;
	
	/**
	 * @var array
	 */
	protected $_group = array();
	
	/**
	 * @var boolean
	 */
	protected $_is_sortable = true;
	
	/**
	 * @var string
	 */
	protected $_query = null;
	
	/**
	 * @var array
	 */
	protected $_multivalue_fields = array();
	
	/**
	 * @var array
	 */
	protected $_filters = array();
	
	/**
	 * @var array
	 */
	protected $_tables_metadata = array();

	/**
	 * @param string $DSN
	 * @return P4A_DB_Source
	 */
	public function setDSN($DSN)
	{
		$this->_DSN = $DSN;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDSN()
	{
		return $this->_DSN;
	}

	/**
	 * Sets the main table for this data source.
	 * If you pass a database view here, remember that
	 * you have to call setPk() on this P4A_DB_Source
	 * and setSequence() on the P4A_Data_Fields (if needed)
	 * because those data won't be autodetected.
	 * @param string $table
	 * @param string $alias
	 * @return P4A_DB_Source
	 */
	public function setTable($table, $alias = null)
	{
		$this->_table = $table;
		$this->_table_alias = $alias;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTable()
	{
		return $this->_table;
	}
	
	/**
	 * @return string
	 */
	public function setTableAlias($alias)
	{
		$this->_table_alias = $alias;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getTableAlias()
	{
		return $this->_table_alias;
	}
	
	/**
	 * @param string $schema
	 * @return P4A_DB_Source
	 */
	public function setSchema($schema)
	{
		$this->_schema = $schema;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSchema()
	{
		return $this->_schema;
	}

	/**
	 * @param array $fields
	 * @return P4A_DB_Source
	 */
	public function setFields(array $fields)
	{
		$this->_fields = $fields;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getFields()
	{
		return $this->_fields;
	}

	/**
	 * Add a join (default join type is INNER)
	 * @param string|array $table Table name or table/alias array (eg: array("table_name", "alias"))
	 * @param string $clausole
	 * @param array $fields
	 * @param string $schema
	 * @return P4A_DB_Source
	 */
	public function addJoin($table, $clausole, $fields = '*', $schema = null)
	{
		$this->_join[] = array('INNER', $table, $clausole, $fields, $schema);
		return $this;
	}
	
	/**
	 * Add an inner join
	 * @param string|array $table Table name or table/alias array (eg: array("table_name", "alias"))
	 * @param string $clausole
	 * @param array $fields
	 * @param string $schema
	 * @return P4A_DB_Source
	 */
	public function addJoinInner($table, $clausole, $fields = '*', $schema = null)
	{
		$this->addJoin($table, $clausole, $fields, $schema);
		return $this;
	}
	
	/**
	 * Add a left join
	 * @param string|array $table Table name or table/alias array (eg: array("table_name", "alias"))
	 * @param string $clausole
	 * @param array $fields
	 * @param string $schema
	 * @return P4A_DB_Source
	 */
	public function addJoinLeft($table, $clausole, $fields = '*', $schema = null)
	{
		$this->_join[] = array('LEFT', $table, $clausole, $fields, $schema);
		return $this;
	}
	
	/**
	 * Add a right join
	 * @param string|array $table Table name or table/alias array (eg: array("table_name", "alias"))
	 * @param string $clausole
	 * @param array $fields
	 * @param string $schema
	 * @return P4A_DB_Source
	 */
	public function addJoinRight($table, $clausole, $fields = '*', $schema = null)
	{
		$this->_join[] = array('RIGHT', $table, $clausole, $fields, $schema);
		return $this;
	}
	
	/**
	 * Add a full join
	 * @param string|array $table Table name or table/alias array (eg: array("table_name", "alias"))
	 * @param string $clausole
	 * @param array $fields
	 * @param string $schema
	 * @return P4A_DB_Source
	 */
	public function addJoinFull($table, $clausole, $fields = '*', $schema = null)
	{
		$this->_join[] = array('FULL', $table, $clausole, $fields, $schema);
		return $this;
	}
	
	/**
	 * Add a cross join
	 * @param string|array $table Table name or table/alias array (eg: array("table_name", "alias"))
	 * @param array $fields
	 * @param string $schema
	 * @return P4A_DB_Source
	 */
	public function addJoinCross($table, $fields = '*', $schema = null)
	{
		$this->_join[] = array('CROSS', $table, null, $fields, $schema);
		return $this;
	}
	
	/**
	 * Add a natural join
	 * @param string|array $table Table name or table/alias array (eg: array("table_name", "alias"))
	 * @param array $fields
	 * @param string $schema
	 * @return P4A_DB_Source
	 */
	public function addJoinNatural($table, $fields = '*', $schema = null)
	{
		$this->_join[] = array('NATURAL', $table, null, $fields, $schema);
		return $this;
	}

	/**
	 * Get all joins
	 * @return array
	 */
	public function getJoin()
	{
		return $this->_join;
	}

	/**
	 * set the where clausole (in SQL syntax)
	 * @param string $where
	 * @return P4A_DB_Source
	 */
	public function setWhere($where)
	{
		$this->resetNumRows();
		$this->_where = $where;				
		// if pointer is null load() was not called yet
		// if isNew() we stay in newRow 
		if ($this->_pointer !== null and !$this->isNew()) $this->firstRow();
		return $this;
	}

	/**
	 * Get the where clausole (in SQL syntax)
	 * @return string
	 */
	public function getWhere()
	{
		return $this->_where;
	}

	/**
	 * @param string $group
	 * @return P4A_DB_Source
	 */
	public function addGroup($group)
	{
		$this->_group[] = $group;
		return $this;
	}

	/**
	 * @param string|array $group
	 * @return P4A_DB_Source
	 */
	public function setGroup($group)
	{
		$this->_group = array();
		if (!is_array($group)) {
			$group = array($group);
		}

		foreach($group as $g) {
			$this->addGroup($g);
		}
		
		return $this;
	}

	/**
	 * @return array
	 */
	public function getGroup()
	{
		return $this->_group;
	}

	/**
	 * @param string $query
	 * @return P4A_DB_Source
	 */
	public function setQuery($query)
	{
		$this->_query = trim(preg_replace("/;$/", "", trim($query)));
		$this->isReadOnly(true);
		$this->isSortable(false);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getQuery()
	{
		return $this->_query;
	}

	/**
	 * @param string $filter
	 * @param P4A_Object $obj
	 * @return P4A_DB_Source
	 */
	public function addFilter($filter, $obj)
	{
		$this->_filters[$filter] =& $obj;
		$this->resetNumRows();
		return $this;
	}

	/**
	 * @param string $filter
	 * @return P4A_DB_Source
	 */
	public function dropFilter($filter)
	{
		if (array_key_exists($filter,$this->_filters)) {
			$this->resetNumRows();
			unset($this->_filters[$filter]);
		}
		return $this;
	}

	/**
	 * @return array
	 */
	public function getFilters()
	{
		$filters = array();
		foreach ($this->_filters as $string=>$obj) {
			if (is_object($obj)) {
				$class = strtolower(get_class($obj));
				if (isset($obj->data_field) and is_object($obj->data_field) and method_exists($obj->data_field, 'getNewValue')) {
					$value = $obj->data_field->getNewValue();
				} elseif (method_exists($obj, 'getNewValue')) {
					$value = $obj->getNewValue();
				} else {
					trigger_error('Filters can be applied only to P4A_Field or P4A_Data_Field', E_USER_ERROR);
				}

				if ((is_string($value) or is_numeric($value)) and strlen($value) > 0) {
					$filters[] = str_replace('?', $value, $string);
				} elseif (is_array($value) and !empty($value)) {
					$filters[] = str_replace('?', "'".implode("', '", $value)."'", $string);
				}
			} else {
				unset($this->_filters[$string]);
			}
		}
		return $filters;
	}

	/**
	 * @return P4A_DB_Source
	 * @see p4a/objects/P4A_Data_Source#load()
	 */
	public function load()
	{
		if (!$this->getQuery() and !$this->getTable()){
			trigger_error('Please define a query of a table', E_USER_ERROR);
		}
		
		$db = P4A_DB::singleton($this->getDSN());
		
		if ($this->getQuery()) {
			$row = $db->fetchRow($this->getQuery());
			foreach ($row as $column_name=>$column_value) {
				$this->createDataField($column_name);
			}
			return $this;
		}
		
		$select = $this->_composeSelectStructureQuery();
		$main_table = $this->getTable();
		
		// retrieving tables metadata 
		foreach ($select->getPart('from') as $alias=>$table_data) {
			$p4a_db_table = new P4A_Db_Table(array('name'=>$table_data['tableName'], 'schema'=>$table_data['schema'], 'db'=>$db->adapter));
			$this->_tables_metadata[$table_data['tableName']] = $p4a_db_table->info();
		}
		
		if (!$this->getSchema()) {
			foreach ($this->_tables_metadata[$main_table]['metadata'] as $column_data) {
				if (strlen($column_data['SCHEMA_NAME'])) {
					$this->setSchema($column_data['SCHEMA_NAME']);
				}
				break;
			}
		}
		
		// creating data fields
		foreach ($select->getPart('columns') as $column_data) {
			$table_name = $column_data[0] == $this->_table_alias ? $this->_table : $column_data[0];
			$column_name = $column_data[1];
			$column_alias = $column_data[2];
			
			if ($column_name == '*') {
				foreach ($this->_tables_metadata[$table_name]['metadata'] as $field_name=>$meta) {
					if (!$meta['SCHEMA_NAME']) $meta['SCHEMA_NAME'] = $this->_tables_metadata[$table_name]['schema'];
					$this->createDataField($field_name, $meta);
				}
			} elseif (!empty($column_alias)) {
				$field_name = strlen($column_alias) ? $column_alias : $column_name;
				if (isset($this->_tables_metadata[$table_name]) and in_array($column_name, array_keys($this->_tables_metadata[$table_name]['metadata']))) {
					$meta = $this->_tables_metadata[$table_name]['metadata'][$column_name];
					if (!$meta['SCHEMA_NAME']) $meta['SCHEMA_NAME'] = $this->_tables_metadata[$table_name]['schema'];
					$this->createDataField($field_name, $meta);
				} else {
					$this->createDataField($field_name);
					$this->fields->$field_name->setAliasOf($column_name);
				}
			}
		}
		
		// setting primary keys
		$primary_keys = array_values($this->_tables_metadata[$main_table]['primary']);
		if (P4A_AUTO_DB_PRIMARY_KEYS and !empty($primary_keys)) {
			if (sizeof($primary_keys) == 1) {
				$this->setPk($primary_keys[0]);
			} else {
				$this->setPk($primary_keys);
			}
		}
		
		$main_table_sequence = $this->_tables_metadata[$main_table]['sequence'];
		if (P4A_AUTO_DB_SEQUENCES and sizeof($primary_keys) == 1 and $main_table_sequence) {
			$field_name = $primary_keys[0];
			$table_name = $this->_tables_metadata[$main_table]['metadata'][$field_name]['TABLE_NAME'];
			if (is_string($main_table_sequence)) {
				$this->fields->$field_name->setSequence($main_table_sequence);
			} elseif (isset($this->fields->$field_name)) {
				$this->fields->$field_name->setSequence("{$table_name}_{$field_name}");
			}
		}
		return $this;
	}
	
	protected function createDataField($name, $meta = null)
	{
		$this->fields->build("P4A_Data_Field", $name);
		$this->fields->$name->setDSN($this->getDSN());
		
		if ($meta === null) {
			$this->fields->$name->isReadOnly(true);
		} else {
			$this->fields->$name->setLength($meta['LENGTH']);
			if ($meta['SCHEMA_NAME']) $this->fields->$name->setSchema($meta['SCHEMA_NAME']);
			$this->fields->$name->setTable($meta['TABLE_NAME']);
			if ($name != $meta['COLUMN_NAME']) {
				$this->fields->$name->setAliasOf($meta['COLUMN_NAME']);
			}
			
			if ($this->fields->$name->getTable() != $this->getTable()) {
				$this->fields->$name->isReadOnly(true);
			}
		}
		
		switch (strtolower($meta['DATA_TYPE'])) {
			case 'int':
			case 'int4':
			case 'integer':
				$this->fields->$name->setType('integer');
				break;
			case 'bit':
			case 'bool':
			case 'boolean':
			case 'tinyint':
				$this->fields->$name->setType('boolean');
				break;
			case 'date':
				$this->fields->$name->setType('date');
				break;
			case 'time':
				$this->fields->$name->setType('time');
				break;
			case 'number':
				if (strlen($meta['SCALE']) == 0) $meta['SCALE'] = 0;
				if ($meta['SCALE'] == 0) {
					$this->fields->$name->setType('integer');
				} else {
					$this->fields->$name->setType('decimal');
					if (is_numeric($meta['SCALE'])) {
						$this->fields->$name->setNumOfDecimals((int)$meta['SCALE']);
					}
				}
			case 'decimal':
			case 'numeric':
				$this->fields->$name->setType('decimal');
				if (is_numeric($meta['SCALE'])) {
					$this->fields->$name->setNumOfDecimals((int)$meta['SCALE']);
				}
				break;
			case 'float':
				$this->fields->$name->setType('float');
				if (is_numeric($meta['SCALE'])) {
					$this->fields->$name->setNumOfDecimals((int)$meta['SCALE']);
				}
				break;
			default:
				$this->fields->$name->setType('text');
		}
	}

	/**
	 * gets/sets read only state
	 * @param boolean $value
	 * @return boolean|P4A_DB_Source
	 */
	public function isReadOnly($value = null)
	{
		if ($value === null) return ($this->_is_read_only or !$this->getPk());
		$this->_is_read_only = $value;
		return $this;
	}

	public function getPkRow($pk)
	{
		$db = P4A_DB::singleton($this->getDSN());
		$select = $this->_composeSelectPkQuery($pk);
		$row = $db->adapter->fetchRow($select);
		if ($db->getDBType() == 'oci' and is_array($row)) {
			foreach ($row as $k=>$field) {
				if (is_resource($field)) {
					$row[$k] = stream_get_contents($field);
				}
			}
		}
		return $row;
	}

	/**
	 * @param integer $num_row The position of the row to retrieve in the data source
	 * @param boolean $move_pointer
	 * @return array
	 */
	public function row($num_row = null, $move_pointer = true)
	{
		$db = P4A_DB::singleton($this->getDSN());
		$schema = $this->getSchema();

		if ($num_row === null) {
			$num_row = $this->_pointer;
		}

		if ($num_row == 0) {
			$num_row = 1;
		}
		
		if ($this->getQuery()) {
			$select = $db->adapter->limit($this->getQuery(), 1, $num_row-1);
		} else {
			$select = $this->_composeSelectQuery();
			$select->limit(1, $num_row-1);
		}
		$row = $db->adapter->fetchRow($select);
		if (isset($row['ZEND_DB_ROWNUM'])) unset($row['ZEND_DB_ROWNUM']);
		if (isset($row['zend_db_rownum'])) unset($row['zend_db_rownum']);
		
		if ($db->getDBType() == 'oci' and is_array($row)) {
			foreach ($row as $k=>$field) {
				if (is_resource($field)) {
					$row[$k] = stream_get_contents($field);
				}
			}
		}

		if ($move_pointer) {
			if ($this->actionHandler('beforemoverow') == ABORT) return ABORT;

			if ($this->isActionTriggered('onmoverow')) {
				if ($this->actionHandler('onmoverow') == ABORT) return ABORT;
			} else {
				if (!empty($row)) {
					$this->_pointer = $num_row;
					foreach($row as $field=>$value) {
						$this->fields->$field->setValue($value);
					}
				} elseif ($this->getNumRows() == 0) {
					$this->newRow();
				}
			}

			$this->actionHandler('aftermoverow');
		}

		foreach ($this->_multivalue_fields as $fieldname=>$mv) {
			$fk = $mv["fk"];
			$fk_field = $mv["fk_field"];
			$table = ($schema ? "$schema." : "") . $mv["table"];

			$pk = $this->getPk();
			$pk_value = $this->fields->$pk->getNewValue();
			$fk_values = $db->adapter->fetchCol("SELECT $fk_field FROM $table WHERE $fk='$pk_value'");
			$this->fields->$fieldname->setValue($fk_values);
			$row[$fieldname] = $fk_values;
		}
		return $row;
	}

	/**
	 * @param mixed $pk
	 * @return array
	 */
	public function rowByPk($pk)
	{
		$row = $this->getPkRow($pk);
		$position = $this->getRowPosition($row);
		return $this->row($position);
	}

	/**
	 * @return integer
	 */
	public function getNumRows()
	{
		if ($this->_num_rows === null) {
			$db = P4A_DB::singleton($this->getDSN());
			$this->_num_rows = $db->adapter->fetchOne($this->_composeSelectCountQuery());
		}

		return $this->_num_rows;
	}

	/**
	 * @param array $row
	 * @return integer
	 */
	public function getRowPosition($row = null)
	{
		if (!$this->getQuery()) {
			$db = P4A_DB::singleton($this->getDSN());
			$select = $db->select();
			$this->_composeSelectPart($select);
			$this->_composeWherePart($select);

			$new_order_array_values = array();
			if ($order = $this->getOrder()) {
				$where_order = "";
				foreach($order as $field=>$direction) {
					$long_fld = $this->fields->$field->getSchemaTableField();
					$p_order = '';
					foreach ($new_order_array_values as $new_order_array_value) {
						list ($p_long_fld,$p_value) = $new_order_array_value;
						$p_order .= "$p_long_fld = $p_value AND ";
					}
					
					/*
					where order_field < "value" or (order_field="value" and pk1 <
					"valuepk1") or ( order_field="value" and pk1 = "valuepk1" and
					pk2<"valuepk2")
					*/

					if ($direction == P4A_ORDER_ASCENDING) {
						$operator = '<';
						$null_case = " OR $long_fld IS NULL ";
					} else {
						$operator = '>';
						$null_case = '';
					}
					if (is_array($row)) {
						$value = $row[$field];
					} else {
						$value = $this->fields->$field->getValue();
					}
					$value = $db->quote($value, true);
					$where_order .= " ($p_order ($long_fld $operator $value $null_case)) OR ";
					$new_order_array_values[] = array($long_fld,$value);
				}

				$select->having(substr($where_order, 0, -4));
				while ($f = $this->fields->nextItem()) {
					if (!isset($this->_multivalue_fields[$f->getName()])) {
						$select->group($f->getSchemaTableField());
					}
				}
			}

			$this->_composeGroupPart($select);
			$select = $this->_composeSelectCountQuery($select->__toString());
			
			/* Hack to solve mystic mysql bug: p4a bug 1666868 */
			/*http://sourceforge.net/tracker/index.php?func=detail&aid=1666868&group_id=98294&atid=620566*/
			if (!empty($this->_join) and $db->getDBType() == 'mysql') {
				$db->adapter->fetchOne($select);
			}
			return $db->adapter->fetchOne($select) + 1;
		}
	}
	
	/**
	 * @return P4A_DB_Source
	 */
	public function updateRowPosition()
	{
		$this->_pointer = $this->getRowPosition();
		return $this;
	}

	/**
	 * Inserts/update the row to the database.
	 * 
	 * If you've multivalue fields be sure that the user can't
	 * change the value of primary keys or the record will
	 * be broken.
	 * 
	 * You can pass $fields_values and $pk_values if you want
	 * to save a row you created in a custom way bypassing
	 * internal data_fields.
	 * 
	 * @param array $fields_values
	 * @param array $pk_values must be associative key_name=>key_value
	 */
	public function saveRow($fields_values = array(), $pk_values = array())
	{
		if(!$this->isReadOnly()) {
			$this->saveUploads();
			$db = P4A_DB::singleton($this->getDSN());
			$table = $this->getTable();
			$schema = $this->getSchema();
			
			if (empty($fields_values)) {
				while($field = $this->fields->nextItem()) {
					if ($field->getSchema() != $schema) continue;
					if ($field->getTable() != $table) continue;
					if ($field->getAliasOf()) {
						$name = $field->getAliasOf();
					} else {
						$name = $field->getName();
					}
					
					if (isset($this->_tables_metadata[$table]['metadata'][$name]) and
						!$field->isReadOnly() and
						!array_key_exists($name, $this->_multivalue_fields)) {
							$fields_values[$name] = $field->getNewValue();
							if ($fields_values[$name] === '') {
								$fields_values[$name] = null;
							}
					}
				}
			}

			$pks = $this->getPk();
			$p4a_db_table = new P4A_Db_Table(array('name'=>$table, 'schema'=>$schema, 'db'=>$db->adapter));
			if ($this->isNew()) {
				$lastinsert_pk_values = $p4a_db_table->insert($fields_values);
				if ($lastinsert_pk_values !== null) {
					if (is_array($lastinsert_pk_values)) {
						foreach ($lastinsert_pk_values as $field=>$value) {
							if (isset($this->fields->$field)) {
								$this->fields->$field->setValue($value);
							}
						}
					} elseif (is_string($pks)) {
						$this->fields->$pks->setValue((string)$lastinsert_pk_values);
					}
				}
			} else {
				$p4a_db_table->update($fields_values, $this->_composePkString($pk_values));
			}
			
			if (is_string($pks)) {
				$pk_value = $this->fields->$pks->getNewValue();  
				foreach ($this->_multivalue_fields as $fieldname=>$aField) {
					$fk_table = $aField["table"];
					$fk_table = ($schema ? "$schema." : "") . $fk_table;
					$fk_field = $aField["fk_field"];
					$fk = $aField["fk"];
					$old_fk_values = $db->adapter->fetchCol("SELECT $fk_field FROM $fk_table WHERE $fk=?", $pk_value);
					$fk_values = $this->fields->$fieldname->getNewValue();
	
					if (!is_array($old_fk_values)) $old_fk_values = array();
					if (!is_array($fk_values)) $fk_values = array();
	
					$toadd = array_diff($fk_values, $old_fk_values);
					$toremove = array_diff($old_fk_values, $fk_values);
	
					if (!empty($toremove)) {
						foreach ($toremove as $k=>$v) {
							$db->adapter->query("DELETE FROM $fk_table WHERE $fk=? AND $fk_field=?", array($pk_value, $v));
						}
					}
	
					if (!empty($toadd)) {
						foreach ($toadd as $k=>$v) {
							$db->adapter->query("INSERT INTO $fk_table($fk, $fk_field) VALUES(?, ?)", array($pk_value, $v));
						}
					}
				}
			}
			
			if (empty($pk_values)) {
				if (is_string($pks)) {
					$pk_values[] = $this->fields->$pks->getNewValue();
				} else {
					foreach($pks as $pk){
						$pk_values[] = $this->fields->$pk->getNewValue();
					}
				}
			}
			$row = $this->getPkRow($pk_values);
			
			$this->resetNumRows();
			if ($row) {
				foreach($row as $field=>$value){
					$this->fields->$field->setValue($value);
				}
				$this->updateRowPosition();
			} else {
				$this->firstRow();
			}
		}
	}

	/**
	 * Removes the row from the database
	 */
	public function deleteRow()
	{
		if (!$this->isReadOnly()) {
			$db = P4A_DB::singleton($this->getDSN());
			$table = $this->getTable();
			$schema = $this->getSchema();

			$pks = $this->getPK();
			foreach ($this->_multivalue_fields as $fieldname=>$aField) {
				$pk_value = $this->fields->$pks->getNewValue();

				$fk_table = ($schema ? "$schema." : "") . $aField["table"];
				$fk = $aField["fk"];

				$db->adapter->query("DELETE FROM $fk_table WHERE $fk=?", array($pk_value));
			}
			
			$table = new P4A_Db_Table(array('name'=>$this->getTable(), 'schema'=>$this->getSchema(), 'db'=>$db->adapter));
			$table->delete($this->_composePkString());
			$this->resetNumRows();
		}

		parent::deleteRow();
	}

	/**
	 * Returns all rows as an associative array.
	 * 
	 * You can get only a subset of all rows using
	 * $from and $count parameters.
	 *
	 * @param integer $from
	 * @param integer $count
	 * @return array
	 */
	public function getAll($from = 0, $count = 0)
	{
		$db = P4A_DB::singleton($this->getDSN());
		$select = $this->_composeSelectQuery();

		if ($this->getQuery() and $this->_limit !== null and $this->_offset !== null) {
			$count = $this->_limit;
			$from = $this->_offset;
		}

		if ($from == 0 and $count == 0) {
			$rows = $db->adapter->fetchAll($select);
			if (!is_array($rows)) {
				$rows = array();
			}
		} else {
			if (is_string($select)) {
				$select = $db->adapter->limit($select, $count, $from);
			} else {
				$select->limit($count, $from);
			}
			$rows = $db->adapter->fetchAll($select);
			if (!is_array($rows)) {
				$rows = array();
			}
		}

		return $rows;
	}

	/**
	 * @param string $query
	 * @return string
	 */
	protected function _composeSelectCountQuery($query = null)
	{
		if ($query !== null) {
			return "SELECT count(*) AS p4a_count FROM ($query) p4a_count";
		}
		
		if ($this->getQuery()) {
			return "SELECT count(*) AS p4a_count FROM (". $this->getQuery() . ") p4a_count";
		}
		
		$query = $this->_composeSelectQuery(false)->__toString();
		return "SELECT count(*) AS p4a_count FROM ($query) p4a_count";
	}
	
	/**
	 * @return Zend_Db_Select|string
	 */
	protected function _composeSelectStructureQuery()
	{
		if ($this->getQuery()) {
			return "SELECT * FROM (". $this->getQuery() . ") p4a_structure";
		}
		
		$select = P4A_DB::singleton($this->getDSN())->select();
		$this->_composeSelectPart($select);
		$this->_composeWherePart($select);
		$this->_composeGroupPart($select);
		return $select;
	}

	/**
	 * @return Zend_Db_Select|string
	 */
	protected function _composeSelectQuery($add_order_clause = true)
	{
		if ($this->getQuery()) {
			return "SELECT * FROM (". $this->getQuery() . ") p4a_select";
		}
		
		$select = P4A_DB::singleton($this->getDSN())->select();
		$this->_composeSelectPart($select);
		$this->_composeWherePart($select);
		$this->_composeGroupPart($select);
		if ($add_order_clause) $this->_composeOrderPart($select);
		return $select;
	}

	/**
	 * @return Zend_Db_Select|string
	 */
	protected function _composeSelectPkQuery($pk_value)
	{
		$db = P4A_Db::singleton($this->getDSN());
		$select = $db->select();
		$this->_composeSelectPart($select);
		$this->_composeWherePart($select);

		$pk_key = $this->getPK();
		$pk_string = "";

		if (is_array($pk_key)) {
			for($i=0;$i<count($pk_key);$i++){
				$pk_string .= "{$this->_table}.{$pk_key[$i]} = '{$pk_value[$i]}' AND ";
			}
			$pk_string = substr($pk_string,0,-4);
		} else {
			if (is_array($pk_value)) {
				list($key,$pk_value) = each($pk_value);
			}
			$pk_string = "{$this->_table}.{$pk_key} = '{$pk_value}' ";
		}
		
		$select->where($pk_string);
		$this->_composeGroupPart($select);
		$this->_composeOrderPart($select);
		return $select;
	}

	protected function _composeSelectPart($select)
	{
		$table = strlen($this->_table_alias) ? array($this->_table_alias=>$this->_table) : $this->_table;
		
		if (empty($this->_fields)) {
			$select->from($table, '*', $this->getSchema());
		} else {
			$new_fields = array();
			foreach ($this->_fields as $k=>$v) {
				if (is_numeric($k)) {
					$k = $v;
				}
				$new_fields[$v] = $k;
			}
			$select->from($table, $new_fields, $this->getSchema());
		}
		
		foreach ($this->_join as $join) {
			$method = "join{$join[0]}";
			$new_fields = $join[3];
			$table = is_array($join[1]) ? array($join[1][1]=>$join[1][0]) : $join[1];
			
			if (is_array($new_fields) and !empty($new_fields)) {
				$new_fields = array();
				foreach ($join[3] as $k=>$v) {
					if (is_numeric($k)) {
						$k = $v;
					}
					$new_fields[$v] = $k;
				}
			}
			
			$select->$method($table, $join[2], $new_fields, $join[4]);
		}
	}

	protected function _composeSelectCountPart($select)
	{
		$table = strlen($this->_table_alias) ? array($this->_table_alias=>$this->_table) : $this->_table;
		$select->from($table, 'count(*)', $this->getSchema());
		foreach ($this->_join as $join) {
			$method = "join{$join[0]}";
			$new_fields = $join[3];
			$table = is_array($join[1]) ? array($join[1][1]=>$join[1][0]) : $join[1];
			
			if (is_array($new_fields) and !empty($new_fields)) {
				$new_fields = array();
				foreach ($join[3] as $k=>$v) {
					if (is_numeric($k)) {
						$k = $v;
					}
					$new_fields[$v] = $k;
				}
			} else {
				var_dump($join[3]);
			}
			
			$select->$method($table, $join[2], $new_fields, $join[4]);
		}
	}

	protected function _composeWherePart($select)
	{
		$query = "";
		if ($where = $this->getWhere()){
			$query .= "($where) AND ";
		}
		foreach ($this->getFilters() as $filter) {
			$query .= "($filter) AND ";
		}
		if (strlen($query) > 0) {
			$query = substr($query,0,-4);
			$select->where($query);
		}
	}

	protected function _composeGroupPart($select)
	{
		$select->group($this->getGroup());
	}

	protected function _composeOrderPart($select, $order = array())
	{
		if (!$order) $order = $this->getOrder();
		if ($order) {
			$order_array = array();
			foreach ($order as $field=>$direction) {
				$order_array[] = "$field $direction";
			}
			$select->order($order_array);
		}
	}

	protected function _composePkString($pk_values = array())
	{
		if (empty($pk_values)) {
			$pk_values = $this->getPkValues();
		}

		if (is_numeric($pk_values) or is_string($pk_values)) {
			return "{$this->getPk()} = " . P4A_DB::singleton($this->getDSN())->quote($pk_values, true);
		} elseif (is_array($pk_values)) {
			$return = '';
			foreach($pk_values as $key=>$value) {
				$return .= "$key = " . P4A_DB::singleton($this->getDSN())->quote($value, true) . " AND ";
			}
			return substr($return, 0, -4);
		} else {
			trigger_error("NO PK", E_USER_ERROR);
		}
	}

	/**
	 * @return P4A_DB_Source
	 */
	public function resetNumRows()
	{
		$this->_num_rows = null;
		return $this;
	}

	/**
	 * @param string $fieldname
	 * @param string $table
	 * @param string $fk
	 * @param string $fk_field
	 * @return P4A_DB_Source
	 */
	public function addMultivalueField($fieldname, $table = null, $fk = null, $fk_field = null)
	{
		$db = P4A_DB::singleton($this->getDSN());
		if ($table === null) $table = $fieldname;
		$this->_multivalue_fields[$fieldname]['table'] = $table;

		if (!$fk) {
			$pk = $this->getPk();
			if (!$pk) {
				trigger_error("Set PK before calling \"addMultivalueField\"", E_USER_ERROR);
			} elseif (is_array($pk)) {
				trigger_error("Multivalue not usable with multiple pk", E_USER_ERROR);
			} else {
				$fk = $pk;
			}
		}
		$this->_multivalue_fields[$fieldname]['fk'] = $fk;

		if (!$fk_field) {
			$p4a_db_table = new P4A_Db_Table(array('name'=>$table, 'db'=>$db->adapter));
			$info = $p4a_db_table->info();
			foreach($info['metadata'] as $field_name=>$field_info) {
				if ($field_name != $fk ) {
					$fk_field = $field_name;
					break;
				}
			}
		}
		$this->_multivalue_fields[$fieldname]['fk_field'] = $fk_field;

		$this->fields->build("P4A_Data_Field", $fieldname);
		$this->fields->$fieldname->setDSN($this->getDSN());
		
		return $this;
	}

	public function __wakeup()
	{
		$this->resetNumRows();
	}
}