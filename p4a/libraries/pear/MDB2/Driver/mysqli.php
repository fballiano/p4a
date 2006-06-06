<?php
// vim: set et ts=4 sw=4 fdm=marker:
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

/**
 * MDB2 MySQLi driver
 *
 * @package MDB2
 * @category Database
 * @author  Lukas Smith <smith@pooteeweet.org>
 */
class MDB2_Driver_mysqli extends MDB2_Driver_Common
{
    // {{{ properties
    var $escape_quotes = "\\";

    // }}}
    // {{{ constructor

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        $this->phptype = 'mysqli';
        $this->dbsyntax = 'mysql';

        $this->supported['sequences'] = 'emulated';
        $this->supported['indexes'] = true;
        $this->supported['affected_rows'] = true;
        $this->supported['transactions'] = false;
        $this->supported['summary_functions'] = true;
        $this->supported['order_by_text'] = true;
        $this->supported['current_id'] = 'emulated';
        $this->supported['limit_queries'] = true;
        $this->supported['LOBs'] = true;
        $this->supported['replace'] = true;
        $this->supported['sub_selects'] = 'emulated';
        $this->supported['auto_increment'] = true;
        $this->supported['primary_key'] = true;
        $this->supported['result_introspection'] = true;
        $this->supported['prepared_statements'] = 'emulated';

        $this->options['default_table_type'] = null;
        $this->options['multi_query'] = false;
    }

    // }}}
    // {{{ errorInfo()

    /**
     * This method is used to collect information about an error
     *
     * @param integer $error
     * @return array
     * @access public
     */
    function errorInfo($error = null)
    {
        if ($this->connection) {
            $native_code = @mysqli_errno($this->connection);
            $native_msg  = @mysqli_error($this->connection);
        } else {
            $native_code = @mysqli_errno();
            $native_msg  = @mysqli_error();
        }
        if (is_null($error)) {
            static $ecode_map;
            if (empty($ecode_map)) {
                $ecode_map = array(
                    1004 => MDB2_ERROR_CANNOT_CREATE,
                    1005 => MDB2_ERROR_CANNOT_CREATE,
                    1006 => MDB2_ERROR_CANNOT_CREATE,
                    1007 => MDB2_ERROR_ALREADY_EXISTS,
                    1008 => MDB2_ERROR_CANNOT_DROP,
                    1022 => MDB2_ERROR_ALREADY_EXISTS,
                    1044 => MDB2_ERROR_ACCESS_VIOLATION,
                    1046 => MDB2_ERROR_NODBSELECTED,
                    1048 => MDB2_ERROR_CONSTRAINT,
                    1049 => MDB2_ERROR_NOSUCHDB,
                    1050 => MDB2_ERROR_ALREADY_EXISTS,
                    1051 => MDB2_ERROR_NOSUCHTABLE,
                    1054 => MDB2_ERROR_NOSUCHFIELD,
                    1061 => MDB2_ERROR_ALREADY_EXISTS,
                    1062 => MDB2_ERROR_ALREADY_EXISTS,
                    1064 => MDB2_ERROR_SYNTAX,
                    1091 => MDB2_ERROR_NOT_FOUND,
                    1100 => MDB2_ERROR_NOT_LOCKED,
                    1136 => MDB2_ERROR_VALUE_COUNT_ON_ROW,
                    1142 => MDB2_ERROR_ACCESS_VIOLATION,
                    1146 => MDB2_ERROR_NOSUCHTABLE,
                    1216 => MDB2_ERROR_CONSTRAINT,
                    1217 => MDB2_ERROR_CONSTRAINT,
                );
            }
            if ($this->options['portability'] & MDB2_PORTABILITY_ERRORS) {
                $ecode_map[1022] = MDB2_ERROR_CONSTRAINT;
                $ecode_map[1048] = MDB2_ERROR_CONSTRAINT_NOT_NULL;
                $ecode_map[1062] = MDB2_ERROR_CONSTRAINT;
            } else {
                // Doing this in case mode changes during runtime.
                $ecode_map[1022] = MDB2_ERROR_ALREADY_EXISTS;
                $ecode_map[1048] = MDB2_ERROR_CONSTRAINT;
                $ecode_map[1062] = MDB2_ERROR_ALREADY_EXISTS;
            }
            if (isset($ecode_map[$native_code])) {
                $error = $ecode_map[$native_code];
            }
        }
        return array($error, $native_code, $native_msg);
    }

    // }}}
    // {{{ escape()

    /**
     * Quotes a string so it can be safely used in a query. It will quote
     * the text so it can safely be used within a query.
     *
     * @param string $text the input string to quote
     * @return string quoted string
     * @access public
     */
    function escape($text)
    {
        $connection = $this->getConnection();
        if (PEAR::isError($connection)) {
            return $connection;
        }
        return @mysqli_escape_string($connection, $text);
    }

    // }}}
    // {{{ quoteIdentifier()

    /**
     * Quote a string so it can be safely used as a table or column name
     *
     * Quoting style depends on which database driver is being used.
     *
     * MySQL can't handle the backtick character (<kbd>`</kbd>) in
     * table or column names.
     *
     * @param string $str  identifier name to be quoted
     * @param bool   $check_option  check the 'quote_identifier' option
     *
     * @return string  quoted identifier string
     *
     * @access public
     */
    function quoteIdentifier($str, $check_option = false)
    {
        if ($check_option && !$this->options['quote_identifier']) {
            return $str;
        }
        return '`' . $str . '`';
    }

    // }}}
    // {{{ beginTransaction()

    /**
     * Start a transaction.
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function beginTransaction()
    {
        $this->debug('starting transaction', 'beginTransaction', false);
        if (!$this->supports('transactions')) {
            return $this->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
                'beginTransaction: transactions are not in use');
        }
        if ($this->in_transaction) {
            return MDB2_OK;  //nothing to do
        }
        $result =& $this->_doQuery('SET AUTOCOMMIT = 0', true);
        if (PEAR::isError($result)) {
            return $result;
        }
        $this->in_transaction = true;
        return MDB2_OK;
    }

    // }}}
    // {{{ commit()

    /**
     * Commit the database changes done during a transaction that is in
     * progress.
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function commit()
    {
        $this->debug('commit transaction', 'commit', false);
        if (!$this->supports('transactions')) {
            return $this->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
                'commit: transactions are not in use');
        }
        if (!$this->in_transaction) {
            return $this->raiseError(MDB2_ERROR_INVALID, null, null,
                'commit: transaction changes are being auto committed');
        }
        $result =& $this->_doQuery('COMMIT', true);
        if (PEAR::isError($result)) {
            return $result;
        }
        $result =& $this->_doQuery('SET AUTOCOMMIT = 1', true);
        if (PEAR::isError($result)) {
            return $result;
        }
        $this->in_transaction = false;
        return MDB2_OK;
    }

    // }}}
    // {{{ rollback()

    /**
     * Cancel any database changes done during a transaction that is in
     * progress.
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function rollback()
    {
        $this->debug('rolling back transaction', 'rollback', false);
        if (!$this->supports('transactions')) {
            return $this->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
                'rollback: transactions are not in use');
        }
        if (!$this->in_transaction) {
            return $this->raiseError(MDB2_ERROR_INVALID, null, null,
                'rollback: transactions can not be rolled back when changes are auto committed');
        }
        $result =& $this->_doQuery('ROLLBACK', true);
        if (PEAR::isError($result)) {
            return $result;
        }
        $result =& $this->_doQuery('SET AUTOCOMMIT = 1', true);
        if (PEAR::isError($result)) {
            return $result;
        }
        $this->in_transaction = false;
        return MDB2_OK;
    }

    // }}}
    // {{{ connect()

    /**
     * Connect to the database
     *
     * @return true on success, MDB2 Error Object on failure
     */
    function connect()
    {
        if (is_object($this->connection)) {
            if (count(array_diff($this->connected_dsn, $this->dsn)) == 0) {
                return MDB2_OK;
            }
            $this->connection = 0;
        }

        if (!PEAR::loadExtension($this->phptype)) {
            return $this->raiseError(MDB2_ERROR_NOT_FOUND, null, null,
                'connect: extension '.$this->phptype.' is not compiled into PHP');
        }

        @ini_set('track_errors', true);
        $php_errormsg = '';

        if ($this->options['ssl']) {
            $init = @mysqli_init();
            @mysqli_ssl_set(
                $init,
                empty($this->dsn['key'])    ? null : $this->dsn['key'],
                empty($this->dsn['cert'])   ? null : $this->dsn['cert'],
                empty($this->dsn['ca'])     ? null : $this->dsn['ca'],
                empty($this->dsn['capath']) ? null : $this->dsn['capath'],
                empty($this->dsn['cipher']) ? null : $this->dsn['cipher']
            );
            if ($connection = @mysqli_real_connect(
                    $init,
                    $this->dsn['hostspec'],
                    $this->dsn['username'],
                    $this->dsn['password'],
                    $this->database_name,
                    $this->dsn['port'],
                    $this->dsn['socket']))
            {
                $connection = $init;
            }
        } else {
            $connection = @mysqli_connect(
                $this->dsn['hostspec'],
                $this->dsn['username'],
                $this->dsn['password'],
                $this->database_name,
                $this->dsn['port'],
                $this->dsn['socket']
            );
        }

        @ini_restore('track_errors');

        if (!$connection) {
            if (($err = @mysqli_connect_error()) != '') {
                return $this->raiseError(MDB2_ERROR_CONNECT_FAILED, null, null, $err);
            } else {
                return $this->raiseError(MDB2_ERROR_CONNECT_FAILED, null, null, $php_errormsg);
            }
        }

        if (isset($this->dsn['charset']) && !empty($this->dsn['charset'])
            && !@mysqli_query('SET character_set_client = '.$this->quote($this->dsn['charset'], 'text'), $connection)
        ) {
            return $this->raiseError(null, null, null,
                'Unable to set client charset: '.$this->dsn['charset']);
        }

        $this->connection = $connection;
        $this->connected_dsn = $this->dsn;
        $this->connected_database_name = $this->database_name;
        $this->dbsyntax = $this->dsn['dbsyntax'] ? $this->dsn['dbsyntax'] : $this->phptype;

        $this->supported['transactions'] = false;
        if ($this->options['default_table_type']) {
            switch (strtoupper($this->options['default_table_type'])) {
            case 'BERKELEYDB':
                $this->options['default_table_type'] = 'BDB';
            case 'BDB':
            case 'INNODB':
            case 'GEMINI':
                $this->supported['transactions'] = true;
                break;
            case 'HEAP':
            case 'ISAM':
            case 'MERGE':
            case 'MRG_MYISAM':
            case 'MYISAM':
                break;
            default:
                $this->warnings[] = $default_table_type.
                    ' is not a supported default table type';
            }
        }

        if ($this->options['use_transactions'] && !$this->supports('transactions')) {
            $this->warnings[] = $this->options['default_table_type'].
                ' is not a transaction-safe default table type; switched to INNODB';
            $this->options['default_table_type'] = 'INNODB';
            $this->supported['transactions'] = true;
        }

        $this->supported['sub_selects'] = 'emulated';
        $this->supported['prepared_statements'] = 'emulated';
        $server_info = $this->getServerVersion();
        if (is_array($server_info)
            && ($server_info['major'] > 4
                || ($server_info['major'] == 4 && $server_info['minor'] >= 1)
            )
        ) {
            $this->supported['sub_selects'] = true;
            $this->supported['prepared_statements'] = true;
        }
        return MDB2_OK;
    }

    // }}}
    // {{{ disconnect()

    /**
     * Log out and disconnect from the database.
     *
     * @param  boolean $force if the disconnect should be forced even if the
     *                        connection is opened persistently
     * @return mixed true on success, false if not connected and error
     *                object on error
     * @access public
     */
    function disconnect($force = true)
    {
        if (is_object($this->connection)) {
            if ($this->in_transaction) {
                $this->rollback();
            }
            if ($force) {
                @mysqli_close($this->connection);
            }
        }
        return parent::disconnect($force);
    }

    // }}}
    // {{{ _doQuery()

    /**
     * Execute a query
     * @param string $query  query
     * @param boolean $is_manip  if the query is a manipulation query
     * @param resource $connection
     * @param string $database_name
     * @return result or error object
     * @access protected
     */
    function &_doQuery($query, $is_manip = false, $connection = null, $database_name = null)
    {
        $this->last_query = $query;
        $this->debug($query, 'query', $is_manip);
        if ($this->options['disable_query']) {
            if ($is_manip) {
                return 0;
            }
            return null;
        }

        if (is_null($connection)) {
            $connection = $this->getConnection();
            if (PEAR::isError($connection)) {
                return $connection;
            }
        }
        if (is_null($database_name)) {
            $database_name = $this->database_name;
        }

        if ($database_name) {
            if ($database_name != $this->connected_database_name) {
                if (!@mysqli_select_db($connection, $database_name)) {
                    $err = $this->raiseError(null, null, null,
                        '_doQuery: Could not select the database: '.$database_name);
                    return $err;
                }
                $this->connected_database_name = $database_name;
            }
        }

        $function = $this->options['multi_query'] ? 'mysqli_multi_query' :
            ($this->options['result_buffering'] ? 'mysqli_query' : 'mysqli_unbuffered_query');
        $result = @$function($connection, $query);
        if (!$result) {
            $err = $this->raiseError(null, null, null,
                '_doQuery: Could not execute statement');
            return $err;
        }

        if ($this->options['multi_query']) {
            if ($this->options['result_buffering']) {
                if (!($result = @mysqli_store_result($connection))) {
                    $err = $this->raiseError(null, null, null,
                        '_doQuery: Could not get the first result from a multi query');
                    return $err;
                }
            } elseif (!($result = @mysqli_use_result($connection))) {
                $err = $this->raiseError(null, null, null,
                        '_doQuery: Could not get the first result from a multi query');
                return $err;
            }
        }

        return $result;
    }

    // }}}
    // {{{ _affectedRows()

    /**
     * Returns the number of rows affected
     *
     * @param resource $result
     * @param resource $connection
     * @return mixed MDB2 Error Object or the number of rows affected
     * @access private
     */
    function _affectedRows($connection, $result = null)
    {
        if (is_null($connection)) {
            $connection = $this->getConnection();
            if (PEAR::isError($connection)) {
                return $connection;
            }
        }
        return @mysqli_affected_rows($connection);
    }

    // }}}
    // {{{ _modifyQuery()

    /**
     * Changes a query string for various DBMS specific reasons
     *
     * @param string $query  query to modify
     * @param boolean $is_manip  if it is a DML query
     * @param integer $limit  limit the number of rows
     * @param integer $offset  start reading from given offset
     * @return string modified query
     * @access protected
     */
    function _modifyQuery($query, $is_manip, $limit, $offset)
    {
        if ($limit > 0
            && !preg_match('/LIMIT\s*\d(\s*(,|OFFSET)\s*\d+)?/i', $query)
        ) {
            $query = rtrim($query);
            if (substr($query, -1) == ';') {
                $query = substr($query, 0, -1);
            }
            if ($is_manip) {
                return $query . " LIMIT $limit";
            } else {
                return $query . " LIMIT $offset, $limit";
            }
        }
        return $query;
    }

    // }}}
    // {{{ getServerVersion()

    /**
     * return version information about the server
     *
     * @param string     $native  determines if the raw version string should be returned
     * @return mixed array/string with version information or MDB2 error object
     * @access public
     */
    function getServerVersion($native = false)
    {
        $connection = $this->getConnection();
        if (PEAR::isError($connection)) {
            return $connection;
        }
        if ($this->connected_server_info) {
            $server_info = $this->connected_server_info;
        } else {
            $server_info = @mysqli_get_server_info($connection);
        }
        if (!$server_info) {
            return $this->raiseError(null, null, null,
                'getServerVersion: Could not get server information');
        }
        // cache server_info
        $this->connected_server_info = $server_info;
        if (!$native) {
            $tmp = explode('.', $server_info, 3);
            if (isset($tmp[2]) && strpos($tmp[2], '-')) {
                $tmp2 = explode('-', @$tmp[2], 2);
            } else {
                $tmp2[0] = isset($tmp[2]) ? $tmp[2] : null;
                $tmp2[1] = null;
            }
            $server_info = array(
                'major' => isset($tmp[0]) ? $tmp[0] : null,
                'minor' => isset($tmp[1]) ? $tmp[1] : null,
                'patch' => $tmp2[0],
                'extra' => $tmp2[1],
                'native' => $server_info,
            );
        }
        return $server_info;
    }

    // }}}
    // {{{ prepare()

    /**
     * Prepares a query for multiple execution with execute().
     * With some database backends, this is emulated.
     * prepare() requires a generic query as string like
     * 'INSERT INTO numbers VALUES(?,?)' or
     * 'INSERT INTO numbers VALUES(:foo,:bar)'.
     * The ? and :[a-zA-Z] and  are placeholders which can be set using
     * bindParam() and the query can be send off using the execute() method.
     *
     * @param string $query the query to prepare
     * @param mixed   $types  array that contains the types of the placeholders
     * @param mixed   $result_types  array that contains the types of the columns in
     *                        the result set or MDB2_PREPARE_RESULT, if set to
     *                        MDB2_PREPARE_MANIP the query is handled as a manipulation query
     * @param mixed   $lobs   key (field) value (parameter) pair for all lob placeholders
     * @return mixed resource handle for the prepared query on success, a MDB2
     *        error on failure
     * @access public
     * @see bindParam, execute
     */
    function &prepare($query, $types = null, $result_types = null, $lobs = array())
    {
        if ($this->options['emulate_prepared']
            || $this->supported['prepared_statements'] !== true
        ) {
            $obj =& parent::prepare($query, $types, $result_types, $lobs);
            return $obj;
        }
        $is_manip = ($result_types === MDB2_PREPARE_MANIP);
        $offset = $this->offset;
        $limit = $this->limit;
        $this->offset = $this->limit = 0;
        $query = $this->_modifyQuery($query, $is_manip, $limit, $offset);
        $this->debug($query, 'prepare', $is_manip);
        $placeholder_type_guess = $placeholder_type = null;
        $question = '?';
        $colon = ':';
        $positions = array();
        $position = 0;
        while ($position < strlen($query)) {
            $q_position = strpos($query, $question, $position);
            $c_position = strpos($query, $colon, $position);
            if ($q_position && $c_position) {
                $p_position = min($q_position, $c_position);
            } elseif ($q_position) {
                $p_position = $q_position;
            } elseif ($c_position) {
                $p_position = $c_position;
            } else {
                break;
            }
            if (is_null($placeholder_type)) {
                $placeholder_type_guess = $query[$p_position];
            }
            if (is_int($quote = strpos($query, "'", $position)) && $quote < $p_position) {
                if (!is_int($end_quote = strpos($query, "'", $quote + 1))) {
                    $err =& $this->raiseError(MDB2_ERROR_SYNTAX, null, null,
                        'prepare: query with an unterminated text string specified');
                    return $err;
                }
                switch ($this->escape_quotes) {
                case '':
                case "'":
                    $position = $end_quote + 1;
                    break;
                default:
                    if ($end_quote == $quote + 1) {
                        $position = $end_quote + 1;
                    } else {
                        if ($query[$end_quote-1] == $this->escape_quotes) {
                            $position = $end_quote;
                        } else {
                            $position = $end_quote + 1;
                        }
                    }
                    break;
                }
            } elseif ($query[$position] == $placeholder_type_guess) {
                if (is_null($placeholder_type)) {
                    $placeholder_type = $query[$p_position];
                    $question = $colon = $placeholder_type;
                }
                if ($placeholder_type == ':') {
                    $parameter = preg_replace('/^.{'.($position+1).'}([a-z0-9_]+).*$/si', '\\1', $query);
                    if ($parameter === '') {
                        $err =& $this->raiseError(MDB2_ERROR_SYNTAX, null, null,
                            'prepare: named parameter with an empty name');
                        return $err;
                    }
                    $positions[$parameter] = $p_position;
                    $query = substr_replace($query, '?', $position, strlen($parameter)+1);
                } else {
                    $positions[] = $p_position;
                }
                $position = $p_position + 1;
            } else {
                $position = $p_position;
            }
        }
        $connection = $this->getConnection();
        if (PEAR::isError($connection)) {
            return $connection;
        }

        if (!$is_manip) {
            $statement_name = 'MDB2_Statement_'.$this->phptype.md5(time() + rand());
            $query = "PREPARE $statement_name FROM '$query'";

            $statement =& $this->_doQuery($query, true, $connection);
            if (PEAR::isError($statement)) {
                return $statement;
            }
            $statement = $statement_name;
        } else {
            $statement = @mysqli_prepare($connection, $query);
            if (!$statement) {
                $err =& $this->raiseError(null, null, null,
                    'prepare: Unable to create prepared statement handle');
                return $err;
            }
        }

        $class_name = 'MDB2_Statement_'.$this->phptype;
        $obj =& new $class_name($this, $statement, $positions, $query, $types, $result_types, $is_manip, $limit, $offset);
        return $obj;
    }

    // }}}
    // {{{ replace()

    /**
     * Execute a SQL REPLACE query. A REPLACE query is identical to a INSERT
     * query, except that if there is already a row in the table with the same
     * key field values, the REPLACE query just updates its values instead of
     * inserting a new row.
     *
     * The REPLACE type of query does not make part of the SQL standards. Since
     * practically only MySQL implements it natively, this type of query is
     * emulated through this method for other DBMS using standard types of
     * queries inside a transaction to assure the atomicity of the operation.
     *
     * @access public
     *
     * @param string $table name of the table on which the REPLACE query will
     *  be executed.
     * @param array $fields associative array that describes the fields and the
     *  values that will be inserted or updated in the specified table. The
     *  indexes of the array are the names of all the fields of the table. The
     *  values of the array are also associative arrays that describe the
     *  values and other properties of the table fields.
     *
     *  Here follows a list of field properties that need to be specified:
     *
     *    value:
     *          Value to be assigned to the specified field. This value may be
     *          of specified in database independent type format as this
     *          function can perform the necessary datatype conversions.
     *
     *    Default:
     *          this property is required unless the Null property
     *          is set to 1.
     *
     *    type
     *          Name of the type of the field. Currently, all types Metabase
     *          are supported except for clob and blob.
     *
     *    Default: no type conversion
     *
     *    null
     *          Boolean property that indicates that the value for this field
     *          should be set to null.
     *
     *          The default value for fields missing in INSERT queries may be
     *          specified the definition of a table. Often, the default value
     *          is already null, but since the REPLACE may be emulated using
     *          an UPDATE query, make sure that all fields of the table are
     *          listed in this function argument array.
     *
     *    Default: 0
     *
     *    key
     *          Boolean property that indicates that this field should be
     *          handled as a primary key or at least as part of the compound
     *          unique index of the table that will determine the row that will
     *          updated if it exists or inserted a new row otherwise.
     *
     *          This function will fail if no key field is specified or if the
     *          value of a key field is set to null because fields that are
     *          part of unique index they may not be null.
     *
     *    Default: 0
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    function replace($table, $fields)
    {
        $count = count($fields);
        $query = $values = '';
        $keys = $colnum = 0;
        for (reset($fields); $colnum < $count; next($fields), $colnum++) {
            $name = key($fields);
            if ($colnum > 0) {
                $query .= ',';
                $values.= ',';
            }
            $query.= $name;
            if (isset($fields[$name]['null']) && $fields[$name]['null']) {
                $value = 'NULL';
            } else {
                $value = $this->quote($fields[$name]['value'], $fields[$name]['type']);
            }
            $values.= $value;
            if (isset($fields[$name]['key']) && $fields[$name]['key']) {
                if ($value === 'NULL') {
                    return $this->raiseError(MDB2_ERROR_CANNOT_REPLACE, null, null,
                        'replace: key value '.$name.' may not be NULL');
                }
                $keys++;
            }
        }
        if ($keys == 0) {
            return $this->raiseError(MDB2_ERROR_CANNOT_REPLACE, null, null,
                'replace: not specified which fields are keys');
        }

        $connection = $this->getConnection();
        if (PEAR::isError($connection)) {
            return $connection;
        }

        $query = "REPLACE INTO $table ($query) VALUES ($values)";
        $this->last_query = $query;
        $this->debug($query, 'query', true);
        $result =& $this->_doQuery($query, true, $connection);
        if (PEAR::isError($result)) {
            return $result;
        }
        return $this->_affectedRows($connection, $result);
    }

    // }}}
    // {{{ nextID()

    /**
     * Returns the next free id of a sequence
     *
     * @param string $seq_name name of the sequence
     * @param boolean $ondemand when true the sequence is
     *                          automatic created, if it
     *                          not exists
     *
     * @return mixed MDB2 Error Object or id
     * @access public
     */
    function nextID($seq_name, $ondemand = true)
    {
        $sequence_name = $this->quoteIdentifier($this->getSequenceName($seq_name), true);
        $seqcol_name = $this->quoteIdentifier($this->options['seqcol_name'], true);
        $query = "INSERT INTO $sequence_name ($seqcol_name) VALUES (NULL)";
        $this->expectError(MDB2_ERROR_NOSUCHTABLE);
        $result =& $this->_doQuery($query, true);
        $this->popExpect();
        if (PEAR::isError($result)) {
            if ($ondemand && $result->getCode() == MDB2_ERROR_NOSUCHTABLE) {
                $this->loadModule('Manager', null, true);
                // Since we are creating the sequence on demand
                // we know the first id = 1 so initialize the
                // sequence at 2
                $result = $this->manager->createSequence($seq_name, 2);
                if (PEAR::isError($result)) {
                    return $this->raiseError($result, null, null,
                        'nextID: on demand sequence '.$seq_name.' could not be created');
                } else {
                    // First ID of a newly created sequence is 1
                    return 1;
                }
            }
            return $result;
        }
        $value = $this->lastInsertID();
        if (is_numeric($value)) {
            $query = "DELETE FROM $sequence_name WHERE $seqcol_name < $value";
            $result =& $this->_doQuery($query, true);
            if (PEAR::isError($result)) {
                $this->warnings[] = 'nextID: could not delete previous sequence table values from '.$seq_name;
            }
        }
        return $value;
    }

    // }}}
    // {{{ lastInsertID()

    /**
     * Returns the autoincrement ID if supported or $id or fetches the current
     * ID in a sequence called: $table.(empty($field) ? '' : '_'.$field)
     *
     * @param string $table name of the table into which a new row was inserted
     * @param string $field name of the field into which a new row was inserted
     * @return mixed MDB2 Error Object or id
     * @access public
     */
    function lastInsertID($table = null, $field = null)
    {
        $connection = $this->getConnection();
        if (PEAR::isError($connection)) {
            return $connection;
        }
        $value = @mysqli_insert_id($connection);
        if (!$value) {
            return $this->raiseError(null, null, null,
                'lastInsertID: Could not get last insert ID');
        }
        return $value;
    }

    // }}}
    // {{{ currID()

    /**
     * Returns the current id of a sequence
     *
     * @param string $seq_name name of the sequence
     * @return mixed MDB2 Error Object or id
     * @access public
     */
    function currID($seq_name)
    {
        $sequence_name = $this->quoteIdentifier($this->getSequenceName($seq_name), true);
        $seqcol_name = $this->quoteIdentifier($this->options['seqcol_name'], true);
        $query = "SELECT MAX($seqcol_name) FROM $sequence_name";
        return $this->queryOne($query, 'integer');
    }
}

/**
 * MDB2 MySQLi result driver
 *
 * @package MDB2
 * @category Database
 * @author  Lukas Smith <smith@pooteeweet.org>
 */
class MDB2_Result_mysqli extends MDB2_Result_Common
{
    // }}}
    // {{{ fetchRow()

    /**
     * Fetch a row and insert the data into an existing array.
     *
     * @param int       $fetchmode  how the array data should be indexed
     * @param int    $rownum    number of the row where the data can be found
     * @return int data array on success, a MDB2 error on failure
     * @access public
     */
    function &fetchRow($fetchmode = MDB2_FETCHMODE_DEFAULT, $rownum = null)
    {
        if (!is_null($rownum)) {
            $seek = $this->seek($rownum);
            if (PEAR::isError($seek)) {
                return $seek;
            }
        }
        if ($fetchmode == MDB2_FETCHMODE_DEFAULT) {
            $fetchmode = $this->db->fetchmode;
        }
        if ($fetchmode & MDB2_FETCHMODE_ASSOC) {
            $row = @mysqli_fetch_assoc($this->result);
            if (is_array($row)
                && $this->db->options['portability'] & MDB2_PORTABILITY_FIX_CASE
            ) {
                $row = array_change_key_case($row, $this->db->options['field_case']);
            }
        } else {
           $row = @mysqli_fetch_row($this->result);
        }

        if (!$row) {
            if ($this->result === false) {
                $err =& $this->db->raiseError(MDB2_ERROR_NEED_MORE_DATA, null, null,
                    'fetchRow: resultset has already been freed');
                return $err;
            }
            $null = null;
            return $null;
        }
        if ($this->db->options['portability'] & MDB2_PORTABILITY_EMPTY_TO_NULL) {
            $this->db->_fixResultArrayValues($row, MDB2_PORTABILITY_EMPTY_TO_NULL);
        }
        if (!empty($this->values)) {
            $this->_assignBindColumns($row);
        }
        if (!empty($this->types)) {
            $row = $this->db->datatype->convertResultRow($this->types, $row);
        }
        if ($fetchmode === MDB2_FETCHMODE_OBJECT) {
            $object_class = $this->db->options['fetch_class'];
            if ($object_class == 'stdClass') {
                $row = (object) $row;
            } else {
                $row = &new $object_class($row);
            }
        }
        ++$this->rownum;
        return $row;
    }

    // }}}
    // {{{ _getColumnNames()

    /**
     * Retrieve the names of columns returned by the DBMS in a query result.
     *
     * @return mixed                an associative array variable
     *                              that will hold the names of columns. The
     *                              indexes of the array are the column names
     *                              mapped to lower case and the values are the
     *                              respective numbers of the columns starting
     *                              from 0. Some DBMS may not return any
     *                              columns when the result set does not
     *                              contain any rows.
     *
     *                              a MDB2 error on failure
     * @access private
     */
    function _getColumnNames()
    {
        $columns = array();
        $numcols = $this->numCols();
        if (PEAR::isError($numcols)) {
            return $numcols;
        }
        for ($column = 0; $column < $numcols; $column++) {
            $column_info = @mysqli_fetch_field_direct($this->result, $column);
            $columns[$column_info->name] = $column;
        }
        if ($this->db->options['portability'] & MDB2_PORTABILITY_FIX_CASE) {
            $columns = array_change_key_case($columns, $this->db->options['field_case']);
        }
        return $columns;
    }

    // }}}
    // {{{ numCols()

    /**
     * Count the number of columns returned by the DBMS in a query result.
     *
     * @return mixed integer value with the number of columns, a MDB2 error
     *                       on failure
     * @access public
     */
    function numCols()
    {
        $cols = @mysqli_num_fields($this->result);
        if (is_null($cols)) {
            if ($this->result === false) {
                return $this->db->raiseError(MDB2_ERROR_NEED_MORE_DATA, null, null,
                    'numCols: resultset has already been freed');
            } elseif (is_null($this->result)) {
                return count($this->types);
            }
            return $this->db->raiseError(null, null, null,
                'numCols: Could not get column count');
        }
        return $cols;
    }

    // }}}
    // {{{ nextResult()

    /**
     * Move the internal result pointer to the next available result
     *
     * @param a valid result resource
     * @return true on success, false if there is no more result set or an error object on failure
     * @access public
     */
    function nextResult()
    {
        $connection = $this->db->getConnection();
        if (PEAR::isError($connection)) {
            return $connection;
        }

        if (!@mysqli_more_results($connection)) {
            return false;
        }
        if (!@mysqli_next_result($connection)) {
            return false;
        }
        if (!($this->result = @mysqli_use_result($connection))) {
            return false;
        }
        return MDB2_OK;
    }

    // }}}
    // {{{ free()

    /**
     * Free the internal resources associated with result.
     *
     * @return boolean true on success, false if result is invalid
     * @access public
     */
    function free()
    {
        $free = @mysqli_free_result($this->result);
        if (!$free) {
            if (is_null($free) || !$this->result) {
                return MDB2_OK;
            }
            return $this->db->raiseError(null, null, null,
                'free: Could not free result');
        }
        $this->result = false;
        return MDB2_OK;
    }
}

/**
 * MDB2 MySQLi buffered result driver
 *
 * @package MDB2
 * @category Database
 * @author  Lukas Smith <smith@pooteeweet.org>
 */
class MDB2_BufferedResult_mysqli extends MDB2_Result_mysqli
{
    // }}}
    // {{{ seek()

    /**
     * Seek to a specific row in a result set
     *
     * @param int    $rownum    number of the row where the data can be found
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function seek($rownum = 0)
    {
        if ($this->rownum != ($rownum - 1) && !@mysqli_data_seek($this->result, $rownum)) {
            if ($this->result === false) {
                return $this->db->raiseError(MDB2_ERROR_NEED_MORE_DATA, null, null,
                    'seek: resultset has already been freed');
            } elseif (is_null($this->result)) {
                return MDB2_OK;
            }
            return $this->db->raiseError(MDB2_ERROR_INVALID, null, null,
                'seek: tried to seek to an invalid row number ('.$rownum.')');
        }
        $this->rownum = $rownum - 1;
        return MDB2_OK;
    }

    // }}}
    // {{{ valid()

    /**
     * Check if the end of the result set has been reached
     *
     * @return mixed true or false on sucess, a MDB2 error on failure
     * @access public
     */
    function valid()
    {
        $numrows = $this->numRows();
        if (PEAR::isError($numrows)) {
            return $numrows;
        }
        return $this->rownum < ($numrows - 1);
    }

    // }}}
    // {{{ numRows()

    /**
     * Returns the number of rows in a result object
     *
     * @return mixed MDB2 Error Object or the number of rows
     * @access public
     */
    function numRows()
    {
        $rows = @mysqli_num_rows($this->result);
        if (is_null($rows)) {
            if ($this->result === false) {
                return $this->db->raiseError(MDB2_ERROR_NEED_MORE_DATA, null, null,
                    'numRows: resultset has already been freed');
            } elseif (is_null($this->result)) {
                return 0;
            }
            return $this->db->raiseError(null, null, null,
                'numRows: Could not get row count');
        }
        return $rows;
    }

    // }}}
    // {{{ nextResult()

    /**
     * Move the internal result pointer to the next available result
     *
     * @param a valid result resource
     * @return true on success, false if there is no more result set or an error object on failure
     * @access public
     */
    function nextResult()
    {
        $connection = $this->db->getConnection();
        if (PEAR::isError($connection)) {
            return $connection;
        }

        if (!@mysqli_more_results($connection)) {
            return false;
        }
        if (!@mysqli_next_result($connection)) {
            return false;
        }
        if (!($this->result = @mysqli_store_result($connection))) {
            return false;
        }
        return MDB2_OK;
    }
}

/**
 * MDB2 MySQLi statement driver
 *
 * @package MDB2
 * @category Database
 * @author  Lukas Smith <smith@pooteeweet.org>
 */
class MDB2_Statement_mysqli extends MDB2_Statement_Common
{
    // {{{ _execute()

    /**
     * Execute a prepared query statement helper method.
     *
     * @param mixed $result_class string which specifies which result class to use
     * @param mixed $result_wrap_class string which specifies which class to wrap results in
     * @return mixed a result handle or MDB2_OK on success, a MDB2 error on failure
     * @access private
     */
    function &_execute($result_class = true, $result_wrap_class = false)
    {
        if (is_null($this->statement)) {
            $result =& parent::_execute($result_class, $result_wrap_class);
            return $result;
        }
        $this->db->last_query = $this->query;
        $this->db->debug($this->query, 'execute', $this->is_manip);
        $this->db->debug($this->values, 'parameters', $this->is_manip);
        if ($this->db->getOption('disable_query')) {
            if ($this->is_manip) {
                $return = 0;
                return $return;
            }
            $null = null;
            return $null;
        }

        $connection = $this->db->getConnection();
        if (PEAR::isError($connection)) {
            return $connection;
        }

        if (!is_object($this->statement)) {
            $query = 'EXECUTE '.$this->statement;
        }
        if (!empty($this->positions)) {
            $parameters = array(0 => $this->statement, 1 => '');
            $lobs = array();
            $i = 0;
            foreach ($this->positions as $parameter => $foo) {
                if (!array_key_exists($parameter, $this->values)) {
                    return $this->db->raiseError(MDB2_ERROR_NOT_FOUND, null, null,
                        '_execute: Unable to bind to missing placeholder: '.$parameter);
                }
                $value = $this->values[$parameter];
                $type = array_key_exists($parameter, $this->types) ? $this->types[$parameter] : null;
                if (!is_object($this->statement)) {
                    if (is_resource($value) || $type == 'clob' || $type == 'blob') {
                        if (!is_resource($value) && preg_match('/^(\w+:\/\/)(.*)$/', $value, $match)) {
                            if ($match[1] == 'file://') {
                                $value = $match[2];
                            }
                            $value = @fopen($value, 'r');
                            $close = true;
                        }
                        if (is_resource($value)) {
                            $data = '';
                            while (!@feof($value)) {
                                $data.= @fread($value, $this->db->options['lob_buffer_length']);
                            }
                            if ($close) {
                                @fclose($value);
                            }
                            $value = $data;
                        }
                    }
                    $param_query = 'SET @'.$parameter.' = '.$this->db->quote($value, $type);
                    $result = $this->db->_doQuery($param_query, true, $connection);
                    if (PEAR::isError($result)) {
                        return $result;
                    }
                } else {
                    if (is_resource($value) || $type == 'clob' || $type == 'blob') {
                        $parameters[] = null;
                        $parameters[1].= 'b';
                        $lobs[$i] = $parameter;
                    } else {
                        $parameters[] = $this->db->quote($value, $type, false);
                        $parameters[1].= $this->db->datatype->mapPrepareDatatype($type);
                    }
                    ++$i;
                }
            }

            if (!is_object($this->statement)) {
                $query.= ' USING @'.implode(', @', array_keys($this->positions));
            } else {
                $result = @call_user_func_array('mysqli_stmt_bind_param', $parameters);
                if ($result === false) {
                    $err =& $this->db->raiseError(null, null, null,
                        '_execute: Unable to bind parameters');
                    return $err;
                }

                foreach ($lobs as $i => $parameter) {
                    $value = $this->values[$parameter];
                    $close = false;
                    if (!is_resource($value)) {
                        $close = true;
                        if (preg_match('/^(\w+:\/\/)(.*)$/', $value, $match)) {
                            if ($match[1] == 'file://') {
                                $value = $match[2];
                            }
                            $value = @fopen($value, 'r');
                        } else {
                            $fp = @tmpfile();
                            @fwrite($fp, $value);
                            @rewind($fp);
                            $value = $fp;
                        }
                    }
                    while (!@feof($value)) {
                        $data = @fread($value, $this->db->options['lob_buffer_length']);
                        @mysqli_stmt_send_long_data($this->statement, $i, $data);
                    }
                    if ($close) {
                        @fclose($value);
                    }
                }
            }
        }

        if (!is_object($this->statement)) {
            $result = $this->db->_doQuery($query, $this->is_manip, $connection);
            if (PEAR::isError($result)) {
                return $result;
            }

            if ($this->is_manip) {
                $affected_rows = $this->db->_affectedRows($connection, $result);
                return $affected_rows;
            }

            $result =& $this->db->_wrapResult($result, $this->result_types,
                $result_class, $result_wrap_class);
        } else {
            if (!@mysqli_stmt_execute($this->statement)) {
                $err =& $this->db->raiseError(null, null, null,
                    '_execute: Unable to execute statement');
                return $err;
            }

            if ($this->is_manip) {
                $affected_rows = @mysqli_stmt_affected_rows($this->statement);
                return $affected_rows;
            }

            if ($this->db->options['result_buffering']) {
                @mysqli_stmt_store_result($this->statement);
            }

            $result =& $this->db->_wrapResult($this->statement, $this->result_types,
                $result_class, $result_wrap_class);
        }

        return $result;
    }

    // }}}
    // {{{ free()

    /**
     * Release resources allocated for the specified prepared query.
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function free()
    {
        if (is_null($this->statement)) {
            return parent::free();
        }

        if (!is_object($this->statement)) {
            $connection = $this->db->getConnection();
            if (PEAR::isError($connection)) {
                return $connection;
            }

            $query = 'DEALLOCATE PREPARE '.$this->statement;
            return $this->db->_doQuery($query, true, $connection);
        }

        if (!@mysqli_stmt_close($this->statement)) {
            return $this->db->raiseError(null, null, null,
                'free: Could not free statement');
        }
        return MDB2_OK;
    }
}
?>