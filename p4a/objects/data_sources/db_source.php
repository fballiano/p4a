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

class P4A_DB_Source extends P4A_Data_Source
{
    var $_DSN = "";

    var $_pk = NULL;

    var $_select = "";
    var $_table = "";
    var $_join = array();
    var $_where = "";
    var $_group = array();
    var $_is_sortable = true;

    var $_query = "";

    var $_use_fields_aliases = FALSE;

    var $_multivalue_fields = array();

    var $_filters = array();


    function P4A_DB_Source($name)
    {
        parent::P4A_Data_Source($name);
    }

    function setDSN($DSN)
    {
    	$this->_DSN = $DSN;
    }

    function getDSN()
    {
    	return $this->_DSN;
    }

    function setTable($table)
    {
        $this->_table = $table;
    }

    function getTable()
    {
        return $this->_table;
    }

    function setFields($fields)
    {
        if ($this->getSelect()){
            p4a_error("Can't use setFields here");
        }
        $fields_keys = array_keys($fields);

        //Check if dictionary or array
        if ($fields_keys[0] !== 0){
            $this->_use_fields_aliases = TRUE;
        }
        $this->_fields = $fields;
    }

    function getFields()
    {
        return $this->_fields;
    }

    function setSelect($select)
    {
        if ($this->getFields()){
            p4a_error("Can't use setSelect here");
        }
        $this->isReadOnly(TRUE);
        $this->_select = $select;
    }

    function getSelect()
    {
        return $this->_select;
    }

    function addJoin($table, $clausole, $type="INNER")
    {
        $this->_join[] = array($type, $table, $clausole);
    }

    function getJoin()
    {
        return $this->_join;
    }

    function setWhere($where)
    {
        $this->resetNumRows();
        $this->_where = $where;
    }

    function getWhere()
    {
        return $this->_where;
    }

    function addGroup($group)
    {
        $this->_group[] = $group;
    }

    function setGroup($group)
    {
        $this->_group = array();
        if (! is_array($group)){
            $group = array($group);
        }

        foreach($group as $g){
            $this->addGroup($g);
        }
    }

    function getGroup()
    {
        return $this->_group;
    }

    function setQuery($query)
    {
        $this->_query = $query;
        $this->isReadOnly(TRUE);
        $this->isSortable(FALSE);
    }

    function setLimitQuery($query, $limit, $offset=0)
    {
        $this->_query = $query;
        $this->_limit = $limit;
        $this->_offset = $offset;
        $this->isReadOnly(TRUE);
        $this->isSortable(FALSE);
    }

    function getQuery()
    {
        return $this->_query;
    }

    function addFilter($filter, &$obj)
    {
        $this->_filters[$filter] =& $obj;
        $this->resetNumRows();
    }

    function dropFilter($filter)
    {
        if (array_key_exists($filter,$this->_filters)) {
            $this->resetNumRows();
            unset($this->_filters[$filter]);
        }
    }

    function getFilters()
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

    function load()
    {
        if (!$this->getQuery() and !$this->getTable()){
            p4a_error("ERRORE");
        }

        $db =& P4A_DB::singleton($this->getDSN());

        $query = $this->_composeSelectStructureQuery();
		$rs = $db->adapter->selectLimit($query, 1, 0);

        if ($db->adapter->metaError()) {
            $e = new P4A_Error('A query has returned an error', $this, $rs);
            if ($this->errorHandler('onQueryError', $e) !== PROCEED) {
                die();
            }
        } else {
            $main_table = $this->getTable();
            $array_fields = $this->getFields();

            for ($i=0; $i<$rs->fieldCount(); $i++) {
            	$col = $rs->fetchField($i);
                $field_name = $col->name;
				$col->meta_type = $rs->metaType($col);
                if (isset($this->fields->$field_name)) {
                    continue;
                }
                $this->fields->build("p4a_data_field",$field_name);
				$this->fields->$field_name->setDSN($this->getDSN());
				$this->fields->$field_name->setLength($col->max_length);
                if ($col->meta_type == 'I' and $col->max_length == 1) {
                    $col->meta_type = 'L';
                }

                switch ($col->meta_type) {
                    case 'C';
                    	// Character fields that should be shown in a <input type="text"> tag
                    	$this->fields->$field_name->setType('text');
                    	break;
                    case 'X';
                    	// Clob (character large objects), or large text fields that should be shown in a <textarea>
                    	$this->fields->$field_name->setType('text');
                    	break;
                    case 'D':
                    	// Date field
                        $this->fields->$field_name->setType('date');
                        break;
                    case 'T':
                    	// Timestamp field
                        $this->fields->$field_name->setType('text');
                        break;
                    case 'L':
                    	// Logical field (boolean or bit-field)
                        $this->fields->$field_name->setType('boolean');
                        break;
                    case 'N':
                    	// Numeric field. Includes decimal, numeric, floating point, and real
                        $this->fields->$field_name->setType('decimal');
                        break;
                    case 'R':
                    	// Counter or Autoincrement field. Must be numeric
                    	if (P4A_AUTO_DB_SEQUENCES) {
                    		$this->fields->$field_name->setSequence("{$col->table}_{$field_name}_seq");
                    	}
                    case 'I':
                    	// Integer field
                        $this->fields->$field_name->setType('integer');
                        break;
                    case 'B':
                    	// Blob, or binary large objects
                        $this->fields->$field_name->setType('text');
                        break;
                    default:
                        p4a_error("unknown type {$col->meta_type} for field {$field_name}");
                }

				// if field is not on main table is not updatable
            	if (!isset($col->table) or !strlen($col->table)) {
            		if (count($this->getJoin())) {
            			$this->fields->$field_name->setReadOnly();
            		} else {
						$this->fields->$field_name->setTable($main_table);
            		}
            	} elseif ($col->table != $main_table){
                    $this->fields->$field_name->setReadOnly();
                	$this->fields->$field_name->setTable($col->table);
                } else {
                	$this->fields->$field_name->setTable($col->table);
            	}

                if ($this->_use_fields_aliases and ($alias_of = array_search($field_name, $array_fields))){
                    $this->fields->$field_name->setAliasOf($alias_of);
                }
            }
        }
    }

	function getFieldName($field)
	{
		$dot_pos = strpos($field, '.');

        if ($dot_pos) {
        	$short_fld = substr($field, $dot_pos + 1);
        } else {
        	$short_fld = $field;
        }

        if ($this->fields->$short_fld->getAliasOf()) {
        	$long_fld = $this->fields->$short_fld->getAliasOf();
        } else {
        	$table = (string)$this->fields->$short_fld->getTable();
            if (strlen($table)) {
            	$table = "{$table}.";
            }
            $long_fld = $table . $this->fields->$short_fld->getName();
        }
		return array($long_fld, $short_fld);
	}

    function isReadOnly($value=null)
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

    function getPkRow($pk)
    {
		$db =& P4A_DB::singleton($this->getDSN());
		$query = $this->_composeSelectPkQuery($pk);
		$row = $db->adapter->getRow($query);
        if ($db->adapter->metaError()) {
			$e = new P4A_Error('A query has returned an error', $this, $row);
			if ($this->errorHandler('onQueryError', $e) !== PROCEED) {
				die();
			}
		}
		return $row;
    }

    function row($num_row = null, $move_pointer = true)
    {
        $db =& P4A_DB::singleton($this->getDSN());
        $query = $this->_composeSelectQuery();

        if ($num_row === null) {
            $num_row = $this->_pointer;
        }

        if ($num_row == 0) {
        	$num_row = 1;
        }

		$rs = $db->adapter->selectLimit($query, 1, $num_row-1);
        if ($db->adapter->metaError()) {
            $e = new P4A_Error('A query has returned an error', $this, $rs);
            if ($this->errorHandler('onQueryError', $e) !== PROCEED) {
                die();
            }
        } else {
            $row = $rs->fetchRow();

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
                $fk_values = $db->adapter->getCol("SELECT $fk_field FROM $table WHERE $fk='$pk_value'");
                $this->fields->$fieldname->setValue($fk_values);
                $row[$fieldname] = $fk_values;
            }
            return $row;
        }
    }

    function rowByPk($pk)
    {
		$row = $this->getPkRow($pk);
		$position = $this->getRowPosition($row);
		$this->row($position);
	}

    function getNumRows()
    {
        $db =& P4A_DB::singleton($this->getDSN());
        $query = $this->_composeSelectCountQuery();

        if ($this->_num_rows === null) {
        	$group = $this->getGroup();
        	if (count($group)) {
        		$result = $db->adapter->queryCol($query);
	        	if ($db->adapter->metaError()) {
	        		$name = $this->getName();
	        		p4a_error("query error retrieving number of rows for P4A_DB_Source \"{$name}\"");
	        	}
        		$this->_num_rows = count($result);
        	} else {
        		$result = $db->adapter->getOne($query);
	        	if ($db->adapter->metaError()) {
	        		$name = $this->getName();
	        		p4a_error("query error retrieving number of rows for P4A_DB_Source \"{$name}\"");
	        	}
	        	$this->_num_rows = (int)$result;
        	}
        }

        return $this->_num_rows;
    }

    function getRowPosition($row=null)
	{
        if (!$this->getQuery()) {
            $query  = $this->_composeSelectCountPart();
            $query .= $this->_composeFromPart();

            $new_order_array = array();
            $new_order_array_values = array();
            if ($order = $this->getOrder()) {
                $where_order = "";
                foreach($order as $field=>$direction) {
                    list($long_fld,$short_fld) = $this->getFieldName($field);

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
                $where = $this->_composeWherePart();
                if ($where != '') {
                    $query .= "$where AND $where_order ";
                } else {
                    $query .= " WHERE $where_order ";
                }
            } else {
                $query .= $this->_composeWherePart();
            }

            $query .= $this->_composeGroupPart();
            //$query .= $this->_composeOrderPart($new_order_array);
            $db =& P4A_DB::singleton($this->getDSN());

            return $db->adapter->getOne($query) + 1;
        }
    }

    function updateRowPosition()
    {
        $this->_pointer = $this->getRowPosition();
    }

    function saveRow($fields_values = array(), $pk_values = array())
    {
        if(!$this->isReadOnly()) {
            $db =& P4A_DB::singleton($this->getDSN());
            if (empty($fields_values)) {
                while($field =& $this->fields->nextItem()) {
                    if ($field->getAliasOf()) {
                        $name = $field->getAliasOf();
                    } else {
                        $name = $field->getName();
                    }

                    if (!$field->isReadOnly()) {
                        if (!array_key_exists($name,$this->_multivalue_fields)) {
                            $fields_values[$name] = $field->getNewValue();
                            if ($fields_values[$name] === "") {
                                $fields_values[$name] = NULL;
                            }
                        }
                    }
                }
            }

            if ($this->isNew()) {
                $res = $db->adapter->autoExecute($this->_table, $fields_values, "INSERT");
            } else {
                $res = $db->adapter->autoExecute($this->_table, $fields_values, "UPDATE", $this->_composePkString());
            }

            if (!$res) {
                $e = new P4A_ERROR('A query has returned an error', $this);
                if ($this->errorHandler('onQueryError', $e) !== PROCEED) {
                    die();
                }
            }

            $pks = $this->getPk();

            foreach ($this->_multivalue_fields as $fieldname=>$aField) {
                $pk_value = $this->fields->$pks->getNewValue();
                $fk_values = $this->fields->$fieldname->getNewValue();

                if (is_string($fk_values) and !empty($fk_values)) {
                    $fk_values = split(",",$fk_values);
                }

                $fk_table = $aField["table"];
                $fk_field = $aField["fk_field"];
                $fk = $aField["fk"];

                $res = $db->adapter->execute("DELETE FROM $fk_table WHERE $fk=?", array($pk_value));
                if ($db->adapter->metaError()) {
                    P4A_Error($db->adapter->metaErrorMsg($db->adapter->metaError()));
                }

                if ($fk_values) {
					foreach ($fk_values as $k=>$v) {
						$fk_values[$k] = array($v);
					}

                    $res = $db->adapter->execute("INSERT INTO $fk_table($fk,$fk_field) VALUES('$pk_value', ?)", $fk_values);
                    if ($db->adapter->metaError()) {
                        P4A_Error($db->adapter->metaErrorMsg($db->adapter->metaError()));
                    }
                }
            }

            if (is_string($pks)) {
                $row = $this->getPkRow($this->fields->$pks->getNewValue());
            } else {
                $pk_values = array();
                foreach($pks as $pk){
                    $pk_values[] = $this->fields->$pk->getNewValue();
                }
                $row = $this->getPkRow($pk_values);
            }
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

    function deleteRow()
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
                $e = new P4A_Error('A query has returned an error', $this);
                if ($this->errorHandler('onQueryError', $e) !== PROCEED) {
                    die();
                }
            }

            $this->resetNumRows();
        }

        parent::deleteRow();
    }

    function getAll($from = 0, $count = 0)
    {
        $db =& P4A_DB::singleton($this->getDSN());
        $query = $this->_composeSelectQuery();

        if ($from == 0 and $count == 0) {
            $rows = $db->adapter->getAll($query);
            if (!is_array($rows)) {
            	$rows = array();
            }
        }else{
            $rows = array();
			$rs = $db->adapter->selectLimit($query, $count, $from);

            if ($db->adapter->metaError()) {
                $e = new P4A_Error('A query has returned an error', $this, $rs);
                if ($this->errorHandler('onQueryError', $e) !== PROCEED) {
                    die();
                }
            }

            while ($row = $rs->fetchRow()) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    function _composeSelectCountQuery()
    {
        if ($this->getQuery()) {
			$query = preg_replace("/SELECT.*?FROM/isu", $this->_composeSelectCountPart() . " FROM", $this->getQuery());
        } else {
            $query  = $this->_composeSelectCountPart();
            $query .= $this->_composeFromPart();
            $query .= $this->_composeWherePart();
            $query .= $this->_composeGroupPart();
        }

		return $query;
    }

    function _composeSelectStructureQuery()
    {
        if ($this->getQuery()) {
            $query  = $this->getQuery();
        } else {
            $query  = $this->_composeSelectPart();
            $query .= $this->_composeFromPart();
            $query .= $this->_composeWherePart();
            $query .= $this->_composeGroupPart();
        }

        return $query;
    }

    function _composeSelectQuery()
    {
        if ($this->getQuery()) {
            $query =  $this->getQuery();
        } else {
            $query  = $this->_composeSelectPart();
            $query .= $this->_composeFromPart();
            $query .= $this->_composeWherePart();
            $query .= $this->_composeGroupPart();
            $query .= $this->_composeOrderPart();
        }

        return $query;
    }

    function _composeSelectPkQuery($pk_value)
    {
        $query  = $this->_composeSelectPart();
        $query .= $this->_composeFromPart();

        $where = $this->_composeWherePart();

        $pk_key = $this->getPK();
        $pk_string = "";

        if (is_array($pk_key)) {
            for($i=0;$i<count($pk_key);$i++){
                $pk_string .= "{$this->_table}.{$pk_key[$i]} = '{$pk_value[$i]}' AND ";
            }
            $pk_string = substr($pk_string,0,-4);
        } else {
            $pk_string = "{$this->_table}.{$pk_key} = '{$pk_value}' ";
        }

        if (strlen($where)) {
            $where .= "AND " . $pk_string;
        } else {
            $where = "WHERE " . $pk_string;
        }

        $query .= $where;
        $query .= $this->_composeGroupPart();
        $query .= $this->_composeOrderPart();
        return $query;
    }

    function _composeSelectPart()
    {
        $query = "SELECT ";
        if ($select_part = $this->getSelect()){
            $query .= "$select_part ";
        } else {
            if ($this->_use_fields_aliases){
                foreach($this->getFields() as $field_name=>$field_alias){
                    if ($field_alias != "" and $field_alias != "*"){
                        $query .= "$field_name AS $field_alias,";
                    }else{
                        $query .= "$field_name,";
                    }
                }
                $query = substr($query,0, -1) . " ";
            } elseif($fields = $this->getFields()) {
                foreach($fields as $field_name){
                    $query .= "$field_name,";
                }
                $query = substr($query,0, -1) . " ";
            } else {
                $query .= "* ";
            }
        }
        return $query;
    }

    function _composeSelectCountPart()
    {
        $query = "SELECT count(*) ";
        return $query;
    }

    function _composeFromPart()
    {
        $query = "FROM {$this->_table} ";

        foreach ($this->_join as $join) {
            $query .= "{$join[0]} JOIN {$join[1]} ON ({$join[2]}) ";
        }
        return $query;
    }

    function _composeWherePart()
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
            $query = " WHERE " . substr($query,0,-4);
        }
        return $query;
    }

    function _composeGroupPart()
    {
        $query = "";
        if ($group = $this->getGroup()) {
            $query .= "GROUP BY " . join($group, ",") . " ";
        }
        return $query;
    }

    function _composeOrderPart($order = array())
    {
        $query = "";
        if (!$order) {
            $order = $this->getOrder();
        }
        if ($order) {
            $query .= "ORDER BY ";

            foreach ($order as $field=>$direction) {
            	list($long_fld,$short_fld) = $this->getFieldName($field);
                $query .= "$long_fld $direction,";
            }
            $query = substr($query,0, -1) . " ";
        }
        return $query;
    }

    function _composePkString()
    {
        $pks = $this->getPk();
        $pk_values = $this->getPkValues();

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

    function resetNumRows()
    {
        $this->_num_rows = NULL;
    }

    function addMultivalueField($fieldname, $table, $fk = NULL, $fk_field = NULL)
    {
        $db =& P4A_DB::singleton($this->getDSN());

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
            $info = $db->adapter->metaColumns($table);
            foreach($info as $field) {
                if ($field->name != $fk ) {
                    $fk_field = $field->name;
                    break;
                }
            }
        }
        $this->_multivalue_fields[$fieldname]['fk_field'] = $fk_field;

        $this->fields->build("P4A_Data_Field",$fieldname);
    	$this->fields->$fieldname->setDSN($this->getDSN());
    }

    function __sleep()
    {
        $this->resetNumRows();
        return array_keys(get_object_vars($this));
    }
}