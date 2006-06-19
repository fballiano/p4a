<?php
// +----------------------------------------------------------------------+
// | PHP versions 4 and 5                                                 |
// +----------------------------------------------------------------------+
// | Copyright (c) 1998-2006 Manuel Lemos, Tomas V.V.Cox,                 |
// | Stig. S. Bakken, Lukas Smith, Frank M. Kromann                       |
// | All rights reserved.                                                 |
// +----------------------------------------------------------------------+
// | MDB2 is a merge of PEAR DB and Metabases that provides a unified DB  |
// | API as well as database abstraction for PHP applications.            |
// | This LICENSE is in the BSD license style.                            |
// |                                                                      |
// | Redistribution and use in source and binary forms, with or without   |
// | modification, are permitted provided that the following conditions   |
// | are met:                                                             |
// |                                                                      |
// | Redistributions of source code must retain the above copyright       |
// | notice, this list of conditions and the following disclaimer.        |
// |                                                                      |
// | Redistributions in binary form must reproduce the above copyright    |
// | notice, this list of conditions and the following disclaimer in the  |
// | documentation and/or other materials provided with the distribution. |
// |                                                                      |
// | Neither the name of Manuel Lemos, Tomas V.V.Cox, Stig. S. Bakken,    |
// | Lukas Smith nor the names of his contributors may be used to endorse |
// | or promote products derived from this software without specific prior|
// | written permission.                                                  |
// |                                                                      |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS  |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT    |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS    |
// | FOR A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE      |
// | REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,          |
// | INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, |
// | BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS|
// |  OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED  |
// | AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT          |
// | LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY|
// | WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE          |
// | POSSIBILITY OF SUCH DAMAGE.                                          |
// +----------------------------------------------------------------------+
// | Author: Lukas Smith <smith@pooteeweet.org>                           |
// +----------------------------------------------------------------------+
//
// $Id$
//

require_once 'MDB2/Driver/Reverse/Common.php';

/**
 * MDB2 Oracle driver for the schema reverse engineering module
 *
 * @package MDB2
 * @category Database
 * @author  Lukas Smith <smith@dybnet.de>
 */
class MDB2_Driver_Reverse_oci8 extends MDB2_Driver_Reverse_Common
{
    // {{{ getTableFieldDefinition()

    /**
     * Get the stucture of a field into an array
     *
     * @param string    $table         name of table that should be used in method
     * @param string    $field_name     name of field that should be used in method
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function getTableFieldDefinition($table, $field_name)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $result = $db->loadModule('Datatype', null, true);
        if (PEAR::isError($result)) {
            return $result;
        }

        $table = $db->quote($table, 'text');
        $field_name = $db->quote($field_name, 'text');
        $query = 'SELECT column_name name, data_type "type", nullable, data_default "default"';
        $query.= ', COALESCE(data_precision, data_length) "length", data_scale "scale"';
        $query.= ' FROM user_tab_columns';
        $query.= ' WHERE (table_name='.$table.' OR table_name='.strtoupper($table).')';
        $query.= ' AND (column_name='.$field_name.' OR column_name='.strtoupper($field_name).')';
        $query.= ' ORDER BY column_id';
        $column = $db->queryRow($query, null, MDB2_FETCHMODE_ASSOC);
        if (PEAR::isError($column)) {
            return $column;
        }

        if (empty($column)) {
            return $db->raiseError(MDB2_ERROR_NOT_FOUND, null, null,
                'getTableFieldDefinition: it was not specified an existing table column');
        }

        $column = array_change_key_case($column, CASE_LOWER);
        if ($db->options['portability'] & MDB2_PORTABILITY_FIX_CASE) {
            if ($db->options['field_case'] == CASE_LOWER) {
                $column['name'] = strtolower($column['name']);
            } else {
                $column['name'] = strtoupper($column['name']);
            }
        }
        list($types, $length, $unsigned, $fixed) = $db->datatype->mapNativeDatatype($column);
        $notnull = false;
        if (!empty($column['nullable']) && $column['nullable'] == 'N') {
            $notnull = true;
        }
        $default = false;
        if (array_key_exists('default', $column)) {
            $default = $column['default'];
            if ($default === 'NULL') {
                $default = null;
            }
            if (is_null($default) && $notnull) {
                $default = '';
            }
        }

        $definition[0] = array('notnull' => $notnull);
        if ($length > 0) {
            $definition[0]['length'] = $length;
        }
        if (!is_null($unsigned)) {
            $definition[0]['unsigned'] = $unsigned;
        }
        if (!is_null($fixed)) {
            $definition[0]['fixed'] = $fixed;
        }
        if ($default !== false) {
            $definition[0]['default'] = $default;
        }
        foreach ($types as $key => $type) {
            $definition[$key] = $definition[0];
            $definition[$key]['type'] = $type;
        }
        return $definition;
    }

    // }}}

    // {{{ getTableIndexDefinition()

    /**
     * Get the stucture of an index into an array
     *
     * @param string    $table      name of table that should be used in method
     * @param string    $index_name name of index that should be used in method
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function getTableIndexDefinition($table, $index_name)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $table = $db->quote($table, 'text');
        $index_name = $db->quote($db->getIndexName($index_name), 'text');
        $query = 'SELECT * FROM user_indexes';
        $query.= ' WHERE (table_name='.$table.' OR table_name='.strtoupper($table).')';
        $query.= ' AND (index_name='.$index_name.' OR index_name='.strtoupper($index_name).')';
        $row = $db->queryRow($query, null, MDB2_FETCHMODE_ASSOC);
        if (PEAR::isError($row)) {
            return $row;
        }
        $definition = array();
        if (!empty($row)) {
            $row = array_change_key_case($row, CASE_LOWER);
            $key_name = $row['index_name'];
            if ($db->options['portability'] & MDB2_PORTABILITY_FIX_CASE) {
                if ($db->options['field_case'] == CASE_LOWER) {
                    $key_name = strtolower($key_name);
                } else {
                    $key_name = strtoupper($key_name);
                }
            }
            $query = 'SELECT * FROM user_ind_columns';
            $query.= ' WHERE (table_name='.$table.' OR table_name='.strtoupper($table).')';
            $query.= ' AND (index_name='.$index_name.' OR index_name='.strtoupper($index_name).')';
            $result = $db->query($query);
            if (PEAR::isError($result)) {
                return $result;
            }
            while ($colrow = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
                $colrow = array_change_key_case($colrow, CASE_LOWER);
                $column_name = $colrow['column_name'];
                if ($db->options['portability'] & MDB2_PORTABILITY_FIX_CASE) {
                    if ($db->options['field_case'] == CASE_LOWER) {
                        $column_name = strtolower($column_name);
                    } else {
                        $column_name = strtoupper($column_name);
                    }
                }
                $definition['fields'][$column_name] = array();
                if (!empty($colrow['descend'])) {
                    $definition['fields'][$column_name]['sorting'] =
                        ($colrow['descend'] == 'ASC' ? 'ascending' : 'descending');
                }
            }
            $result->free();
        }
        if (empty($definition['fields'])) {
            return $db->raiseError(MDB2_ERROR_NOT_FOUND, null, null,
                'getTableIndexDefinition: it was not specified an existing table index');
        }
        return $definition;
    }

    // }}}
    // {{{ getTableConstraintDefinition()

    /**
     * Get the stucture of a constraint into an array
     *
     * @param string    $table      name of table that should be used in method
     * @param string    $index_name name of index that should be used in method
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function getTableConstraintDefinition($table, $index_name)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        if (strtolower($index_name) != 'primary') {
            $index_name = $db->getIndexName($index_name);
        }

        $database_name = $db->quote(strtoupper($db->dsn['username']), 'text');
        $index_name = $db->quote($index_name, 'text');
        $table = $db->quote($table, 'text');
        $query = "SELECT * FROM all_constraints WHERE owner = $database_name";
        $query.= ' AND (table_name='.$table.' OR table_name='.strtoupper($table).')';
        $query.= ' AND (index_name='.$index_name.' OR index_name='.strtoupper($index_name).')';
        $result = $db->query($query);
        if (PEAR::isError($result)) {
            return $result;
        }
        $definition = array();
        while (is_array($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC))) {
            $row = array_change_key_case($row, CASE_LOWER);
            $key_name = $row['constraint_name'];
            if ($row) {
                $definition['primary'] = $row['constraint_type'] == 'P';
                $definition['unique'] = $row['constraint_type'] == 'U';

                $query = 'SELECT * FROM all_cons_columns WHERE constraint_name='.$db->quote($key_name, 'text');
                $query.= ' AND (table_name='.$table.' OR table_name='.strtoupper($table).')';
                $colres = $db->query($query);
                if (PEAR::isError($colres)) {
                    return $colres;
                }
                while ($colrow = $colres->fetchRow(MDB2_FETCHMODE_ASSOC)) {
                    $colrow = array_change_key_case($colrow, CASE_LOWER);
                    $column_name = $colrow['column_name'];
                    if ($db->options['portability'] & MDB2_PORTABILITY_FIX_CASE) {
                        if ($db->options['field_case'] == CASE_LOWER) {
                            $column_name = strtolower($column_name);
                        } else {
                            $column_name = strtoupper($column_name);
                        }
                    }
                    $definition['fields'][$column_name] = array();
                }
            }
        }
        $result->free();
        if (empty($definition['fields'])) {
            return $db->raiseError(MDB2_ERROR_NOT_FOUND, null, null,
                'getTableConstraintDefinition: it was not specified an existing table constraint');
        }
        return $definition;
    }

    // }}}
    // {{{ getSequenceDefinition()

    /**
     * Get the stucture of a sequence into an array
     *
     * @param string    $sequence   name of sequence that should be used in method
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function getSequenceDefinition($sequence)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $sequence_name = $db->quote($db->getSequenceName($sequence), 'text');
        $query = 'SELECT last_number FROM user_sequences';
        $query.= ' WHERE sequence_name='.$sequence_name.' OR sequence_name='.strtoupper($sequence_name);
        $start = $db->queryOne($query);
        if (PEAR::isError($start)) {
            return $start;
        }
        $start = ($db->currId($sequence)+1);
        if (PEAR::isError($start)) {
            return $start;
        }
        $definition = array();
        if ($start != 1) {
            $definition = array('start' => $start);
        }
        return $definition;
    }

    // }}}
    // {{{ tableInfo()

    /**
     * Returns information about a table or a result set
     *
     * NOTE: only supports 'table' and 'flags' if <var>$result</var>
     * is a table name.
     *
     * NOTE: flags won't contain index information.
     *
     * @param object|string  $result  MDB2_result object from a query or a
     *                                 string containing the name of a table.
     *                                 While this also accepts a query result
     *                                 resource identifier, this behavior is
     *                                 deprecated.
     * @param int            $mode    a valid tableInfo mode
     *
     * @return array  an associative array with the information requested.
     *                 A MDB2_Error object on failure.
     *
     * @see MDB2_Driver_Common::tableInfo()
     */
    function tableInfo($result, $mode = null)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        if ($db->options['portability'] & MDB2_PORTABILITY_FIX_CASE) {
            if ($db->options['field_case'] == CASE_LOWER) {
                $case_func = 'strtolower';
            } else {
                $case_func = 'strtoupper';
            }
        } else {
            $case_func = 'strval';
        }

        $res = array();

        if (is_string($result)) {
            /*
             * Probably received a table name.
             * Create a result resource identifier.
             */
            $query = 'SELECT column_name, data_type, data_length, nullable';
            $query.= ' FROM user_tab_columns';
            $query.= ' WHERE table_name='.$db->quote(strtoupper($result), 'text');
            $query.= ' OR table_name='.$db->quote($result, 'text');
            $query.= ' ORDER BY column_id';

            $stmt =& $db->_doQuery($query, false);
            if (PEAR::isError($stmt)) {
                return $stmt;
            }

            $i = 0;
            while (@OCIFetch($stmt)) {
                $res[$i] = array(
                    'table'  => $case_func($result),
                    'name'   => $case_func(@OCIResult($stmt, 1)),
                    'type'   => @OCIResult($stmt, 2),
                    'length' => @OCIResult($stmt, 3),
                    'flags'  => (@OCIResult($stmt, 4) == 'N') ? 'not_null' : '',
                );
                $res[$i]['mdb2type'] = $db->datatype->mapNativeDatatype($res[$i]);
                if ($mode & MDB2_TABLEINFO_ORDER) {
                    $res['order'][$res[$i]['name']] = $i;
                }
                if ($mode & MDB2_TABLEINFO_ORDERTABLE) {
                    $res['ordertable'][$res[$i]['table']][$res[$i]['name']] = $i;
                }
                $i++;
            }

            if ($mode) {
                $res['num_fields'] = $i;
            }
            @OCIFreeStatement($stmt);

        } else {
            if (MDB2::isResultCommon($result)) {
                /*
                 * Probably received a result object.
                 * Extract the result resource identifier.
                 */
                $resource = $result->getResource();
            }

            $res = array();

            $count = $result->numCols();
            if ($mode) {
                $res['num_fields'] = $count;
            }
            for ($i = 0; $i < $count; $i++) {
                $column = array(
                    'table'  => '',
                    'name'   => $case_func(@OCIColumnName($resource, $i+1)),
                    'type'   => @OCIColumnType($resource, $i+1),
                    'length' => @OCIColumnSize($resource, $i+1),
                    'flags'  => '',
                );
                $res[$i] = $column;
                $res[$i]['mdb2type'] = $db->datatype->mapNativeDatatype($res[$i]);
                if ($mode & MDB2_TABLEINFO_ORDER) {
                    $res['order'][$res[$i]['name']] = $i;
                }
                if ($mode & MDB2_TABLEINFO_ORDERTABLE) {
                    $res['ordertable'][$res[$i]['table']][$res[$i]['name']] = $i;
                }
            }
        }
        return $res;
    }
}
?>