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
 * To contact the authors write to:									<br />
 * CreaLabs SNC														<br />
 * Via Medail, 32													<br />
 * 10144 Torino (Italy)												<br />
 * Website: {@link http://www.crealabs.it}							<br />
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
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_DB
{
	/**
	 * @var string
	 */
	protected $db_type = null;
	
	/**
	 * Connects to the configured database.
	 * Database is configured by setting P4A_DSN constant.
	 * @throws onDBConnectionError
	 */
	public static function singleton($DSN = "")
  	{
		//If DSN is not specified I use default connection
		if (!strlen($DSN) and defined("P4A_DSN")){
			$DSN = P4A_DSN;
		}

		if (strlen($DSN)) {
			$dbconn = base64_encode($DSN);
			$dbconn = "db" . str_replace(array('=','+','/'),'',$dbconn);
			global $$dbconn; //static $$ doesn't work
		}

		if(!isset($$dbconn) or $$dbconn == null) {
			if(strlen($DSN)) {
				$$dbconn = new p4a_db();
				$dsn_data = parse_url($DSN);
				if (!isset($dsn_data['host'])) $dsn_data['host'] = null;
				if (!isset($dsn_data['port'])) $dsn_data['port'] = null;
				if (!isset($dsn_data['user'])) $dsn_data['user'] = null;
				if (!isset($dsn_data['pass'])) $dsn_data['pass'] = null;
		
				if (!in_array($dsn_data['scheme'], array('mysql','oci','pgsql','sqlite'))) {
					p4a_error("db not supported");
				}
		
				$$dbconn->db_type = $dsn_data['scheme'];
				$driver = 'Zend_Db_Adapter_Pdo_' . ucfirst($dsn_data['scheme']);
				$connection_params = array(
					'host' => $dsn_data['host'],
					'port' => $dsn_data['port'],
					'username' => $dsn_data['user'],
					'password' => $dsn_data['pass'],
					'dbname' => substr($dsn_data['path'], 1)
				);
				
				require_once str_replace('_', '/', $driver) . '.php';
				$$dbconn->adapter = new $driver($connection_params);
    			$$dbconn->adapter->setFetchMode(Zend_Db::FETCH_ASSOC);
			} else {
				$$dbconn = null;
			}
		}
		return $$dbconn;
	}

	/**
	 * Connects to the configured database.
	 * Database is configured by setting P4A_DSN constant.
	 */
	private function connect($DSN = "")
	{
		return P4A_DB::singleton($DSN);
	}
	
	/**
	 * @param string $sequence_name
	 * @return integer
	 */
	public function nextSequenceId($sequence_name)
	{
		switch ($this->db_type) {
			case 'mysql':
				try {
					$this->adapter->insert($sequence_name, array());
					$id = $this->adapter->lastInsertId();
					$this->adapter->query("DELETE FROM $sequence_name WHERE id<$id");
				} catch (Exception $e) {
					$this->adapter->query("CREATE TABLE $sequence_name (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY)");
					$this->adapter->insert($sequence_name, array());
					$id = $this->adapter->lastInsertId();
					$this->adapter->query("DELETE FROM $sequence_name WHERE id<$id");
				}
				return $id;
			case 'sqlite':
				try {
					$this->adapter->insert($sequence_name, array('p4a'=>null));
					$id = $this->adapter->lastInsertId();
					$this->adapter->query("DELETE FROM $sequence_name WHERE id<$id");
				} catch (Exception $e) {
					$this->adapter->query("CREATE TABLE $sequence_name (id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, p4a CHAR)");
					$this->adapter->insert($sequence_name, array('p4a'=>null));
					$id = $this->adapter->lastInsertId();
					$this->adapter->query("DELETE FROM $sequence_name WHERE id<$id");
				}
				return $id;
			case 'pgsql':
				return $this->adapter->nextSequenceId($sequence_name);
			case 'oci':
				$sequence_name = strtoupper($sequence_name);
				try {
					$id = $this->adapter->nextSequenceId($sequence_name);
				} catch (Exception $e) {
					$this->adapter->query("CREATE SEQUENCE $sequence_name");
					$id = $this->adapter->nextSequenceId($sequence_name);
				}
				return $id;
		}
	}
	
	/**
	 * @return Zend_Db_Select
	 */
	public function select()
	{
		return $this->adapter->select();
	}

	public function beginTransaction()
	{
		$this->adapter->beginTransaction();
	}

	public function commit()
	{
		$this->adapter->commit();
	}
	
	public function rollback()
	{
		$this->adapter->rollback();
	}

	/**
	 * @param string $query
	 * @return array
	 * @deprecated 
	 */
	public function getAll($query)
	{
		return $this->adapter->fetchAll($query);
	}

	/**
	 * @param string $query
	 * @return array
	 * @deprecated 
	 */
	public function queryAll($query)
	{
		return $this->adapter->fetchAll($query);
	}
	
	/**
	 * @param string $query
	 * @return array
	 */
	public function fetchAll($query)
	{
		return $this->adapter->fetchAll($query);
	}

	/**
	 * @param string $query
	 * @return array
	 * @deprecated 
	 */
	public function getRow($query)
	{
		return $this->adapter->fetchRow($query);
	}

	/**
	 * @param string $query
	 * @return array
	 * @deprecated 
	 */
	public function queryRow($query)
	{
		return $this->adapter->fetchRow($query);
	}
	
	/**
	 * @param string $query
	 * @return array
	 */
	public function fetchRow($query)
	{
		return $this->adapter->fetchRow($query);
	}

	/**
	 * @param string $query
	 * @return array
	 * @deprecated 
	 */
	public function getCol($query)
	{
		return $this->adapter->fetchCol($query);
	}

	/**
	 * @param string $query
	 * @return array
	 * @deprecated 
	 */
	public function queryCol($query)
	{
		return $this->adapter->fetchCol($query);
	}
	
	/**
	 * @param string $query
	 * @return array
	 */
	public function fetchCol($query)
	{
		return $this->adapter->fetchCol($query);
	}

	/**
	 * @param string $query
	 * @return string
	 * @deprecated 
	 */
	public function getOne($query)
	{
		return $this->adapter->fetchOne($query);
	}

	/**
	 * @param string $query
	 * @return string
	 * @deprecated 
	 */
	public function queryOne($query)
	{
		return $this->adapter->fetchOne($query);
	}
	
	/**
	 * @param string $query
	 * @return string
	 */
	public function fetchOne($query)
	{
		return $this->adapter->fetchOne($query);
	}
	
	/**
	 * @param string $column_name
	 * @param string $search_pattern
	 * @return string
	 */
	public function getCaseInsensitiveLikeSQL($column_name, $search_pattern)
	{
		switch ($this->db_type) {
			case 'mysql':
			case 'sqlite':
				return "$column_name LIKE '$search_pattern'";
			case 'pgsql':
				return "$column_name ILIKE '$search_pattern'";
			case 'oci':
				return "UPPER($column_name) LIKE UPPER('$search_pattern')";
		}
	}
}