<?php

class P4A_DB  
{
	/**
	* Connects to the configured database.
	* Database is configured by setting P4A_DSN constant.
	* @access public
	* @throws onDBConnectionError
	*/
	function &singleton()
  	{
		static $db;
		if(!isset($db) or $db == null){
			if(defined("P4A_DSN")){
				$db = DB::connect(P4A_DSN);
    			if (DB::isError($db)){
					$e = new P4A_ERROR('Database connection failed.', $this, $db);
    				if ($this->errorHandler('onDBConnectionError', $e) !== PROCEED ){
    					die();
    				}
    			}
    			$db->setFetchMode(DB_FETCHMODE_ASSOC);
			}else{
				$db = null;
			}
		}
		return $db;
	}
	
	/**
	* Connects to the configured database.
	* Database is configured by setting P4A_DSN constant.
	* @access private
	* @throws onDBConnectionError
	*/	
	function &connect()
	{
		return P4A_DB::singleton();
	}
	
	/**
	* Close the connection to the database.
	* @access private
	*/	
	function close()
	{
		$db = P4A_DB::singleton();
		if(is_object($db)){
			$db->disconnect();
		}
	}
}
?>