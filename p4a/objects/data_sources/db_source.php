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
class P4A_DB_Source extends P4A_Data_Source
{
	protected $_DSN = null;
	protected $_pk = null;
	protected $_table = null;
	protected $_schema = null;
	protected $_fields = array();
	protected $_join = array();
	protected $_where = null;
	protected $_group = array();
	protected $_is_sortable = true;
	protected $_query = null;
	protected $_multivalue_fields = array();
	protected $_filters = array();
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
	 * @param string $table
	 * @return P4A_DB_Source
	 */
	public function setTable($table)
	{
		$this->_table = $table;
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

	public function getFields()
	{
		return $this->_fields;
	}

	/**
	 * @param string $table
	 * @param string $clausole
	 * @param array $fields
	 * @return P4A_DB_Source
	 */
	public function addJoin($table, $clausole, array $fields = null)
	{
		$this->_join[] = array('INNER', $table, $clausole, $fields);
		return $this;
	}
	
	/**
	 * @param string $table
	 * @param string $clausole
	 * @param array $fields
	 * @return P4A_DB_Source
	 */
	public function addJoinInner($table, $clausole, array $fields = null)
	{
		$this->addJoin($table, $clausole, $fields);
		return $this;
	}
	
	/**
	 * @param string $table
	 * @param string $clausole
	 * @param array $fields
	 * @return P4A_DB_Source
	 */
	public function addJoinLeft($table, $clausole, array $fields = null)
	{
		$this->_join[] = array('LEFT', $table, $clausole, $fields);
		return $this;
	}
	
	/**
	 * @param string $table
	 * @param string $clausole
	 * @param array $fields
	 * @return P4A_DB_Source
	 */
	public function addJoinRight($table, $clausole, array $fields = null)
	{
		$this->_join[] = array('RIGHT', $table, $clausole, $fields);
		return $this;
	}
	
	/**
	 * @param string $table
	 * @param string $clausole
	 * @param array $fields
	 * @return P4A_DB_Source
	 */
	public function addJoinFull($table, $clausole, array $fields = null)
	{
		$this->_join[] = array('FULL', $table, $clausole, $fields);
		return $this;
	}
	
	/**
	 * @param string $table
	 * @param string $clausole
	 * @param array $fields
	 * @return P4A_DB_Source
	 */
	public function addJoinCross($table, array $fields = null)
	{
		$this->_join[] = array('CROSS', $table, null, $fields);
		return $this;
	}
	
	/**
	 * @param string $table
	 * @param string $clausole
	 * @param array $fields
	 * @return P4A_DB_Source
	 */
	public function addJoinNatural($table, array $fields = null)
	{
		$this->_join[] = array('NATURAL', $table, null, $fields);
		return $this;
	}

	/**
	 * @return array
	 */
	public function getJoin()
	{
		return $this->_join;
	}

	/**
	 * @param string $where
	 * @return P4A_DB_Source
	 */
	public function setWhere($where)
	{
		$this->resetNumRows();
		$this->_where = $where;
		return $this;
	}

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
		$this->_query = $query;
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
					P4A_Error('FILTERS CAN ONLY BE APPLIED TO P4A_Field OR P4A_Data_Field');
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

	public function load()
	{
		if (!$this->getQuery() and !$this->getTable()){
			p4a_error("PLEASE DEFINE A QUERY OR A TABLE");
		}
		
		$db = P4A_DB::singleton($this->getDSN());
		
		if ($this->getQuery()) {
			$row = $db->getRow($this->getQuery());
			foreach ($row as $column_name=>$column_value) {
				$this->createDataField($column_name);
			}
			return;
		}
		
		$select = $this->_composeSelectStructureQuery();
		$main_table = $this->getTable();
		
		// retrieving tables metadata 
		foreach ($select->getPart('from') as $table=>$table_data) {
			$p4a_db_table = new P4A_Db_Table(array('name'=>$table, 'schema'=>$table_data['schema'], 'db'=>$db->adapter));
			$this->_tables_metadata[$table] = $p4a_db_table->info();
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
			$table_name = $column_data[0];
			$column_name = $column_data[1];
			$column_alias = $column_data[2];
			
			if ($column_name == '*') {
				foreach ($this->_tables_metadata[$table_name]['metadata'] as $field_name=>$meta) {
					$this->createDataField($field_name, $meta);
				}
			} else {
				$field_name = strlen($column_alias) ? $column_alias : $column_name;
				if (in_array($column_name, array_keys($this->_tables_metadata[$table_name]['metadata']))) {
					$this->createDataField($field_name, $this->_tables_metadata[$table_name]['metadata'][$column_name]);
				} else {
					$this->createDataField($field_name);
				}
			}
		}
		
		// setting primary keys
		$primary_keys = array_values($this->_tables_metadata[$main_table]['primary']);
		if (P4A_AUTO_DB_PRIMARY_KEYS) {
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
			} else {
				$this->fields->$field_name->setSequence("{$table_name}_{$field_name}");
			}
		}
	}
	
	protected function createDataField($name, $meta = null)
	{
		$this->fields->build("P4A_Data_Field", $name);
		$this->fields->$name->setDSN($this->getDSN());
		
		if ($meta === null) {
			$this->fields->$name->isReadOnly(false);
		} else {
			$this->fields->$name->setLength($meta['LENGTH']);
			if ($meta['SCHEMA_NAME']) $this->fields->$name->setSchema($meta['SCHEMA_NAME']);
			$this->fields->$name->setTable($meta['TABLE_NAME']);
			if ($name != $meta['COLUMN_NAME']) {
				$this->fields->$name->setAliasOf($meta['COLUMN_NAME']);
			}
		}
		
		switch (strtolower($meta['DATA_TYPE'])) {
			case 'int':
			case 'int4':
			case 'integer':
				$this->fields->$name->setType('integer');
				break;
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
	 * @param booleab $value
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
		return $db->adapter->fetchRow($select);
	}

	public function row($num_row = null, $move_pointer = true)
	{
		$db = P4A_DB::singleton($this->getDSN());

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

		foreach ($this->_multivalue_fields as $fieldname=>$mv) {
			$fk = $mv["fk"];
			$fk_field = $mv["fk_field"];
			$table = $mv["table"];

			$pk = $this->getPk();
			$pk_value = $this->fields->$pk->getNewValue();
			$fk_values = $db->adapter->fetchCol("SELECT $fk_field FROM $table WHERE $fk='$pk_value'");
			$this->fields->$fieldname->setValue($fk_values);
			$row[$fieldname] = $fk_values;
		}
		return $row;
	}

	public function rowByPk($pk)
	{
		$row = $this->getPkRow($pk);
		$position = $this->getRowPosition($row);
		$this->row($position);
	}

	public function getNumRows()
	{
		if ($this->_num_rows === null) {
			$db = P4A_DB::singleton($this->getDSN());
			$this->_num_rows = $db->adapter->fetchOne($this->_composeSelectCountQuery());
		}

		return $this->_num_rows;
	}

	public function getRowPosition($row = null)
	{
		if (!$this->getQuery()) {
			$db = P4A_DB::singleton($this->getDSN());
			$select = $db->select();
			$this->_composeSelectPart($select);
			$this->_composeWherePart($select);

			$new_order_array = array();
			$new_order_array_values = array();
			if ($order = $this->getOrder()) {
				$where_order = "";
				foreach($order as $field=>$direction) {
					$long_fld = $this->fields->$field->getSchemaTableField();
					$p_order = '';
					foreach ($new_order_array_values as $p_long_fld=>$p_value) {
						$p_order .= "$p_long_fld = '$p_value' AND ";
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
						$value = P4A_Quote_SQL_Value($row[$field]);
					} else {
						$value = $this->fields->$field->getSQLValue();
					}
					$where_order .= " ($p_order ($long_fld $operator '$value' $null_case)) OR ";
					$new_order_array[$long_fld] = $direction;
					$new_order_array_values[$long_fld] = $value;
				}

				$select->where(substr($where_order, 0, -4));
			}

			$this->_composeGroupPart($select);
			$select = $this->_composeSelectCountQuery((string)$select);
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

	public function saveRow($fields_values = array(), $pk_values = array())
	{
		if(!$this->isReadOnly()) {
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

			$p4a_db_table = new P4A_Db_Table(array('name'=>$table, 'schema'=>$schema, 'db'=>$db->adapter));
			if ($this->isNew()) {
				$p4a_db_table->insert($fields_values);
			} else {
				$p4a_db_table->update($fields_values, $this->_composePkString($pk_values));
			}

			$pks = $this->getPk();
			
			if (is_string($pks)) {
				$pk_value = $this->fields->$pks->getNewValue();  
				foreach ($this->_multivalue_fields as $fieldname=>$aField) {
					$fk_table = $aField["table"];
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

	public function deleteRow()
	{
		if (!$this->isReadOnly()) {
			$db = P4A_DB::singleton($this->getDSN());
			$table = $this->getTable();

			$pks = $this->getPK();
			foreach ($this->_multivalue_fields as $fieldname=>$aField) {
				$pk_value = $this->fields->$pks->getNewValue();

				$fk_table = $aField["table"];
				$fk = $aField["fk"];

				$res = $db->adapter->execute("DELETE FROM $fk_table WHERE $fk=?", array($pk_value));
				if ($db->adapter->metaError()) {
					P4A_Error($db->adapter->metaErrorMsg($db->adapter->metaError()));
				}
			}

			$res = $db->adapter->query("DELETE FROM $table WHERE " . $this->_composePkString());
			if ($db->adapter->metaError()) {
				$e = new P4A_Error('A query has returned an error', $this, $db->getNativeError());
				if ($this->errorHandler('onQueryError', $e) !== PROCEED) {
					die();
				}
			}

			$this->resetNumRows();
		}

		parent::deleteRow();
	}

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

	protected function _composeSelectCountQuery($query = null)
	{
		if ($query !== null) {
			return "SELECT count(*) AS p4a_count FROM ($query) p4a_count";
		}
		
		if ($this->getQuery()) {
			return "SELECT count(*) AS p4a_count FROM (". $this->getQuery() . ") p4a_count";
		}
		
		$select = P4A_DB::singleton($this->getDSN())->select();
		$this->_composeSelectCountPart($select);
		$this->_composeWherePart($select);
		$this->_composeGroupPart($select);
		return $select;
	}
	
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

	protected function _composeSelectQuery()
	{
		if ($this->getQuery()) {
			return "SELECT * FROM (". $this->getQuery() . ") p4a_select";
		}
		
		$select = P4A_DB::singleton($this->getDSN())->select();
		$this->_composeSelectPart($select);
		$this->_composeWherePart($select);
		$this->_composeGroupPart($select);
		$this->_composeOrderPart($select);
		return $select;
	}

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
				$pk_value = $pk_value[0];
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
		if (empty($this->_fields)) {
			$select->from($this->getTable(), '*', $this->getSchema());
		} else {
			$fields = $this->_fields;
			$new_fields = array();
			foreach ($fields as $k=>$v) {
				if (is_numeric($k)) {
					$k = $v;
				}
				$new_fields[$v] = $k;
			}
			$select->from($this->getTable(), $new_fields, $this->getSchema());
		}
		
		foreach ($this->_join as $join) {
			$method = "join{$join[0]}";
			if (empty($join[3])) {
				$select->$method($join[1], $join[2]);
			} else {
				$select->$method($join[1], $join[2], array_flip($join[3]));
			}
		}
	}

	protected function _composeSelectCountPart($select)
	{
		$select->from($this->getTable(), 'count(*)', $this->getSchema());
	}

	protected function _composeWherePart($select)
	{
		$query = "";
		if ($where = $this->getWhere()){
			$query .= "($where) AND ";
		}
		$filters = $this->getFilters();
		foreach ($filters as $filter) {
			$query .= "($filter) AND ";
		}
		if (strlen($query) > 0) {
			$query = substr($query,0,-4);
			$select->where($query);
		}
	}

	protected function _composeGroupPart($select)
	{
		if ($this->getGroup()) {
			$select->group(join(',', $this->getGroup()));
		}
	}

	protected function _composeOrderPart($select, $order = array())
	{
		if (!$order) $order = $this->getOrder();
		if ($order) {
			$order_array = array();
			foreach ($order as $field=>$direction) {
				$real_field_name = $this->fields->$field->getSchemaTableField();
				$order_array[] = "$real_field_name $direction";
			}
			$select->order($order_array);
		}
	}

	protected function _composePkString($pk_values = array())
	{
		$pks = $this->getPk();
		if (!$pk_values) {
			$pk_values = $this->getPkValues();
		}

		if (is_string($pks)) {
			return "$pks = '" . P4A_Quote_SQL_Value($pk_values) . "' ";
		} elseif (is_array($pks)) {
			$return = '';
			foreach($pk_values as $key=>$value) {
				$return .= "$key = '" . P4A_Quote_SQL_Value($value) . "' AND ";
			}
			return substr($return, 0, -4);
		} else {
			p4a_error("NO PK");
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
				P4A_Error("Set PK before calling \"addMultivalueField\"");
			} elseif (is_array($pk)) {
				P4A_Error("Multivalue not usable with multiple pk");
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