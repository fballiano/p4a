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
 * To contact the authors write to:                                 <br>
 * CreaLabs                                                         <br>
 * Via Medail, 32                                                   <br>
 * 10144 Torino (Italy)                                             <br>
 * Web:    {@link http://www.crealabs.it}                           <br>
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
class P4A_DB_Source extends P4A_Data_Source
{
	protected $_DSN = "";
	protected $_pk = null;
	protected $_table = "";
	protected $_fields = array();
	protected $_join = array();
	protected $_where = "";
	protected $_group = array();
	protected $_is_sortable = true;
	protected $_query = "";
	protected $_multivalue_fields = array();
	protected $_filters = array();
	protected $_tables_metadata = array();

	public function setDSN($DSN)
	{
		$this->_DSN = $DSN;
	}

	public function getDSN()
	{
		return $this->_DSN;
	}

	public function setTable($table)
	{
		$this->_table = $table;
	}

	public function getTable()
	{
		return $this->_table;
	}

	public function setFields($fields)
	{
		$this->_fields = $fields;
	}

	public function getFields()
	{
		return $this->_fields;
	}

	public function addJoin($table, $clausole, $fields)
	{
		$this->_join[] = array('INNER', $table, $clausole, $fields);
	}
	
	public function addJoinInner($table, $clausole, $fields)
	{
		$this->addJoin($table, $clausole, $fields);
	}
	
	public function addJoinLeft($table, $clausole, $fields)
	{
		$this->_join[] = array('LEFT', $table, $clausole, $fields);
	}
	
	public function addJoinRight($table, $clausole, $fields)
	{
		$this->_join[] = array('RIGHT', $table, $clausole, $fields);
	}
	
	public function addJoinFull($table, $clausole, $fields)
	{
		$this->_join[] = array('FULL', $table, $clausole, $fields);
	}
	
	public function addJoinCross($table, $fields)
	{
		$this->_join[] = array('CROSS', $table, null, $fields);
	}
	
	public function addJoinNatural($table, $fields)
	{
		$this->_join[] = array('NATURAL', $table, null, $fields);
	}

	public function getJoin()
	{
		return $this->_join;
	}

	public function setWhere($where)
	{
		$this->resetNumRows();
		$this->_where = $where;
	}

	public function getWhere()
	{
		return $this->_where;
	}

	public function addGroup($group)
	{
		$this->_group[] = $group;
	}

	public function setGroup($group)
	{
		$this->_group = array();
		if (!is_array($group)) {
			$group = array($group);
		}

		foreach($group as $g) {
			$this->addGroup($g);
		}
	}

	public function getGroup()
	{
		return $this->_group;
	}

	public function setQuery($query)
	{
		$this->_query = $query;
		$this->isReadOnly(true);
		$this->isSortable(false);
	}

	public function getQuery()
	{
		return $this->_query;
	}

	public function addFilter($filter, &$obj)
	{
		$this->_filters[$filter] =& $obj;
		$this->resetNumRows();
	}

	public function dropFilter($filter)
	{
		if (array_key_exists($filter,$this->_filters)) {
			$this->resetNumRows();
			unset($this->_filters[$filter]);
		}
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
		
		$db =& P4A_DB::singleton($this->getDSN());
		
		if ($this->getQuery()) {
			$row = $db->getRow($this->getQuery());
			foreach ($row as $column_name=>$column_value) {
				$this->createDataField($column_name);
			}
			return;
		}
		
		$select =& $this->_composeSelectStructureQuery();
		$main_table = $this->getTable();
		
		// retrieving tables metadata 
		foreach ($select->getPart('from') as $table=>$table_data) {
			$p4a_db_table = new P4A_Db_Table(array('name'=>$table, 'db'=>$db->adapter));
			$this->_tables_metadata[$table] = $p4a_db_table->info();
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
			$this->fields->$name->setReadOnly();
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
				$this->fields->$name->setType('integer');
				break;
			case 'bool':
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
	 * Returns the DB field name (converting alias) in 2 formats: "schema.table.field" and "field"
	 * @param string $field
	 * @return array
	 */
	function getFieldName($field)
	{
		$field = explode('.', $field);
		$field = $field[sizeof($field) - 1];
		
		$alias_of = $this->fields->$field->getAliasOf();
		if (!strlen($alias_of)) {
			$alias_of = $field;
		}
		
		$schema = $this->fields->$field->getSchema();
		if (strlen($schema)) $schema = "{$schema}.";

		$table = $this->fields->$field->getTable();
		if (strlen($table)) $table = "{$table}.";

		return array($schema . $table . $alias_of, $alias_of);
	}

	public function isReadOnly($value=null)
	{
		if ($value !== null) {
			$this->_is_read_only = $value;
		}

		if ($this->_is_read_only or !$this->getPk()){
			return true;
		} else {
			return false;
		}
	}

	public function getPkRow($pk)
	{
		$db =& P4A_DB::singleton($this->getDSN());
		$select =& $this->_composeSelectPkQuery($pk);
		return $db->adapter->fetchRow($select);
	}

	public function row($num_row = null, $move_pointer = true)
	{
		$db =& P4A_DB::singleton($this->getDSN());

		if ($num_row === null) {
			$num_row = $this->_pointer;
		}

		if ($num_row == 0) {
			$num_row = 1;
		}
		
		if ($this->getQuery()) {
			$select = $db->adapter->limit($this->getQuery(), 1, $num_row-1);
		} else {
			$select =& $this->_composeSelectQuery();
			$select->limit(1, $num_row-1);
		}
		$row = $db->adapter->fetchRow($select);

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
			$db =& P4A_DB::singleton($this->getDSN());
			$this->_num_rows = $db->adapter->fetchOne($this->_composeSelectCountQuery());
		}

		return $this->_num_rows;
	}

	public function getRowPosition($row = null)
	{
		if (!$this->getQuery()) {
			$db =& P4A_DB::singleton($this->getDSN());
			$select =& $db->select();
			$this->_composeSelectCountPart($select);
			$this->_composeWherePart($select);

			$new_order_array = array();
			$new_order_array_values = array();
			if ($order = $this->getOrder()) {
				$where_order = "";
				foreach($order as $field=>$direction) {
					list($long_fld, $short_fld) = $this->getFieldName($field);

					$p_order = "";
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
						if (isset($row[$short_fld])) {
							$value = addslashes($row[$short_fld]);
						} else {
							p4a_error("error in P4A_DB_Source::getRowPosition(): maybe you passed an incomplete row");
						}
					} else {
						$value = addslashes($this->fields->$short_fld->getValue());
					}
					$where_order .= " ($p_order ($long_fld $operator '$value' $null_case)) OR ";

					$new_order_array[$long_fld] = $direction;
					$new_order_array_values[$long_fld] = $value;
				}

				$where_order = substr($where_order, 0, -3);
				$select->where($where_order);
			}

			$this->_composeGroupPart($select);

			/* Hack to solve mystic mysql bug: p4a bug 1666868 */
			/*http://sourceforge.net/tracker/index.php?func=detail&aid=1666868&group_id=98294&atid=620566*/
			if (count($this->_join)) {
				$db->adapter->fetchOne($select);
			}
			return $db->adapter->fetchOne($select) + 1;
		}
	}

	public function updateRowPosition()
	{
		$this->_pointer = $this->getRowPosition();
	}

	public function saveRow($fields_values = array(), $pk_values = array())
	{
		if(!$this->isReadOnly()) {
			$db =& P4A_DB::singleton($this->getDSN());
			$table = $this->getTable();
			
			if (empty($fields_values)) {
				while($field =& $this->fields->nextItem()) {
					if ($field->getAliasOf()) {
						$name = $field->getAliasOf();
					} else {
						$name = $field->getName();
					}
					
					if (isset($this->_tables_metadata[$table]['metadata'][$name]) and
						!$field->isReadOnly() and
						!array_key_exists($name, $this->_multivalue_fields)) {
							$fields_values[$name] = $field->getNewValue();
							if ($fields_values[$name] === "") {
								$fields_values[$name] = NULL;
							}
					}
				}
			}

			$p4a_db_table = new P4A_Db_Table(array('name'=>$table, 'db'=>$db->adapter));
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
			$db =& P4A_DB::singleton($this->getDSN());
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
		$db =& P4A_DB::singleton($this->getDSN());
		$select =& $this->_composeSelectQuery();

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

	protected function &_composeSelectCountQuery()
	{
		if ($this->getQuery()) {
			return "SELECT count(*) p4a_count FROM (". $this->getQuery() . ") p4a_count";
		}
		
		$select =& P4A_DB::singleton($this->getDSN())->select();
		$this->_composeSelectCountPart($select);
		$this->_composeWherePart($select);
		$this->_composeGroupPart($select);
		return $select;
	}
	
	protected function &_composeSelectStructureQuery()
	{
		if ($this->getQuery()) {
			return "SELECT * FROM (". $this->getQuery() . ") p4a_structure";
		}
		
		$select =& P4A_DB::singleton($this->getDSN())->select();
		$this->_composeSelectPart($select);
		$this->_composeWherePart($select);
		$this->_composeGroupPart($select);
		return $select;
	}

	protected function &_composeSelectQuery()
	{
		if ($this->getQuery()) {
			return "SELECT * FROM (". $this->getQuery() . ") p4a_select";
		}
		
		$select =& P4A_DB::singleton($this->getDSN())->select();
		$this->_composeSelectPart($select);
		$this->_composeWherePart($select);
		$this->_composeGroupPart($select);
		$this->_composeOrderPart($select);
		return $select;
	}

	protected function &_composeSelectPkQuery($pk_value)
	{
		$db =& P4A_Db::singleton($this->getDSN());
		$select =& $db->select();
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

	protected function &_composeSelectPart(&$select)
	{
		if (empty($this->_fields)) {
			$select->from($this->getTable());
		} else {
			$select->from($this->getTable(), array_flip($this->_fields));
		}
		
		foreach ($this->_join as $join) {
			$method = "join{$join[0]}";
			$select->$method($join[1], $join[2], array_flip($join[3]));
		}
	}

	protected function _composeSelectCountPart(&$select)
	{
		$select->from($this->getTable(), 'count(*)');
	}

	protected function _composeWherePart(&$select)
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

	protected function _composeGroupPart(&$select)
	{
		if ($this->getGroup()) {
			$select->group(join(',', $this->getGroup()));
		}
	}

	protected function _composeOrderPart(&$select, $order = array())
	{
		if (!$order) $order = $this->getOrder();
		if ($order) {
			$order_array = array();
			foreach ($order as $field=>$direction) {
				list($long_fld, $short_fld) = $this->getFieldName($field);
				$order_array[] = "$long_fld $direction";
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
			return "$pks = '".addslashes($pk_values)."' ";
		} elseif(is_array($pks)) {
			$return = '';
			foreach($pk_values as $key=>$value){
				$return .= "$key = '".addslashes($value)."' AND ";
			}
			return substr($return, 0, -4);
		} else {
			p4a_error("NO PK");
		}
	}

	public function resetNumRows()
	{
		$this->_num_rows = NULL;
	}

	public function addMultivalueField($fieldname, $table = null, $fk = null, $fk_field = null)
	{
		$db =& P4A_DB::singleton($this->getDSN());
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
	}

	public function __wakeup()
	{
		$this->resetNumRows();
	}
}