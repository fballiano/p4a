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
 * To contact the authors write to:									<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)												<br>
 * Web:    {@link http://www.crealabs.it}							<br>
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
 * Every DATA_SOURCE field is a DATA_FIELD.
 * It's used to emulate some database behaviours
 * such as default values.<br>
 * It can be considered the same as a database table's field.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
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
	 * Sets value and new_value
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
		$this->setNewValue($value);
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Returns the value of the data field for safe SQL queries.
	 * @return mixed
	 */
	public function getSQLValue()
	{
		return str_replace("'", "''", $this->value);
	}

	/**
	 * @param string $value
	 */
	public function setNewValue($value)
	{
		$this->new_value = $value;
	}

	/**
	 * @return string
	 */
	public function getNewValue()
	{
		return $this->new_value;
	}

	/**
	 * Returns the value of the data field for safe SQL queries.
	 * @return string
	 */
	public function getSQLNewValue()
	{
		return str_replace("'", "''", $this->new_value);
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setReadOnly($value = true)
	{
		$this->is_read_only = $value;
	}

	public function isReadOnly()
	{
		return $this->is_read_only;
	}

	public function setDSN($DSN)
	{
		$this->_DSN = $DSN;
	}

	public function getDSN()
	{
		return $this->_DSN;
	}

	public function setDefaultValue($value = null)
	{
		if ($value === null) {
			$this->setNewValue($this->getDefaultValue());
		} else {
			$this->default_value = $value;
		}
	}

	public function setSequence($name = null)
	{
		if ($name === null) {
			$this->sequence = null;
		} else {
			$this->sequence = "{$name}_seq";
		}
	}

	public function getDefaultValue()
	{
		if ($this->sequence === null) {
			return $this->default_value;
		} else {
			return P4A_DB::singleton($this->getDSN())->nextSequenceId($this->sequence);
		}
	}
	
	public function setSchema($schema)
	{
		$this->schema = $schema;
	}

	public function getSchema()
	{
		return $this->schema;
	}

	public function setTable($table)
	{
		$this->table = $table;
	}

	public function getTable()
	{
		return $this->table;
	}

	public function setAliasOf($column)
	{
		$this->alias_of = $column;
	}

	public function getAliasOf()
	{
		return $this->alias_of;
	}

	public function setLength($length)
	{
		$this->length = $length;
	}

	public function getLength()
	{
		return $this->length;
	}
	
	public function setNumOfDecimals($num_of_decimals)
	{
		$this->num_of_decimals = $num_of_decimals;
	}
	
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
}