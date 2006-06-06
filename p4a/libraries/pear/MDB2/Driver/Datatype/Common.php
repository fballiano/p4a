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

require_once 'MDB2/LOB.php';

/**
 * @package  MDB2
 * @category Database
 * @author   Lukas Smith <smith@pooteeweet.org>
 */

/**
 * MDB2_Driver_Common: Base class that is extended by each MDB2 driver
 *
 * @package MDB2
 * @category Database
 * @author Lukas Smith <smith@pooteeweet.org>
 */
class MDB2_Driver_Datatype_Common extends MDB2_Module_Common
{
    var $valid_types = array(
        'text'      => '',
        'boolean'   => true,
        'integer'   => 0,
        'decimal'   => 0.0,
        'float'     => 0.0,
        'timestamp' => '1970-01-01 00:00:00',
        'time'      => '00:00:00',
        'date'      => '1970-01-01',
        'clob'      => '',
        'blob'      => '',
    );

    /**
     * contains all LOB objects created with this MDB2 instance
     * @var array
     * @access protected
     */
    var $lobs = array();

    // }}}
    // {{{ checkResultTypes()

    /**
     * Define the list of types to be associated with the columns of a given
     * result set.
     *
     * This function may be called before invoking fetchRow(), fetchOne()
     * fetchCole() and fetchAll() so that the necessary data type
     * conversions are performed on the data to be retrieved by them. If this
     * function is not called, the type of all result set columns is assumed
     * to be text, thus leading to not perform any conversions.
     *
     * @param string $types array variable that lists the
     *       data types to be expected in the result set columns. If this array
     *       contains less types than the number of columns that are returned
     *       in the result set, the remaining columns are assumed to be of the
     *       type text. Currently, the types clob and blob are not fully
     *       supported.
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function checkResultTypes($types)
    {
        $types = is_array($types) ? $types : array($types);
        foreach ($types as $key => $type) {
            if (!isset($this->valid_types[$type])) {
                $db =& $this->getDBInstance();
                if (PEAR::isError($db)) {
                    return $db;
                }

                return $db->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
                    'checkResultTypes: ' . $type . ' for '. $key .' is not a supported column type');
            }
        }
        return $types;
    }

    // }}}
    // {{{ _baseConvertResult()

    /**
     * general type conversion method
     *
     * @param mixed $value refernce to a value to be converted
     * @param int $type constant that specifies which type to convert to
     * @return object a MDB2 error on failure
     * @access protected
     */
    function _baseConvertResult($value, $type)
    {
        switch ($type) {
        case 'text':
            return $value;
        case 'integer':
            return intval($value);
        case 'boolean':
            return !empty($value);
        case 'decimal':
            return $value;
        case 'float':
            return doubleval($value);
        case 'date':
            return $value;
        case 'time':
            return $value;
        case 'timestamp':
            return $value;
        case 'clob':
        case 'blob':
            $this->lobs[] = array(
                'buffer' => null,
                'position' => 0,
                'lob_index' => null,
                'endOfLOB' => false,
                'resource' => $value,
                'value' => null,
                'loaded' => false,
            );
            end($this->lobs);
            $lob_index = key($this->lobs);
            $this->lobs[$lob_index]['lob_index'] = $lob_index;
            return fopen('MDB2LOB://'.$lob_index.'@'.$this->db_index, 'r+');
        }

        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        return $db->raiseError(MDB2_ERROR_INVALID, null, null,
            'attempt to convert result value to an unknown type ' . $type);
    }

    // }}}
    // {{{ convertResult()

    /**
     * convert a value to a RDBMS indepdenant MDB2 type
     *
     * @param mixed $value value to be converted
     * @param int $type constant that specifies which type to convert to
     * @return mixed converted value or a MDB2 error on failure
     * @access public
     */
    function convertResult($value, $type)
    {
        if (is_null($value)) {
            return null;
        }
        return $this->_baseConvertResult($value, $type);
    }

    // }}}
    // {{{ convertResultRow()

    /**
     * convert a result row
     *
     * @param resource $result result identifier
     * @param array $row array with data
     * @return mixed MDB2_OK on success,  a MDB2 error on failure
     * @access public
     */
    function convertResultRow($types, $row)
    {
        if (is_array($types)) {
            reset($types);
            $current_column = -1;
            foreach ($row as $key => $column) {
                ++$current_column;
                if (!isset($column)) {
                    continue;
                }
                if (isset($types[$current_column])) {
                    $type = $types[$current_column];
                } elseif (isset($types[$key])) {
                    $type = $types[$key];
                } elseif (current($types)) {
                    $type = current($types);
                    next($types);
                } else {
                    continue;
                }
                $value = $this->convertResult($row[$key], $type);
                if (PEAR::isError($value)) {
                    return $value;
                }
                $row[$key] = $value;
            }
        }
        return $row;
    }

    // }}}
    // {{{ getDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare
     * of the given type
     *
     * @param string $type type to which the value should be converted to
     * @param string  $name   name the field to be declared.
     * @param string  $field  definition of the field
     * @return string  DBMS specific SQL code portion that should be used to
     *                 declare the specified field.
     * @access public
     */
    function getDeclaration($type, $name, $field)
    {
        if (!method_exists($this, "_get{$type}Declaration")) {
            $db =& $this->getDBInstance();
            if (PEAR::isError($db)) {
                return $db;
            }

            return $db->raiseError(MDB2_ERROR_NOT_FOUND, null, null,
                'type not defined: '.$type);
        }
        return $this->{"_get{$type}Declaration"}($name, $field);
    }

    // }}}
    // {{{ getTypeDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare an text type
     * field to be used in statements like CREATE TABLE.
     *
     * @param array $field  associative array with the name of the properties
     *      of the field being declared as array indexes. Currently, the types
     *      of supported field properties are as follows:
     *
     *      length
     *          Integer value that determines the maximum length of the text
     *          field. If this argument is missing the field should be
     *          declared to have the longest length allowed by the DBMS.
     *
     *      default
     *          Text value to be used as default for this field.
     *
     *      notnull
     *          Boolean flag that indicates whether this field is constrained
     *          to not be set to null.
     * @return string  DBMS specific SQL code portion that should be used to
     *      declare the specified field.
     * @access public
     */
    function getTypeDeclaration($field)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        switch ($field['type']) {
        case 'text':
            $length = array_key_exists('length', $field)
                ? $field['length'] : $db->options['default_text_field_length'];
            $fixed = array_key_exists('fixed', $field) ? $field['fixed'] : false;
            return $fixed ? ($length ? 'CHAR('.$length.')' : 'CHAR('.$db->options['default_text_field_length'].')')
                : ($length ? 'VARCHAR('.$length.')' : 'TEXT');
        case 'clob':
            return 'TEXT';
        case 'blob':
            return 'TEXT';
        case 'integer':
            return 'INT';
        case 'boolean':
            return 'INT';
        case 'date':
            return 'CHAR ('.strlen('YYYY-MM-DD').')';
        case 'time':
            return 'CHAR ('.strlen('HH:MM:SS').')';
        case 'timestamp':
            return 'CHAR ('.strlen('YYYY-MM-DD HH:MM:SS').')';
        case 'float':
            return 'TEXT';
        case 'decimal':
            return 'TEXT';
        }
        return '';
    }

    // }}}
    // {{{ _getDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare a generic type
     * field to be used in statements like CREATE TABLE.
     *
     * @param string $name   name the field to be declared.
     * @param array  $field  associative array with the name of the properties
     *      of the field being declared as array indexes. Currently, the types
     *      of supported field properties are as follows:
     *
     *      length
     *          Integer value that determines the maximum length of the text
     *          field. If this argument is missing the field should be
     *          declared to have the longest length allowed by the DBMS.
     *
     *      default
     *          Text value to be used as default for this field.
     *
     *      notnull
     *          Boolean flag that indicates whether this field is constrained
     *          to not be set to null.
     * @return string  DBMS specific SQL code portion that should be used to
     *      declare the specified field.
     * @access protected
     */
    function _getDeclaration($name, $field)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $default = '';
        if (array_key_exists('default', $field)) {
            if ($field['default'] === '') {
                $field['default'] = (array_key_exists('notnull', $field) && $field['notnull'])
                    ? $this->valid_types[$field['type']] : null;
                if ($field['default'] === ''
                    && $db->options['portability'] & MDB2_PORTABILITY_EMPTY_TO_NULL
                ) {
                    $field['default'] = ' ';
                }
            }

            $default = ' DEFAULT '.$this->quote($field['default'], $field['type']);
        }

        $notnull = (array_key_exists('notnull', $field) && $field['notnull']) ? ' NOT NULL' : '';
        $name = $db->quoteIdentifier($name, true);
        return $name.' '.$this->getTypeDeclaration($field).$default.$notnull;
    }

    // }}}
    // {{{ _getIntegerDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare an integer type
     * field to be used in statements like CREATE TABLE.
     *
     * @param string $name name the field to be declared.
     * @param array $field associative array with the name of the properties
     *       of the field being declared as array indexes. Currently, the types
     *       of supported field properties are as follows:
     *
     *       unsigned
     *           Boolean flag that indicates whether the field should be
     *           declared as unsigned integer if possible.
     *
     *       default
     *           Integer value to be used as default for this field.
     *
     *       notnull
     *           Boolean flag that indicates whether this field is constrained
     *           to not be set to null.
     * @return string DBMS specific SQL code portion that should be used to
     *       declare the specified field.
     * @access protected
     */
    function _getIntegerDeclaration($name, $field)
    {
        if (array_key_exists('unsigned', $field) && $field['unsigned']) {
            $db =& $this->getDBInstance();
            if (PEAR::isError($db)) {
                return $db;
            }

            $db->warnings[] = "unsigned integer field \"$name\" is being declared as signed integer";
        }
        return $this->_getDeclaration($name, $field);
    }

    // }}}
    // {{{ _getTextDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare an text type
     * field to be used in statements like CREATE TABLE.
     *
     * @param string $name name the field to be declared.
     * @param array $field associative array with the name of the properties
     *       of the field being declared as array indexes. Currently, the types
     *       of supported field properties are as follows:
     *
     *       length
     *           Integer value that determines the maximum length of the text
     *           field. If this argument is missing the field should be
     *           declared to have the longest length allowed by the DBMS.
     *
     *       default
     *           Text value to be used as default for this field.
     *
     *       notnull
     *           Boolean flag that indicates whether this field is constrained
     *           to not be set to null.
     * @return string DBMS specific SQL code portion that should be used to
     *       declare the specified field.
     * @access protected
     */
    function _getTextDeclaration($name, $field)
    {
        return $this->_getDeclaration($name, $field);
    }

    // }}}
    // {{{ _getCLOBDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare an character
     * large object type field to be used in statements like CREATE TABLE.
     *
     * @param string $name name the field to be declared.
     * @param array $field associative array with the name of the properties
     *        of the field being declared as array indexes. Currently, the types
     *        of supported field properties are as follows:
     *
     *        length
     *            Integer value that determines the maximum length of the large
     *            object field. If this argument is missing the field should be
     *            declared to have the longest length allowed by the DBMS.
     *
     *        notnull
     *            Boolean flag that indicates whether this field is constrained
     *            to not be set to null.
     * @return string DBMS specific SQL code portion that should be used to
     *        declare the specified field.
     * @access public
     */
    function _getCLOBDeclaration($name, $field)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $notnull = (array_key_exists('notnull', $field) && $field['notnull']) ? ' NOT NULL' : '';
        $name = $db->quoteIdentifier($name, true);
        return $name.' '.$this->getTypeDeclaration($field).$notnull;
    }

    // }}}
    // {{{ _getBLOBDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare an binary large
     * object type field to be used in statements like CREATE TABLE.
     *
     * @param string $name name the field to be declared.
     * @param array $field associative array with the name of the properties
     *        of the field being declared as array indexes. Currently, the types
     *        of supported field properties are as follows:
     *
     *        length
     *            Integer value that determines the maximum length of the large
     *            object field. If this argument is missing the field should be
     *            declared to have the longest length allowed by the DBMS.
     *
     *        notnull
     *            Boolean flag that indicates whether this field is constrained
     *            to not be set to null.
     * @return string DBMS specific SQL code portion that should be used to
     *        declare the specified field.
     * @access protected
     */
    function _getBLOBDeclaration($name, $field)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $notnull = (array_key_exists('notnull', $field) && $field['notnull']) ? ' NOT NULL' : '';
        $name = $db->quoteIdentifier($name, true);
        return $name.' '.$this->getTypeDeclaration($field).$notnull;
    }

    // }}}
    // {{{ _getBooleanDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare a boolean type
     * field to be used in statements like CREATE TABLE.
     *
     * @param string $name name the field to be declared.
     * @param array $field associative array with the name of the properties
     *       of the field being declared as array indexes. Currently, the types
     *       of supported field properties are as follows:
     *
     *       default
     *           Boolean value to be used as default for this field.
     *
     *       notnullL
     *           Boolean flag that indicates whether this field is constrained
     *           to not be set to null.
     * @return string DBMS specific SQL code portion that should be used to
     *       declare the specified field.
     * @access protected
     */
    function _getBooleanDeclaration($name, $field)
    {
        return $this->_getDeclaration($name, $field);
    }

    // }}}
    // {{{ _getDateDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare a date type
     * field to be used in statements like CREATE TABLE.
     *
     * @param string $name name the field to be declared.
     * @param array $field associative array with the name of the properties
     *       of the field being declared as array indexes. Currently, the types
     *       of supported field properties are as follows:
     *
     *       default
     *           Date value to be used as default for this field.
     *
     *       notnull
     *           Boolean flag that indicates whether this field is constrained
     *           to not be set to null.
     * @return string DBMS specific SQL code portion that should be used to
     *       declare the specified field.
     * @access protected
     */
    function _getDateDeclaration($name, $field)
    {
        return $this->_getDeclaration($name, $field);
    }

    // }}}
    // {{{ _getTimestampDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare a timestamp
     * field to be used in statements like CREATE TABLE.
     *
     * @param string $name name the field to be declared.
     * @param array $field associative array with the name of the properties
     *       of the field being declared as array indexes. Currently, the types
     *       of supported field properties are as follows:
     *
     *       default
     *           Timestamp value to be used as default for this field.
     *
     *       notnull
     *           Boolean flag that indicates whether this field is constrained
     *           to not be set to null.
     * @return string DBMS specific SQL code portion that should be used to
     *       declare the specified field.
     * @access protected
     */
    function _getTimestampDeclaration($name, $field)
    {
        return $this->_getDeclaration($name, $field);
    }

    // }}}
    // {{{ _getTimeDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare a time
     * field to be used in statements like CREATE TABLE.
     *
     * @param string $name name the field to be declared.
     * @param array $field associative array with the name of the properties
     *       of the field being declared as array indexes. Currently, the types
     *       of supported field properties are as follows:
     *
     *       default
     *           Time value to be used as default for this field.
     *
     *       notnull
     *           Boolean flag that indicates whether this field is constrained
     *           to not be set to null.
     * @return string DBMS specific SQL code portion that should be used to
     *       declare the specified field.
     * @access protected
     */
    function _getTimeDeclaration($name, $field)
    {
        return $this->_getDeclaration($name, $field);
    }

    // }}}
    // {{{ _getFloatDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare a float type
     * field to be used in statements like CREATE TABLE.
     *
     * @param string $name name the field to be declared.
     * @param array $field associative array with the name of the properties
     *       of the field being declared as array indexes. Currently, the types
     *       of supported field properties are as follows:
     *
     *       default
     *           Float value to be used as default for this field.
     *
     *       notnull
     *           Boolean flag that indicates whether this field is constrained
     *           to not be set to null.
     * @return string DBMS specific SQL code portion that should be used to
     *       declare the specified field.
     * @access protected
     */
    function _getFloatDeclaration($name, $field)
    {
        return $this->_getDeclaration($name, $field);
    }

    // }}}
    // {{{ _getDecimalDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare a decimal type
     * field to be used in statements like CREATE TABLE.
     *
     * @param string $name name the field to be declared.
     * @param array $field associative array with the name of the properties
     *       of the field being declared as array indexes. Currently, the types
     *       of supported field properties are as follows:
     *
     *       default
     *           Decimal value to be used as default for this field.
     *
     *       notnull
     *           Boolean flag that indicates whether this field is constrained
     *           to not be set to null.
     * @return string DBMS specific SQL code portion that should be used to
     *       declare the specified field.
     * @access protected
     */
    function _getDecimalDeclaration($name, $field)
    {
        return $this->_getDeclaration($name, $field);
    }

    // }}}
    // {{{ compareDefinition()

    /**
     * Obtain an array of changes that may need to applied
     *
     * @param array $current new definition
     * @param array  $previous old definition
     * @return array  containing all changes that will need to be applied
     * @access public
     */
    function compareDefinition($current, $previous)
    {
        $type = array_key_exists('type', $current) ? $current['type'] : null;

        if (!method_exists($this, "_compare{$type}Definition")) {
            $db =& $this->getDBInstance();
            if (PEAR::isError($db)) {
                return $db;
            }

            return $db->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
                'type "'.$current['type'].'" is not yet supported');
        }

        if (!array_key_exists('type', $previous) || $previous['type'] != $type) {
            return $current;
        }

        $change = $this->{"_compare{$type}Definition"}($current, $previous);

        if ($previous['type'] != $type) {
            $change['type'] = true;
        }

        $previous_notnull = array_key_exists('notnull', $previous) ? $previous['notnull'] : false;
        $notnull = array_key_exists('notnull', $current) ? $current['notnull'] : false;
        if ($previous_notnull != $notnull) {
            $change['notnull'] = true;
        }

        $previous_default = array_key_exists('default', $previous) ? $previous['default'] :
            ($previous_notnull ? '' : null);
        $default = array_key_exists('default', $current) ? $current['default'] :
            ($notnull ? '' : null);
        if ($previous_default !== $default) {
            $change['default'] = true;
        }

        return $change;
    }

    // }}}
    // {{{ _compareIntegerDefinition()

    /**
     * Obtain an array of changes that may need to applied to an integer field
     *
     * @param array $current new definition
     * @param array  $previous old definition
     * @return array  containing all changes that will need to be applied
     * @access protected
     */
    function _compareIntegerDefinition($current, $previous)
    {
        $change = array();
        $previous_unsigned = array_key_exists('unsigned', $previous) ? $previous['unsigned'] : false;
        $unsigned = array_key_exists('unsigned', $current) ? $current['unsigned'] : false;
        if ($previous_unsigned != $unsigned) {
            $change['unsigned'] = true;
        }
        $previous_autoincrement = array_key_exists('autoincrement', $previous) ? $previous['autoincrement'] : false;
        $autoincrement = array_key_exists('autoincrement', $current) ? $current['autoincrement'] : false;
        if ($previous_autoincrement != $autoincrement) {
            $change['autoincrement'] = true;
        }
        return $change;
    }

    // }}}
    // {{{ _compareTextDefinition()

    /**
     * Obtain an array of changes that may need to applied to an text field
     *
     * @param array $current new definition
     * @param array  $previous old definition
     * @return array  containing all changes that will need to be applied
     * @access protected
     */
    function _compareTextDefinition($current, $previous)
    {
        $change = array();
        $previous_length = array_key_exists('length', $previous) ? $previous['length'] : 0;
        $length = array_key_exists('length', $current) ? $current['length'] : 0;
        if ($previous_length != $length) {
            $change['length'] = true;
        }
        $previous_fixed = array_key_exists('fixed', $previous) ? $previous['fixed'] : 0;
        $fixed = array_key_exists('fixed', $current) ? $current['fixed'] : 0;
        if ($previous_fixed != $fixed) {
            $change['fixed'] = true;
        }
        return $change;
    }

    // }}}
    // {{{ _compareCLOBDefinition()

    /**
     * Obtain an array of changes that may need to applied to an CLOB field
     *
     * @param array $current new definition
     * @param array  $previous old definition
     * @return array  containing all changes that will need to be applied
     * @access protected
     */
    function _compareCLOBDefinition($current, $previous)
    {
        return $this->_compareTextDefinition($current, $previous);
    }

    // }}}
    // {{{ _compareBLOBDefinition()

    /**
     * Obtain an array of changes that may need to applied to an BLOB field
     *
     * @param array $current new definition
     * @param array  $previous old definition
     * @return array  containing all changes that will need to be applied
     * @access protected
     */
    function _compareBLOBDefinition($current, $previous)
    {
        return $this->_compareTextDefinition($current, $previous);
    }

    // }}}
    // {{{ _compareDateDefinition()

    /**
     * Obtain an array of changes that may need to applied to an date field
     *
     * @param array $current new definition
     * @param array  $previous old definition
     * @return array  containing all changes that will need to be applied
     * @access protected
     */
    function _compareDateDefinition($current, $previous)
    {
        return array();
    }

    // }}}
    // {{{ _compareTimeDefinition()

    /**
     * Obtain an array of changes that may need to applied to an time field
     *
     * @param array $current new definition
     * @param array  $previous old definition
     * @return array  containing all changes that will need to be applied
     * @access protected
     */
    function _compareTimeDefinition($current, $previous)
    {
        return array();
    }

    // }}}
    // {{{ _compareTimestampDefinition()

    /**
     * Obtain an array of changes that may need to applied to an timestamp field
     *
     * @param array $current new definition
     * @param array  $previous old definition
     * @return array  containing all changes that will need to be applied
     * @access protected
     */
    function _compareTimestampDefinition($current, $previous)
    {
        return array();
    }

    // }}}
    // {{{ _compareBooleanDefinition()

    /**
     * Obtain an array of changes that may need to applied to an boolean field
     *
     * @param array $current new definition
     * @param array  $previous old definition
     * @return array  containing all changes that will need to be applied
     * @access protected
     */
    function _compareBooleanDefinition($current, $previous)
    {
        return array();
    }

    // }}}
    // {{{ _compareFloatDefinition()

    /**
     * Obtain an array of changes that may need to applied to an float field
     *
     * @param array $current new definition
     * @param array  $previous old definition
     * @return array  containing all changes that will need to be applied
     * @access protected
     */
    function _compareFloatDefinition($current, $previous)
    {
        return array();
    }

    // }}}
    // {{{ _compareDecimalDefinition()

    /**
     * Obtain an array of changes that may need to applied to an decimal field
     *
     * @param array $current new definition
     * @param array  $previous old definition
     * @return array  containing all changes that will need to be applied
     * @access protected
     */
    function _compareDecimalDefinition($current, $previous)
    {
        return array();
    }

    // }}}
    // {{{ quote()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @param string $type type to which the value should be converted to
     * @param bool $quote determines if the value should be quoted and escaped
     * @return string text string that represents the given argument value in
     *       a DBMS specific format.
     * @access public
     */
    function quote($value, $type = null, $quote = true)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        if (is_null($value)
            || ($value === '' && $db->options['portability'] & MDB2_PORTABILITY_EMPTY_TO_NULL)
        ) {
            if (!$quote) {
                return null;
            }
            return 'NULL';
        }

        if (is_null($type)) {
            switch (gettype($value)) {
            case 'integer':
                $type = 'integer';
                break;
            case 'double':
                // todo: default to decimal as float is quite unusual
                // $type = 'float';
                $type = 'decimal';
                break;
            case 'boolean':
                $type = 'boolean';
                break;
            case 'array':
                 $value = serialize($value);
            case 'object':
                 $type = 'text';
                break;
            default:
                if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $value)) {
                    $type = 'timestamp';
                } elseif (preg_match('/^\d{2}:\d{2}$/', $value)) {
                    $type = 'time';
                } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $type = 'date';
                } else {
                    $type = 'text';
                }
                break;
            }
        }

        if (!method_exists($this, "_quote{$type}")) {
            return $db->raiseError('type not defined: '.$type);
        }
        $value = $this->{"_quote{$type}"}($value, $quote);

        return $value;
    }

    // }}}
    // {{{ _quoteInteger()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @param bool $quote determines if the value should be quoted and escaped
     * @return string text string that represents the given argument value in
     *       a DBMS specific format.
     * @access protected
     */
    function _quoteInteger($value, $quote)
    {
        return (int)$value;
    }

    // }}}
    // {{{ _quoteText()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @param bool $quote determines if the value should be quoted and escaped
     * @return string text string that already contains any DBMS specific
     *       escaped character sequences.
     * @access protected
     */
    function _quoteText($value, $quote)
    {
        if (!$quote) {
            return $value;
        }
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        return "'".$db->escape($value)."'";
    }

    // }}}
    // {{{ _readFile()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @return string text string that represents the given argument value in
     *       a DBMS specific format.
     * @access protected
     */
    function _readFile($value)
    {
        $close = false;
        if (preg_match('/^(\w+:\/\/)(.*)$/', $value, $match)) {
            $close = true;
            if ($match[1] == 'file://') {
                $value = $match[2];
            }
            $value = @fopen($value, 'r');
        }

        if (is_resource($value)) {
            $db =& $this->getDBInstance();
            if (PEAR::isError($db)) {
                return $db;
            }

            $fp = $value;
            $value = '';
            while (!@feof($fp)) {
                $value.= @fread($fp, $db->options['lob_buffer_length']);
            }
            if ($close) {
                @fclose($fp);
            }
        }

        return $value;
    }

    // }}}
    // {{{ _quoteLOB()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @param bool $quote determines if the value should be quoted and escaped
     * @return string text string that represents the given argument value in
     *       a DBMS specific format.
     * @access protected
     */
    function _quoteLOB($value, $quote)
    {
        $value = $this->_readFile($value);
        return $this->_quoteText($value, $quote);
    }

    // }}}
    // {{{ _quoteCLOB()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @param bool $quote determines if the value should be quoted and escaped
     * @return string text string that represents the given argument value in
     *       a DBMS specific format.
     * @access protected
     */
    function _quoteCLOB($value, $quote)
    {
        return $this->_quoteLOB($value, $quote);
    }

    // }}}
    // {{{ _quoteBLOB()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @param bool $quote determines if the value should be quoted and escaped
     * @return string text string that represents the given argument value in
     *       a DBMS specific format.
     * @access protected
     */
    function _quoteBLOB($value, $quote)
    {
        return $this->_quoteLOB($value, $quote);
    }

    // }}}
    // {{{ _quoteBoolean()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @param bool $quote determines if the value should be quoted and escaped
     * @return string text string that represents the given argument value in
     *       a DBMS specific format.
     * @access protected
     */
    function _quoteBoolean($value, $quote)
    {
        return ($value ? 1 : 0);
    }

    // }}}
    // {{{ _quoteDate()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @param bool $quote determines if the value should be quoted and escaped
     * @return string text string that represents the given argument value in
     *       a DBMS specific format.
     * @access protected
     */
    function _quoteDate($value, $quote)
    {
        if ($value === 'CURRENT_DATE') {
            $db =& $this->getDBInstance();
            if (PEAR::isError($db)) {
                return $db;
            }
            if (isset($db->function) && is_a($db->function, 'MDB2_Driver_Function_Common')) {
                return $db->function->now('date');
            }
            return 'CURRENT_DATE';
        }
        return $this->_quoteText($value, $quote);
    }

    // }}}
    // {{{ _quoteTimestamp()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @param bool $quote determines if the value should be quoted and escaped
     * @return string text string that represents the given argument value in
     *       a DBMS specific format.
     * @access protected
     */
    function _quoteTimestamp($value, $quote)
    {
        if ($value === 'CURRENT_TIMESTAMP') {
            $db =& $this->getDBInstance();
            if (PEAR::isError($db)) {
                return $db;
            }
            if (isset($db->function) && is_a($db->function, 'MDB2_Driver_Function_Common')) {
                return $db->function->now('timestamp');
            }
            return 'CURRENT_TIMESTAMP';
        }
        return $this->_quoteText($value, $quote);
    }

    // }}}
    // {{{ _quoteTime()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     *       compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @param bool $quote determines if the value should be quoted and escaped
     * @return string text string that represents the given argument value in
     *       a DBMS specific format.
     * @access protected
     */
    function _quoteTime($value, $quote)
    {
        if ($value === 'CURRENT_TIME') {
            $db =& $this->getDBInstance();
            if (PEAR::isError($db)) {
                return $db;
            }
            if (isset($db->function) && is_a($db->function, 'MDB2_Driver_Function_Common')) {
                return $db->function->now('time');
            }
            return 'CURRENT_TIME';
        }
        return $this->_quoteText($value, $quote);
    }

    // }}}
    // {{{ _quoteFloat()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @param bool $quote determines if the value should be quoted and escaped
     * @return string text string that represents the given argument value in
     *       a DBMS specific format.
     * @access protected
     */
    function _quoteFloat($value, $quote)
    {
        if (preg_match('/^(.*)e([-+])(\d+)$/i', $value, $matches)) {
            $value = $matches[1].'E'.$matches[2].str_pad($matches[3], 2, '0', STR_PAD_LEFT);
        } else {
            $value = $this->_quoteDecimal($value, $quote);
        }
        return $value;
    }

    // }}}
    // {{{ _quoteDecimal()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @param bool $quote determines if the value should be quoted and escaped
     * @return string text string that represents the given argument value in
     *       a DBMS specific format.
     * @access protected
     */
    function _quoteDecimal($value, $quote)
    {
        $precision = strlen($value) - strlen(intval($value));
        if ($precision) {
            --$precision; // don't count decimal seperator
        }
        return number_format($value, $precision, '.', '');
    }

    // }}}
    // {{{ writeLOBToFile()

    /**
     * retrieve LOB from the database
     *
     * @param resource $lob stream handle
     * @param string $file name of the file into which the LOb should be fetched
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access protected
     */
    function writeLOBToFile($lob, $file)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        if (preg_match('/^(\w+:\/\/)(.*)$/', $file, $match)) {
            if ($match[1] == 'file://') {
                $file = $match[2];
            }
        }

        $fp = @fopen($file, 'wb');
        while (!@feof($lob)) {
            $result = @fread($lob, $db->options['lob_buffer_length']);
            $read = strlen($result);
            if (@fwrite($fp, $result, $read) != $read) {
                @fclose($fp);
                return $db->raiseError(MDB2_ERROR, null, null,
                    'writeLOBToFile: could not write to the output file');
            }
        }
        @fclose($fp);
        return MDB2_OK;
    }

    // }}}
    // {{{ _retrieveLOB()

    /**
     * retrieve LOB from the database
     *
     * @param array $lob array
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access protected
     */
    function _retrieveLOB(&$lob)
    {
        if (is_null($lob['value'])) {
            $lob['value'] = $lob['resource'];
        }
        $lob['loaded'] = true;
        return MDB2_OK;
    }

    // }}}
    // {{{ readLOB()

    /**
     * Read data from large object input stream.
     *
     * @param resource $lob stream handle
     * @param string $data reference to a variable that will hold data
     *                          to be read from the large object input stream
     * @param integer $length    value that indicates the largest ammount ofdata
     *                          to be read from the large object input stream.
     * @return mixed the effective number of bytes read from the large object
     *                      input stream on sucess or an MDB2 error object.
     * @access public
     * @see endOfLOB()
     */
    function _readLOB($lob, $length)
    {
        return substr($lob['value'], $lob['position'], $length);
    }

    // }}}
    // {{{ _endOfLOB()

    /**
     * Determine whether it was reached the end of the large object and
     * therefore there is no more data to be read for the its input stream.
     *
     * @param array $lob array
     * @return mixed true or false on success, a MDB2 error on failure
     * @access protected
     */
    function _endOfLOB($lob)
    {
        return $lob['endOfLOB'];
    }

    // }}}
    // {{{ destroyLOB()

    /**
     * Free any resources allocated during the lifetime of the large object
     * handler object.
     *
     * @param resource $lob stream handle
     * @access public
     */
    function destroyLOB($lob)
    {
        $lob_data = stream_get_meta_data($lob);
        $lob_index = $lob_data['wrapper_data']->lob_index;
        fclose($lob);
        if (isset($this->lobs[$lob_index])) {
            $this->_destroyLOB($this->lobs[$lob_index]);
            unset($this->lobs[$lob_index]);
        }
        return MDB2_OK;
    }

    // }}}
    // {{{ _destroyLOB()

    /**
     * Free any resources allocated during the lifetime of the large object
     * handler object.
     *
     * @param array $lob array
     * @access private
     */
    function _destroyLOB(&$lob)
    {
        return MDB2_OK;
    }

    // }}}
    // {{{ implodeArray()

    /**
     * apply a type to all values of an array and return as a comma seperated string
     * useful for generating IN statements
     *
     * @access public
     *
     * @param array $array data array
     * @param string $type determines type of the field
     *
     * @return string comma seperated values
     */
    function implodeArray($array, $type = false)
    {
        if (!is_array($array) || empty($array)) {
            return 'NULL';
        }
        if ($type) {
            foreach ($array as $value) {
                $return[] = $this->quote($value, $type);
            }
        } else {
            $return = $array;
        }
        return implode(', ', $return);
    }

    // }}}
    // {{{ mapNativeDatatype()

    /**
     * Maps a native array description of a field to a MDB2 datatype and length
     *
     * @param array  $field native field description
     * @return array containing the various possible types, length, sign, fixed
     * @access public
     */
    function mapNativeDatatype($field)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        return $db->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
            'mapNativeDatatype: method not implemented');
    }

}

?>