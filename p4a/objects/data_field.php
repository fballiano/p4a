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
 * To contact the authors write to:                                     <br />
 * CreaLabs SNC                                                         <br />
 * Via Medail, 32                                                       <br />
 * 10144 Torino (Italy)                                                 <br />
 * Website: {@link http://www.crealabs.it}                              <br />
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
 * Every DATA_SOURCE field is a DATA_FIELD.
 * It's used to emulate some database behaviours
 * such as default values.<br>
 * It can be considered the same as a database table's field.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
class P4A_Data_Field extends P4A_Object
{
	/**
	 * @var string
	 */
	protected $value = null;

	/**
	 * @var string
	 */
	protected $new_value = null;

	/**
	 * @var string
	 */
	protected $default_value = null;

	/**
	 * The default value for the field in new rows.
	 * @var string
	 */
	protected $type = 'text';

	/**
	 * @var boolean
	 */
	protected $is_read_only = false;
	
	/**
	 * @var string
	 */
	protected $sequence = null;
	
	/**
	 * @var string
	 */
	protected $_DSN = null;
	
	/**
	 * @var string
	 */
	protected $schema = null;
	
	/**
	 * @var string
	 */
	protected $table = null;
	
	/**
	 * @var string
	 */
	protected $alias_of = null;
	
	/**
	 * @var integer
	 */
	protected $length = null;
	
	/**
	 * @var integer
	 */
	protected $num_of_decimals = null;
	
	/**
	 * Path under P4A_UPLOADS_PATH where uploads will be stored
	 * @var string
	 */
	protected $upload_subpath = null;	

	/**
	 * Sets value and new_value
	 * @param string $value
	 * @return P4A_Data_Field
	 */
	public function setValue($value)
	{
		$this->value = $value;
		$this->setNewValue($value);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Returns the value of the data field for safe SQL queries
	 * @return string
	 */
	public function getSQLValue()
	{
		return P4A_DB::singleton($this->getDSN())->quote($this->value);
	}

	/**
	 * @param string $value
	 * @return P4A_Data_Field
	 */
	public function setNewValue($value)
	{
		$this->new_value = $value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getNewValue()
	{
		return $this->new_value;
	}

	/**
	 * Returns the value of the data field for safe SQL queries
	 * @return string
	 */
	public function getSQLNewValue()
	{
		return P4A_DB::singleton($this->getDSN())->quote($this->new_value);
	}

	/**
	 * @param string $type
	 * @return P4A_Data_Field
	 */
	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Gets/sets read only state
	 * @param boolean|P4A_Data_Field $read_only
	 * @return boolean
	 */
	public function isReadOnly($read_only = null)
	{
		if ($read_only === null) return $this->is_read_only;
		$this->is_read_only = $read_only;
		return $this;
	}

	/**
	 * @param string $DSN
	 * @return P4A_Data_Field
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
	 * @param string $value
	 * @return P4A_Data_Field
	 */
	public function setDefaultValue($value = null)
	{
		if ($value === null) {
			$this->setNewValue($this->getDefaultValue());
		} else {
			$this->default_value = $value;
		}
		return $this;
	}

	/**
	 * @param string $name
	 * @return P4A_Data_Field
	 */
	public function setSequence($name = null)
	{
		$this->sequence = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDefaultValue()
	{
		if ($this->sequence === null) {
			return $this->default_value;
		} else {
			return P4A_DB::singleton($this->getDSN())->nextSequenceId($this->sequence, $this->schema);
		}
	}
	
	/**
	 * @param string $schema
	 * @return P4A_Data_Field
	 */
	public function setSchema($schema)
	{
		$this->schema = $schema;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSchema()
	{
		return $this->schema;
	}

	/**
	 * @param string $table
	 * @return P4A_Data_Field
	 */
	public function setTable($table)
	{
		$this->table = $table;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * @param string $column
	 * @return P4A_Data_Field
	 */
	public function setAliasOf($column)
	{
		$this->alias_of = $column;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAliasOf()
	{
		return $this->alias_of;
	}

	/**
	 * @param integer $length
	 */
	public function setLength($length)
	{
		$this->length = $length;
		return $this;
	}

	/**
	 * @return integer
	 */
	public function getLength()
	{
		return $this->length;
	}
	
	/**
	 * @param integer $num_of_decimals
	 * @return P4A_Data_Field
	 */
	public function setNumOfDecimals($num_of_decimals)
	{
		$this->num_of_decimals = $num_of_decimals;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getNumOfDecimals()
	{
		return $this->num_of_decimals;
	}
	
	/**
	 * @return string
	 */
	public function getSchemaTableField()
	{
		$schema = $this->getSchema();
		if (strlen($schema)) $schema = "{$schema}.";

		$table = $this->getTable();
		if (strlen($table)) $table = "{$table}.";
		
		$alias_of = $this->getAliasOf();
		if (!strlen($alias_of)) $alias_of = $this->getName();

		return $schema . $table . $alias_of;
	}
	
	/**
	 * @return string
	 */
	public function getWhereFit()
	{
		if (strlen($this->getAliasOf())) {
			return $this->getAliasOf();
		} else {
			$schema = $this->getSchema();
			if (strlen($schema)) $schema = "{$schema}.";

			$table = $this->getTable();
			if (strlen($table)) $table = "{$table}.";		

			$name = $this->getName();
			return $schema . $table . $name;
		}
	}
	
	/**
	 * Sets the subpath of P4A_UPLOADS_PATH where the upload will happen
	 * @param string The subdir (can be "test", "test/", "test/test", "test/test/test/")
	 * @return P4A_Field
	 */
	public function setUploadSubpath($subpath = null)
	{
		$this->upload_subpath = $subpath;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUploadSubpath()
	{
		return $this->upload_subpath;
	}	
}