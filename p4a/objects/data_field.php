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
	 * The value of field.
	 * @var string
	 */
	protected $value = null;

	/**
	 * The new value of field
	 * @var string
	 */
	protected $new_value = null;

	/**
	 * The default value for the field in new rows.
	 * @var string
	 */
	protected $default_value = null;

	/**
	 * The default value for the field in new rows.
	 * @var string
	 */
	protected $type = 'text';

	protected $is_read_only = false;
	protected $sequence = null;
	protected $schema = null;
	protected $table = null;
	protected $alias_of = null;
	protected $length = null;

	public function setValue($value)
	{
		$this->value = $value;
		$this->setNewValue($value);
	}

	/**
	 * Returns the value of the data field.
	 * @return mixed
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
		return addslashes($this->value);
	}

	/**
	 * Sets the new value of the data field.
	 */
	public function setNewValue($value)
	{
		$this->new_value = $value;
	}

	/**
	 * Returns the new value of the data field.
	 * @return mixed
	 */
	public function getNewValue()
	{
		return $this->new_value;
	}

	/**
	 * Returns the value of the data field for safe SQL queries.
	 * @return mixed
	 */
	public function getSQLNewValue()
	{
		return addslashes($this->new_value);
	}

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

	function setDefaultValue($value = null)
	{
		if ($value === null) {
			$this->setNewValue($this->getDefaultValue());
		} else {
			$this->default_value = $value;
		}
	}

	function setSequence($name = null)
	{
		if ($name === null) {
			$this->sequence = null;
		} else {
			$this->sequence = "{$name}_seq";
		}
	}

	function getDefaultValue()
	{
		if ($this->sequence === null) {
			return $this->default_value;
		} else {
			$db =& P4A_DB::singleton($this->getDSN());
			return $db->nextSequenceId($this->sequence);
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
}