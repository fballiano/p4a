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
	
	protected $like_operator = 'LIKE';
	
	/**
	 * Connects to the configured database.
	 * Database is configured by setting P4A_DSN constant.
	 * @throws onDBConnectionError
	 */
	public function &singleton($DSN = "")
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
				$$dbconn =& new p4a_db();
				$dsn_data = parse_url($DSN);
		
				if (!in_array($dsn_data['scheme'], array('mysql','oracle','pgsql','sqlite'))) {
					p4a_error("db not supported");
				}
				
				if ($dsn_data['scheme'] == 'pgsql') {
					$this->like_operator = 'ILIKE';
				}
		
				$$dbconn->db_type = $dsn_data['scheme'];
				$driver = 'Zend_Db_Adapter_Pdo_' . ucfirst($dsn_data['scheme']);
				$connection_params = array(
					'host' => $dsn_data['host'],
					'username' => $dsn_data['user'],
					'password' => $dsn_data['pass'],
					'dbname' => substr($dsn_data['path'], 1)
				);
				
				require_once str_replace('_', '/', $driver) . '.php';
				$$dbconn->adapter =& new $driver($connection_params);
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
	private function &connect($DSN = "")
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
				$create_sequence_sql = "CREATE TABLE $sequence_name (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY)";
			case 'sqlite':
				if (!isset($create_sequence_sql)) {
					$create_sequence_sql = "CREATE TABLE $sequence_name (id INTEGER NOT NULL AUTOINCREMENT PRIMARY KEY)";
				}
				try {
					$this->adapter->insert($sequence_name, array());
					$id = $this->adapter->lastInsertId();
					$this->adapter->query("DELETE FROM $sequence_name WHERE id<$id");
				} catch (Exception $e) {
					$this->adapter->query($create_sequence_sql);
					$this->adapter->insert($sequence_name, array());
					$id = $this->adapter->lastInsertId();
					$this->adapter->query("DELETE FROM $sequence_name WHERE id<$id");
				}
				return $id;
			case 'postgres':
			case 'oracle':
				return $this->adapter->nextSequenceId($sequence_name);
				break;
		}
	}
	
	/**
	 * @return Zend_Db_Select
	 */
	public function &select()
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
	 * @return string
	 */
	public function getLikeOperator()
	{
		return $this->like_operator;
	}
	
	/**
	 * @param string $value
	 */
	public function setLikeOperator($value)
	{
		$this->like_operator = $value;
	}
}