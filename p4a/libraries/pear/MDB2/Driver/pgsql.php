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
// | Author: Paul Cooper <pgc@ucecom.com>                                 |
// +----------------------------------------------------------------------+
//
// $Id$

/**
 * MDB2 PostGreSQL driver
 *
 * @package MDB2
 * @category Database
 * @author  Paul Cooper <pgc@ucecom.com>
 */
class MDB2_Driver_pgsql extends MDB2_Driver_Common
{
    // {{{ properties
    var $escape_quotes = "'";

    var $escape_identifier = '"';

    // }}}
    // {{{ constructor

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        $this->phptype = 'pgsql';
        $this->dbsyntax = 'pgsql';

        $this->supported['sequences'] = true;
        $this->supported['indexes'] = true;
        $this->supported['affected_rows'] = true;
        $this->supported['summary_functions'] = true;
        $this->supported['order_by_text'] = true;
        $this->supported['transactions'] = true;
        $this->supported['current_id'] = true;
        $this->supported['limit_queries'] = true;
        $this->supported['LOBs'] = true;
        $this->supported['replace'] = 'emulated';
        $this->supported['sub_selects'] = true;
        $this->supported['auto_increment'] = 'emulated';
        $this->supported['primary_key'] = true;
        $this->supported['result_introspection'] = true;
        $this->supported['prepared_statements'] = true;

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
        // Fall back to MDB2_ERROR if there was no mapping.
        $error_code = MDB2_ERROR;

        $native_msg = '';
        if (is_resource($error)) {
            $native_msg = @pg_result_error($error);
        } elseif ($this->connection) {
            $native_msg = @pg_last_error($this->connection);
            if (!$native_msg && @pg_connection_status($this->connection) === PGSQL_CONNECTION_BAD) {
                $native_msg = 'Database connection has been lost.';
                $error_code = MDB2_ERROR_CONNECT_FAILED;
            }
        }

        static $error_regexps;
        if (empty($error_regexps)) {
            $error_regexps = array(
                '/column .* (of relation .*)?does not exist/i'
                    => MDB2_ERROR_NOSUCHFIELD,
                '/(relation|sequence|table).*does not exist|class .* not found/i'
                    => MDB2_ERROR_NOSUCHTABLE,
                '/index .* does not exist/'
                    => MDB2_ERROR_NOT_FOUND,
                '/relation .* already exists/i'
                    => MDB2_ERROR_ALREADY_EXISTS,
                '/(divide|division) by zero$/i'
                    => MDB2_ERROR_DIVZERO,
                '/pg_atoi: error in .*: can\'t parse /i'
                    => MDB2_ERROR_INVALID_NUMBER,
                '/invalid input syntax for( type)? (integer|numeric)/i'
                    => MDB2_ERROR_INVALID_NUMBER,
                '/value .* is out of range for type \w*int/i'
                    => MDB2_ERROR_INVALID_NUMBER,
                '/integer out of range/i'
                    => MDB2_ERROR_INVALID_NUMBER,
                '/value too long for type character/i'
                    => MDB2_ERROR_INVALID,
                '/attribute .* not found|relation .* does not have attribute/i'
                    => MDB2_ERROR_NOSUCHFIELD,
                '/column .* specified in USING clause does not exist in (left|right) table/i'
                    => MDB2_ERROR_NOSUCHFIELD,
                '/parser: parse error at or near/i'
                    => MDB2_ERROR_SYNTAX,
                '/syntax error at/'
                    => MDB2_ERROR_SYNTAX,
                '/column reference .* is ambiguous/i'
                    => MDB2_ERROR_SYNTAX,
                '/permission denied/'
                    => MDB2_ERROR_ACCESS_VIOLATION,
                '/violates not-null constraint/'
                    => MDB2_ERROR_CONSTRAINT_NOT_NULL,
                '/violates [\w ]+ constraint/'
                    => MDB2_ERROR_CONSTRAINT,
                '/referential integrity violation/'
                    => MDB2_ERROR_CONSTRAINT,
                '/more expressions than target columns/i'
                    => MDB2_ERROR_VALUE_COUNT_ON_ROW,
            );
        }
        if (is_numeric($error) && $error < 0) {
            $error_code = $error;
        } else {
            foreach ($error_regexps as $regexp => $code) {
                if (preg_match($regexp, $native_msg)) {
                    $error_code = $code;
                    break;
                }
            }
        }
        return array($error_code, null, $native_msg);
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
        return @pg_escape_string($text);
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
        if ($this->in_transaction) {
            return MDB2_OK;  //nothing to do
        }
        if (!$this->destructor_registered && $this->opened_persistent) {
            $this->destructor_registered = true;
            register_shutdown_function('MDB2_closeOpenTransactions');
        }
        $result =& $this->_doQuery('BEGIN', true);
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
        if (!$this->in_transaction) {
            return $this->raiseError(MDB2_ERROR_INVALID, null, null,
                'commit: transaction changes are being auto committed');
        }
        $result =& $this->_doQuery('COMMIT', true);
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
        if (!$this->in_transaction) {
            return $this->raiseError(MDB2_ERROR_INVALID, null, null,
                'rollback: transactions can not be rolled back when changes are auto committed');
        }
        $result =& $this->_doQuery('ROLLBACK', true);
        if (PEAR::isError($result)) {
            return $result;
        }
        $this->in_transaction = false;
        return MDB2_OK;
    }

    // }}}
    // {{{ _doConnect()

    /**
     * Does the grunt work of connecting to the database
     *
     * @return mixed connection resource on success, MDB2 Error Object on failure
     * @access protected
     **/
    function _doConnect($database_name, $persistent = false)
    {
        if ($database_name == '') {
            $database_name = 'template1';
        }

        $protocol = $this->dsn['protocol'] ? $this->dsn['protocol'] : 'tcp';

        $params = array('');
        if ($protocol == 'tcp') {
            if ($this->dsn['hostspec']) {
                $params[0].= 'host=' . $this->dsn['hostspec'];
            }
            if ($this->dsn['port']) {
                $params[0].= ' port=' . $this->dsn['port'];
            }
        } elseif ($protocol == 'unix') {
            // Allow for pg socket in non-standard locations.
            if ($this->dsn['socket']) {
                $params[0].= 'host=' . $this->dsn['socket'];
            }
            if ($this->dsn['port']) {
                $params[0].= ' port=' . $this->dsn['port'];
            }
        }
        if ($database_name) {
            $params[0].= ' dbname=\'' . addslashes($database_name) . '\'';
        }
        if ($this->dsn['username']) {
            $params[0].= ' user=\'' . addslashes($this->dsn['username']) . '\'';
        }
        if ($this->dsn['password']) {
            $params[0].= ' password=\'' . addslashes($this->dsn['password']) . '\'';
        }
        if (!empty($this->dsn['options'])) {
            $params[0].= ' options=' . $this->dsn['options'];
        }
        if (!empty($this->dsn['tty'])) {
            $params[0].= ' tty=' . $this->dsn['tty'];
        }
        if (!empty($this->dsn['connect_timeout'])) {
            $params[0].= ' connect_timeout=' . $this->dsn['connect_timeout'];
        }
        if (!empty($this->dsn['sslmode'])) {
            $params[0].= ' sslmode=' . $this->dsn['sslmode'];
        }
        if (!empty($this->dsn['service'])) {
            $params[0].= ' service=' . $this->dsn['service'];
        }

        if (!empty($this->dsn['new_link'])
            && ($this->dsn['new_link'] == 'true' || $this->dsn['new_link'] === true))
        {
            if (version_compare(phpversion(), '4.3.0', '>=')) {
                $params[] = PGSQL_CONNECT_FORCE_NEW;
            }
        }

        $connect_function = $persistent ? 'pg_pconnect' : 'pg_connect';

        @ini_set('track_errors', true);
        $php_errormsg = '';
        $connection = @call_user_func_array($connect_function, $params);
        @ini_restore('track_errors');
        if (!$connection) {
            return $this->raiseError(MDB2_ERROR_CONNECT_FAILED,
                null, null, strip_tags($php_errormsg));
        }

        if (!@pg_query($connection, "SET SESSION DATESTYLE = 'ISO'")) {
            return $this->raiseError(null, null, null,
                'Unable to set connection charset: '.$this->dsn['charset']);
        }

        if (!empty($this->dsn['charset'])) {
            $result = $this->setCharset($this->dsn['charset'], $connection);
            if (PEAR::isError($result)) {
                return $result;
            }
        }

        return $connection;
    }

    // }}}
    // {{{ connect()

    /**
     * Connect to the database
     *
     * @return true on success, MDB2 Error Object on failure
     * @access public
     **/
    function connect()
    {
        if (is_resource($this->connection)) {
            if (count(array_diff($this->connected_dsn, $this->dsn)) == 0
                && $this->connected_database_name == $this->database_name
                && ($this->opened_persistent == $this->options['persistent'])
            ) {
                return MDB2_OK;
            }
            $this->disconnect(false);
        }

        if (!PEAR::loadExtension($this->phptype)) {
            return $this->raiseError(MDB2_ERROR_NOT_FOUND, null, null,
                'connect: extension '.$this->phptype.' is not compiled into PHP');
        }

        if ($this->database_name) {
            $connection = $this->_doConnect($this->database_name, $this->options['persistent']);
            if (PEAR::isError($connection)) {
                return $connection;
            }
            $this->connection = $connection;
            $this->connected_dsn = $this->dsn;
            $this->connected_database_name = $this->database_name;
            $this->opened_persistent = $this->options['persistent'];
            $this->dbsyntax = $this->dsn['dbsyntax'] ? $this->dsn['dbsyntax'] : $this->phptype;
        }
        return MDB2_OK;
    }

    // }}}
    // {{{ setCharset()

    /**
     * Set the charset on the current connection
     *
     * @param string    charset
     * @param resource  connection handle
     *
     * @return true on success, MDB2 Error Object on failure
     */
    function setCharset($charset, $connection = null)
    {
        if (is_null($connection)) {
            $connection = $this->getConnection();
            if (PEAR::isError($connection)) {
                return $connection;
            }
        }

        $result = @pg_set_client_encoding($connection, $charset);

        if (!$result) {
            return $this->raiseError(null, null, null,
                'setCharset: Unable to set client charset: '.$charset);
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
        if (is_resource($this->connection)) {
            if ($this->in_transaction) {
                $this->rollback();
            }
            if (!$this->opened_persistent || $force) {
                @pg_close($this->connection);
            }
        }
        return parent::disconnect($force);
    }

    // }}}
    // {{{ standaloneQuery()

   /**
     * execute a query as DBA
     *
     * @param string $query the SQL query
     * @param mixed   $types  array that contains the types of the columns in
     *                        the result set
     * @param boolean $is_manip  if the query is a manipulation query
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function &standaloneQuery($query, $types = null, $is_manip = false)
    {
        $connection = $this->_doConnect('template1', false);
        if (PEAR::isError($connection)) {
            $err =& $this->raiseError(MDB2_ERROR_CONNECT_FAILED, null, null,
                'Cannot connect to template1');
            return $err;
        }

        $offset = $this->offset;
        $limit = $this->limit;
        $this->offset = $this->limit = 0;
        $query = $this->_modifyQuery($query, $is_manip, $limit, $offset);

        $result =& $this->_doQuery($query, $is_manip, $connection, false);
        @pg_close($connection);
        if (PEAR::isError($result)) {
            return $result;
        }

        if ($is_manip) {
            $affected_rows =  $this->_affectedRows($connection, $result);
            return $affected_rows;
        }
        $result =& $this->_wrapResult($result, $types, true, false, $limit, $offset);
        return $result;
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
        $result = $this->debug($query, 'query', $is_manip);
        if ($result) {
            if (PEAR::isError($result)) {
                return $result;
            }
            $query = $result;
        }
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

        $function = $this->options['multi_query'] ? 'pg_send_query' : 'pg_query';
        $result = @$function($connection, $query);
        if (!$result) {
            $err =& $this->raiseError(null, null, null,
                '_doQuery: Could not execute statement');
            return $err;
        }

        if ($this->options['multi_query']) {
            if (!($result = @pg_get_result($connection))) {
                $err =& $this->raiseError(null, null, null,
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
        return @pg_affected_rows($result);
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
                $manip = preg_replace('/^(DELETE FROM|UPDATE).*$/', '\\1', $query);
                $from = $match[2];
                $where = $match[3];
                $query = $manip.' '.$from.' WHERE ctid=(SELECT ctid FROM '.$from.' '.$where.' LIMIT '.$limit.')';
            } else {
                $query.= " LIMIT $limit OFFSET $offset";
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
        $query = 'SHOW SERVER_VERSION';
        if ($this->connected_server_info) {
            $server_info = $this->connected_server_info;
        } else {
            $server_info = $this->queryOne($query, 'text');
            if (PEAR::isError($server_info)) {
                return $server_info;
            }
        }
        // cache server_info
        $this->connected_server_info = $server_info;
        if (!$native && !PEAR::isError($server_info)) {
            $tmp = explode('.', $server_info, 3);
            if (empty($tmp[2])
                && isset($tmp[1])
                && preg_match('/(\d+)(.*)/', $tmp[1], $tmp2)
            ) {
                $server_info = array(
                    'major' => $tmp[0],
                    'minor' => $tmp2[1],
                    'patch' => null,
                    'extra' => $tmp2[2],
                    'native' => $server_info,
                );
            } else {
                $server_info = array(
                    'major' => isset($tmp[0]) ? $tmp[0] : null,
                    'minor' => isset($tmp[1]) ? $tmp[1] : null,
                    'patch' => isset($tmp[2]) ? $tmp[2] : null,
                    'extra' => null,
                    'native' => $server_info,
                );
            }
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
        if ($this->options['emulate_prepared']) {
            $obj =& parent::prepare($query, $types, $result_types, $lobs);
            return $obj;
        }
        $is_manip = ($result_types === MDB2_PREPARE_MANIP);
        $offset = $this->offset;
        $limit = $this->limit;
        $this->offset = $this->limit = 0;
        $result = $this->debug($query, 'prepare', $is_manip);
        if ($result) {
            if (PEAR::isError($result)) {
                return $result;
            }
            $query = $result;
        }
        if (!empty($types)) {
            $this->loadModule('Datatype', null, true);
        }
        $query = $this->_modifyQuery($query, $is_manip, $limit, $offset);
        $placeholder_type_guess = $placeholder_type = null;
        $question = '?';
        $colon = ':';
        $positions = $pgtypes = array();
        $position = $parameter = 0;
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
                    if (!empty($types) && is_array($types)) {
                        if ($placeholder_type == ':') {
                        } else {
                            $types = array_values($types);
                        }
                    }
                }
                if ($placeholder_type_guess == '?') {
                    $length = 1;
                    $name = $parameter;
                } else {
                    $name = preg_replace('/^.{'.($position+1).'}([a-z0-9_]+).*$/si', '\\1', $query);
                    if ($name === '') {
                        $err =& $this->raiseError(MDB2_ERROR_SYNTAX, null, null,
                            'prepare: named parameter with an empty name');
                        return $err;
                    }
                    $length = strlen($name) + 1;
                }
                if (is_array($types) && array_key_exists($name, $types)) {
                    $pgtypes[] = $this->datatype->mapPrepareDatatype($types[$name]);
                } elseif (is_array($types) && array_key_exists($parameter, $types)) {
                    $pgtypes[] = $this->datatype->mapPrepareDatatype($types[$parameter]);
                } else {
                    $pgtypes[] = 'text';
                }
                $positions[$name] = $p_position;
                $query = substr_replace($query, '$'.++$parameter, $position, $length);
                $position = $p_position + strlen($parameter);
            } else {
                $position = $p_position;
            }
        }
        $connection = $this->getConnection();
        if (PEAR::isError($connection)) {
            return $connection;
        }

        $types_string = '';
        if ($pgtypes) {
            $types_string = ' ('.implode(', ', $pgtypes).') ';
        }
        $statement_name = strtolower('MDB2_Statement_'.$this->phptype.md5(time() + rand()));
        if (function_exists('pg_prepare')) {
            $result = @pg_prepare($connection, $statement_name, $query);
            if (!$result) {
                $err =& $this->raiseError(null, null, null,
                    'prepare: Unable to create prepared statement handle');
                return $err;
            }
        } else {
            $query = 'PREPARE '.$statement_name.$types_string.' AS '.$query;
            $statement =& $this->_doQuery($query, true, $connection);
            if (PEAR::isError($statement)) {
                return $statement;
            }
        }

        $class_name = 'MDB2_Statement_'.$this->phptype;
        $obj =& new $class_name($this, $statement_name, $positions, $query, $types, $result_types, $is_manip, $limit, $offset);
        return $obj;
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
     * @return mixed MDB2 Error Object or id
     * @access public
     */
    function nextID($seq_name, $ondemand = true)
    {
        $sequence_name = $this->quoteIdentifier($this->getSequenceName($seq_name), true);
        $query = "SELECT NEXTVAL('$sequence_name')";
        $this->expectError(MDB2_ERROR_NOSUCHTABLE);
        $result = $this->queryOne($query, 'integer');
        $this->popExpect();
        if (PEAR::isError($result)) {
            if ($ondemand && $result->getCode() == MDB2_ERROR_NOSUCHTABLE) {
                $this->loadModule('Manager', null, true);
                $result = $this->manager->createSequence($seq_name, 1);
                if (PEAR::isError($result)) {
                    return $this->raiseError($result, null, null,
                        'nextID: on demand sequence could not be created');
                }
                return $this->nextId($seq_name, false);
            }
        }
        return $result;
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
        return $this->queryOne("SELECT last_value FROM $sequence_name", 'integer');
    }
}

/**
 * MDB2 PostGreSQL result driver
 *
 * @package MDB2
 * @category Database
 * @author  Paul Cooper <pgc@ucecom.com>
 */
class MDB2_Result_pgsql extends MDB2_Result_Common
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
            $row = @pg_fetch_array($this->result, null, PGSQL_ASSOC);
            if (is_array($row)
                && $this->db->options['portability'] & MDB2_PORTABILITY_FIX_CASE
            ) {
                $row = array_change_key_case($row, $this->db->options['field_case']);
            }
        } else {
            $row = @pg_fetch_row($this->result);
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
     * @return  mixed   Array variable that holds the names of columns as keys
     *                  or an MDB2 error on failure.
     *                  Some DBMS may not return any columns when the result set
     *                  does not contain any rows.
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
            $column_name = @pg_field_name($this->result, $column);
            $columns[$column_name] = $column;
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
     * @access public
     * @return mixed integer value with the number of columns, a MDB2 error
     *                       on failure
     */
    function numCols()
    {
        $cols = @pg_num_fields($this->result);
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

        if (!($this->result = @pg_get_result($connection))) {
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
        if (is_resource($this->result) && $this->db->connection) {
            $free = @pg_free_result($this->result);
            if ($free === false) {
                return $this->db->raiseError(null, null, null,
                    'free: Could not free result');
            }
        }
        $this->result = false;
        return MDB2_OK;
    }
}

/**
 * MDB2 PostGreSQL buffered result driver
 *
 * @package MDB2
 * @category Database
 * @author  Paul Cooper <pgc@ucecom.com>
 */
class MDB2_BufferedResult_pgsql extends MDB2_Result_pgsql
{
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
        if ($this->rownum != ($rownum - 1) && !@pg_result_seek($this->result, $rownum)) {
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
        $rows = @pg_num_rows($this->result);
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
}

/**
 * MDB2 PostGreSQL statement driver
 *
 * @package MDB2
 * @category Database
 * @author  Paul Cooper <pgc@ucecom.com>
 */
class MDB2_Statement_pgsql extends MDB2_Statement_Common
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

        $query = false;
        $parameters = array();
        // todo: disabled until pg_execute() bytea issues are cleared up
        if (true || !function_exists('pg_execute')) {
            $query = 'EXECUTE '.$this->statement;
        }
        if (!empty($this->positions)) {
            foreach ($this->positions as $parameter => $current_position) {
                if (!array_key_exists($parameter, $this->values)) {
                    return $this->db->raiseError(MDB2_ERROR_NOT_FOUND, null, null,
                        '_execute: Unable to bind to missing placeholder: '.$parameter);
                }
                $value = $this->values[$parameter];
                $type = array_key_exists($parameter, $this->types) ? $this->types[$parameter] : null;
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
                $parameters[] = $this->db->quote($value, $type, $query);
            }
            if ($query) {
                $query.= ' ('.implode(', ', $parameters).')';
            }
        }

        if (!$query) {
            $result = @pg_execute($connection, $this->statement, $parameters);
            if (!$result) {
                $err =& $this->db->raiseError(null, null, null,
                    '_execute: Unable to execute statement');
                return $err;
            }
        } else {
            $result = $this->db->_doQuery($query, $this->is_manip, $connection);
            if (PEAR::isError($result)) {
                return $result;
            }
        }

        if ($this->is_manip) {
            $affected_rows = $this->db->_affectedRows($connection, $result);
            return $affected_rows;
        }

        $result =& $this->db->_wrapResult($result, $this->result_types,
            $result_class, $result_wrap_class, $this->limit, $this->offset);
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
        if (is_null($this->positions)) {
            return $this->db->raiseError(MDB2_ERROR, null, null,
                'free: Prepared statement has already been freed');
        }
        $result = MDB2_OK;

        if (!is_null($this->statement)) {
            $connection = $this->db->getConnection();
            if (PEAR::isError($connection)) {
                return $connection;
            }
            $query = 'DEALLOCATE PREPARE '.$this->statement;
            $result = $this->db->_doQuery($query, true, $connection);
        }

        parent::free();
        return $result;
    }
}
?>