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

namespace P4A\DataSource;

/**
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class ArraySource extends DataSource
{
    /**
     * @var array
     */
    protected $_array = array();

    /**
     * @var P4A_Collection
     */
    public $fields = null;

    /**
     * @param array $array
     * @return P4A_Array_Source
     */
    public function load($array = null)
    {
        $this->_array = array();
        $this->_array[-1] = array();

        if (empty($array)) {
            return;
        }

        $first_row = $array[0];
        if (!is_array($first_row)) {
            foreach ($array as $value) {
                $this->_array[] = array('f0' => $value);
            }
            $this->setPK('f0');
            $first_row = array('f0' => $first_row);
        } else {
            foreach ($array as $value) {
                $this->_array[] = $value;
            }
        }

        foreach ($first_row as $field_name => $value) {
            if (!isset($this->fields->$field_name)) {
                $this->fields->build('P4A\DataField', $field_name);
            }
            $this->_array[-1][$field_name] = '';
        }

        return $this;
    }


    public function row($num_row = null, $move_pointer = true)
    {
        if ($num_row !== null) {
            $row = @$this->_array[$num_row - 1];
        } else {
            $num_row = $this->_pointer;
            $row = @$this->_array[$num_row - 1];
        }

        if ($row === null) {
            $row = array();
        }

        if ($move_pointer) {
            if ($this->actionHandler('beforemoverow') == ABORT) {
                return ABORT;
            }

            if ($this->isActionTriggered('onmoverow')) {
                if ($this->actionHandler('onmoverow') == ABORT) {
                    return ABORT;
                }
            } else {
                if (!empty($row)) {
                    $this->_pointer = $num_row;

                    foreach ($row as $field => $value) {
                        $this->fields->$field->setValue($value);
                    }
                } elseif ($this->getNumRows() == 0) {
                    $this->newRow();
                }
            }

            $this->actionHandler('aftermoverow');
        }

        return $row;
    }

    public function getAll($from = 0, $count = 0)
    {
        if ($this->getNumRows() == 0) {
            return array();
        }

        if ($from == 0 and $count == 0) {
            $array = $this->_array;
            array_shift($array);
            return $array;
        } else {
            $array = $this->_array;
            array_shift($array);
            return array_slice($array, $from, $count);
        }
    }

    public function getNumRows()
    {
        return count($this->_array) - 1;
    }

    public function getPkRow($pk)
    {
        foreach ($this->_array as $row) {
            if ($row[$this->_pk] == $pk) {
                return $row;
            }
        }
        return false;
    }

    public function deleteRow()
    {
        $pointer = $this->getRowNumber();
        unset($this->_array[$pointer - 1]);
        parent::deleteRow();
    }
}