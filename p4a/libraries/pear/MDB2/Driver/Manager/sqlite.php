<?php
// +----------------------------------------------------------------------+
// | PHP versions 4 and 5                                                 |
// +----------------------------------------------------------------------+
// | Copyright (c) 1998-2006 Manuel Lemos, Tomas V.V.Cox,                 |
// | Stig. S. Bakken, Lukas Smith                                         |
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

require_once 'MDB2/Driver/Manager/Common.php';

/**
 * MDB2 SQLite driver for the management modules
 *
 * @package MDB2
 * @category Database
 * @author  Lukas Smith <smith@pooteeweet.org>
 */
class MDB2_Driver_Manager_sqlite extends MDB2_Driver_Manager_Common
{
    // {{{ createDatabase()

    /**
     * create a new database
     *
     * @param string $name name of the database that should be created
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function createDatabase($name)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $database_file = $db->_getDatabaseFile($name);
        if (file_exists($database_file)) {
            return $db->raiseError(MDB2_ERROR_ALREADY_EXISTS, null, null,
                'createDatabase: database already exists');
        }
        $php_errormsg = '';
        $handle = @sqlite_open($database_file, $db->dsn['mode'], $php_errormsg);
        if (!$handle) {
            return $db->raiseError(MDB2_ERROR_CANNOT_CREATE, null, null,
                'createDatabase: '.(isset($php_errormsg) ? $php_errormsg : 'could not create the database file'));
        }
        @sqlite_close($handle);
        return MDB2_OK;
    }

    // }}}
    // {{{ dropDatabase()

    /**
     * drop an existing database
     *
     * @param string $name name of the database that should be dropped
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function dropDatabase($name)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $database_file = $db->_getDatabaseFile($name);
        if (!@file_exists($database_file)) {
            return $db->raiseError(MDB2_ERROR_CANNOT_DROP, null, null,
                'dropDatabase: database does not exist');
        }
        $result = @unlink($database_file);
        if (!$result) {
            return $db->raiseError(MDB2_ERROR_CANNOT_DROP, null, null,
                'dropDatabase: '.(isset($php_errormsg) ? $php_errormsg : 'could not remove the database file'));
        }
        return MDB2_OK;
    }

    // }}}
    // {{{ alterTable()

    /**
     * alter an existing table
     *
     * @param string $name         name of the table that is intended to be changed.
     * @param array $changes     associative array that contains the details of each type
     *                             of change that is intended to be performed. The types of
     *                             changes that are currently supported are defined as follows:
     *
     *                             name
     *
     *                                New name for the table.
     *
     *                            add
     *
     *                                Associative array with the names of fields to be added as
     *                                 indexes of the array. The value of each entry of the array
     *                                 should be set to another associative array with the properties
     *                                 of the fields to be added. The properties of the fields should
     *                                 be the same as defined by the Metabase parser.
     *
     *
     *                            remove
     *
     *                                Associative array with the names of fields to be removed as indexes
     *                                 of the array. Currently the values assigned to each entry are ignored.
     *                                 An empty array should be used for future compatibility.
     *
     *                            rename
     *
     *                                Associative array with the names of fields to be renamed as indexes
     *                                 of the array. The value of each entry of the array should be set to
     *                                 another associative array with the entry named name with the new
     *                                 field name and the entry named Declaration that is expected to contain
     *                                 the portion of the field declaration already in DBMS specific SQL code
     *                                 as it is used in the CREATE TABLE statement.
     *
     *                            change
     *
     *                                Associative array with the names of the fields to be changed as indexes
     *                                 of the array. Keep in mind that if it is intended to change either the
     *                                 name of a field and any other properties, the change array entries
     *                                 should have the new names of the fields as array indexes.
     *
     *                                The value of each entry of the array should be set to another associative
     *                                 array with the properties of the fields to that are meant to be changed as
     *                                 array entries. These entries should be assigned to the new values of the
     *                                 respective properties. The properties of the fields should be the same
     *                                 as defined by the Metabase parser.
     *
     *                            Example
     *                                array(
     *                                    'name' => 'userlist',
     *                                    'add' => array(
     *                                        'quota' => array(
     *                                            'type' => 'integer',
     *                                            'unsigned' => 1
     *                                        )
     *                                    ),
     *                                    'remove' => array(
     *                                        'file_limit' => array(),
     *                                        'time_limit' => array()
     *                                    ),
     *                                    'change' => array(
     *                                        'name' => array(
     *                                            'length' => '20',
     *                                            'definition' => array(
     *                                                'type' => 'text',
     *                                                'length' => 20,
     *                                            ),
     *                                        )
     *                                    ),
     *                                    'rename' => array(
     *                                        'sex' => array(
     *                                            'name' => 'gender',
     *                                            'definition' => array(
     *                                                'type' => 'text',
     *                                                'length' => 1,
     *                                                'default' => 'M',
     *                                            ),
     *                                        )
     *                                    )
     *                                )
     *
     * @param boolean $check     indicates whether the function should just check if the DBMS driver
     *                             can perform the requested table alterations if the value is true or
     *                             actually perform them otherwise.
     * @access public
     *
      * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    function alterTable($name, $changes, $check)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        // actually sqlite 2.x supports no ALTER TABLE at all ..
        // so the only solution is:
        // - reverse engineer table schema
        // - alter table schema in memory
        // - read all data into memory (or file?)
        // - drop table
        // - create table
        // - import data
        $version = $db->getServerVersion();
        foreach ($changes as $change_name => $change) {
            switch ($change_name) {
            case 'add':
                if ($version['major'] >= 3 && $version['minor'] >= 1) {
                    break;
                }
            case 'name':
                if ($version['major'] >= 3 && $version['minor'] >= 1) {
                    break;
                }
            case 'remove':
            case 'change':
            case 'rename':
            default:
                return $db->raiseError(MDB2_ERROR_CANNOT_ALTER, null, null,
                    'alterTable: change type "'.$change_name.'" not yet supported');
            }
        }

        if ($check) {
            return MDB2_OK;
        }

        $query = '';
        if (array_key_exists('name', $changes)) {
            $change_name = $db->quoteIdentifier($changes['name'], true);
            $query .= 'RENAME TO ' . $change_name;
        }

        if (array_key_exists('add', $changes)) {
            foreach ($changes['add'] as $field_name => $field) {
                if ($query) {
                    $query.= ', ';
                }
                $query.= 'ADD COLUMN ' . $db->getDeclaration($field['type'], $field_name, $field);
            }
        }

        if (!$query) {
            return MDB2_OK;
        }

        $name = $db->quoteIdentifier($name, true);
        return $db->exec("ALTER TABLE $name $query");
    }


    // }}}
    // {{{ listDatabases()

    /**
     * list all databases
     *
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function listDatabases()
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        return $db->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
            'listDatabases: list databases is not supported');
    }

    // }}}
    // {{{ listUsers()

    /**
     * list all users
     *
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function listUsers()
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        return $db->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
            'listDatabases: list databases is not supported');
    }

    // }}}
    // {{{ listTables()

    /**
     * list all tables in the current database
     *
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function listTables()
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $query = "SELECT name FROM sqlite_master WHERE type='table' AND sql NOT NULL ORDER BY name";
        $table_names = $db->queryCol($query);
        if (PEAR::isError($table_names)) {
            return $table_names;
        }
        $result = array();
        foreach ($table_names as $table_name) {
            if (!$this->_fixSequenceName($table_name, true)) {
                $result[] = $table_name;
            }
        }
        if ($db->options['portability'] & MDB2_PORTABILITY_FIX_CASE) {
            $result = array_map(($db->options['field_case'] == CASE_LOWER ? 'strtolower' : 'strtoupper'), $result);
        }
        return $result;
    }

    // }}}
    // {{{ listTableFields()

    /**
     * list all fields in a tables in the current database
     *
     * @param string $table name of table that should be used in method
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function listTableFields($table)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $table = $db->quoteIdentifier($table, true);
        $query = "SELECT * FROM $table";
        $db->setLimit(1);
        $result2 = $db->query($query);
        if (PEAR::isError($result2)) {
            return $result2;
        }
        $result = $result2->getColumnNames();
        $result2->free();
        if (PEAR::isError($result)) {
            return $result;
        }
        return array_flip($result);
    }

    // }}}
    // {{{ createIndex()

    /**
     * Get the stucture of a field into an array
     *
     * @param string    $table         name of the table on which the index is to be created
     * @param string    $name         name of the index to be created
     * @param array     $definition        associative array that defines properties of the index to be created.
     *                                 Currently, only one property named FIELDS is supported. This property
     *                                 is also an associative with the names of the index fields as array
     *                                 indexes. Each entry of this array is set to another type of associative
     *                                 array that specifies properties of the index that are specific to
     *                                 each field.
     *
     *                                Currently, only the sorting property is supported. It should be used
     *                                 to define the sorting direction of the index. It may be set to either
     *                                 ascending or descending.
     *
     *                                Not all DBMS support index sorting direction configuration. The DBMS
     *                                 drivers of those that do not support it ignore this property. Use the
     *                                 function support() to determine whether the DBMS driver can manage indexes.

     *                                 Example
     *                                    array(
     *                                        'fields' => array(
     *                                            'user_name' => array(
     *                                                'sorting' => 'ascending'
     *                                            ),
     *                                            'last_login' => array()
     *                                        )
     *                                    )
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function createIndex($table, $name, $definition)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $table = $db->quoteIdentifier($table, true);
        $name  = $db->getIndexName($name);
        $query = "CREATE INDEX $name ON $table";
        $fields = array();
        foreach ($definition['fields'] as $field_name => $field) {
            $field_string = $field_name;
            if (array_key_exists('sorting', $field)) {
                switch ($field['sorting']) {
                case 'ascending':
                    $field_string.= ' ASC';
                    break;
                case 'descending':
                    $field_string.= ' DESC';
                    break;
                }
            }
            $fields[] = $field_string;
        }
        $query .= ' ('.implode(', ', $fields) . ')';
        return $db->exec($query);
    }

    // }}}
    // {{{ dropIndex()

    /**
     * drop existing index
     *
     * @param string    $table         name of table that should be used in method
     * @param string    $name         name of the index to be dropped
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function dropIndex($table, $name)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $name = $db->getIndexName($name);
        return $db->exec("DROP INDEX $name");
    }

    // }}}
    // {{{ listTableIndexes()

    /**
     * list all indexes in a table
     *
     * @param string    $table      name of table that should be used in method
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function listTableIndexes($table)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $table = $db->quote($table, 'text');
        $query = "SELECT sql FROM sqlite_master WHERE type='index' AND ";
        if ($db->options['portability'] & MDB2_PORTABILITY_FIX_CASE) {
            $query.= 'LOWER(tbl_name)='.strtolower($table);
        } else {
            $query.= "tbl_name=$table";
        }
        $query.= " AND sql NOT NULL ORDER BY name";
        $indexes = $db->queryCol($query, 'text');
        if (PEAR::isError($indexes)) {
            return $indexes;
        }

        $result = array();
        foreach ($indexes as $sql) {
            if (preg_match("/^create index ([^ ]*) on /i", $sql, $tmp)) {
                $result[$this->_fixIndexName($tmp[1])] = true;
            }
        }

        if ($db->options['portability'] & MDB2_PORTABILITY_FIX_CASE) {
            $result = array_change_key_case($result, $db->options['field_case']);
        }
        return array_keys($result);
    }

    // }}}
    // {{{ createConstraint()

    /**
     * create a constraint on a table
     *
     * @param string    $table         name of the table on which the constraint is to be created
     * @param string    $name         name of the constraint to be created
     * @param array     $definition        associative array that defines properties of the constraint to be created.
     *                                 Currently, only one property named FIELDS is supported. This property
     *                                 is also an associative with the names of the constraint fields as array
     *                                 constraints. Each entry of this array is set to another type of associative
     *                                 array that specifies properties of the constraint that are specific to
     *                                 each field.
     *
     *                                 Example
     *                                    array(
     *                                        'fields' => array(
     *                                            'user_name' => array(),
     *                                            'last_login' => array()
     *                                        )
     *                                    )
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function createConstraint($table, $name, $definition)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        if (array_key_exists('primary', $definition) && $definition['primary']) {
            return $db->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
                'createConstraint: Creating Primary Constraints is not supported');
        }

        $table = $db->quoteIdentifier($table, true);
        $name  = $db->getIndexName($name);
        $query = "CREATE UNIQUE INDEX $name ON $table";
        $fields = array();
        foreach ($definition['fields'] as $field_name => $field) {
            $field_string = $field_name;
            if (array_key_exists('sorting', $field)) {
                switch ($field['sorting']) {
                case 'ascending':
                    $field_string.= ' ASC';
                    break;
                case 'descending':
                    $field_string.= ' DESC';
                    break;
                }
            }
            $fields[] = $field_string;
        }
        $query .= ' ('.implode(', ', $fields) . ')';
        return $db->exec($query);
    }

    // }}}
    // {{{ dropConstraint()

    /**
     * drop existing constraint
     *
     * @param string    $table        name of table that should be used in method
     * @param string    $name         name of the constraint to be dropped
     * @param string    $primary      hint if the constraint is primary
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function dropConstraint($table, $name, $primary = false)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        if ($primary || $name == 'PRIMARY') {
            return $db->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
                'dropConstraints: Dropping Primary Constraints is not supported');
        }

        $name = $db->getIndexName($name);
        return $db->exec("DROP INDEX $name");
    }

    // }}}
    // {{{ listTableConstraints()

    /**
     * list all sonstraints in a table
     *
     * @param string    $table      name of table that should be used in method
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function listTableConstraints($table)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $table = $db->quote($table, 'text');
        $query = "SELECT sql FROM sqlite_master WHERE type='index' AND ";
        if ($db->options['portability'] & MDB2_PORTABILITY_FIX_CASE) {
            $query.= 'LOWER(tbl_name)='.strtolower($table);
        } else {
            $query.= "tbl_name=$table";
        }
        $query.= " AND sql NOT NULL ORDER BY name";
        $indexes = $db->queryCol($query, 'text');
        if (PEAR::isError($indexes)) {
            return $indexes;
        }

        $result = array();
        foreach ($indexes as $sql) {
            if (preg_match("/^create unique index ([^ ]*) on /i", $sql, $tmp)) {
                $result[$this->_fixIndexName($tmp[1])] = true;
            }
        }

        if ($db->options['portability'] & MDB2_PORTABILITY_FIX_CASE) {
            $result = array_change_key_case($result, $db->options['field_case']);
        }
        return array_keys($result);
    }

    // }}}
    // {{{ createSequence()

    /**
     * create sequence
     *
     * @param string    $seq_name     name of the sequence to be created
     * @param string    $start         start value of the sequence; default is 1
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function createSequence($seq_name, $start = 1)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $sequence_name = $db->quoteIdentifier($db->getSequenceName($seq_name), true);
        $seqcol_name = $db->quoteIdentifier($db->options['seqcol_name'], true);
        $query = "CREATE TABLE $sequence_name ($seqcol_name INTEGER PRIMARY KEY DEFAULT 0 NOT NULL)";
        $res = $db->exec($query);
        if (PEAR::isError($res)) {
            return $res;
        }
        if ($start == 1) {
            return MDB2_OK;
        }
        $res = $db->exec("INSERT INTO $sequence_name ($seqcol_name) VALUES (".($start-1).')');
        if (!PEAR::isError($res)) {
            return MDB2_OK;
        }
        // Handle error
        $result = $db->exec("DROP TABLE $sequence_name");
        if (PEAR::isError($result)) {
            return $db->raiseError($result, null, null,
                'createSequence: could not drop inconsistent sequence table');
        }
        return $db->raiseError($res, null, null,
            'createSequence: could not create sequence table');
    }

    // }}}
    // {{{ dropSequence()

    /**
     * drop existing sequence
     *
     * @param string    $seq_name     name of the sequence to be dropped
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function dropSequence($seq_name)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $sequence_name = $db->quoteIdentifier($db->getSequenceName($seq_name), true);
        return $db->exec("DROP TABLE $sequence_name");
    }

    // }}}
    // {{{ listSequences()

    /**
     * list all sequences in the current database
     *
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function listSequences()
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $query = "SELECT name FROM sqlite_master WHERE type='table' AND sql NOT NULL ORDER BY name";
        $table_names = $db->queryCol($query);
        if (PEAR::isError($table_names)) {
            return $table_names;
        }
        $result = array();
        foreach ($table_names as $table_name) {
            if ($sqn = $this->_fixSequenceName($table_name, true)) {
                $result[] = $sqn;
            }
        }
        if ($db->options['portability'] & MDB2_PORTABILITY_FIX_CASE) {
            $result = array_map(($db->options['field_case'] == CASE_LOWER ? 'strtolower' : 'strtoupper'), $result);
        }
        return $result;
    }

    // }}}
}
?>
