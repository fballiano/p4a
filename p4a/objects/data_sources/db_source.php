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
 * Viale dei Mughetti 13/A											<br>
 * 10151 Torino (Italy)												<br>
 * Tel.:   (+39) 011 735645											<br>
 * Fax:    (+39) 011 735645											<br>
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
	 * Data source through databases.
	 * Every istanced DB_SOURCE is the result set of a query.
	 * You can build your custom query in two ways. The first
	 * (commonly used) is to build the query token by token,
	 * so you'll add the from clause, then the where clause etc.
	 * The second one (to be used in very complex cases) is to
	 * define a query for every operation. So you can define one (or many)
	 * query for select, one (or many) for getting a single row,
	 * one (or many) for inserting, one (or many) for updating
	 * and one (or many) for deleting.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_DB_SOURCE extends P4A_DATA_SOURCE
	{
		/**
		 * Here we store the custom defined queries.
		 * @var array
		 * @access private
		 */
		var $defined_queries = array();
		
		/**
		 * Here we store every single part of the built queries.
		 * @var array
		 * @access private
		 */
		var $query_parts = array();
		
		/**
		 * The name of the sequence that determines the primary key.
		 * @var string
		 * @access private
		 */
		var $pk_sequence = NULL;
		
		/**
		 * Class constructor.
		 * @param string				Mnemonic identifier for the object.
		 * @access private
		 */
		function &p4a_db_source($name)
		{
			parent::p4a_data_source($name);
			
			$this->defined_queries['select']	= NULL;
			$this->defined_queries['insert']	= array();
			$this->defined_queries['update']	= array();
			$this->defined_queries['delete']	= array();
			
			$this->query_parts['select']		= NULL;
			$this->query_parts['from']			= NULL;
			$this->query_parts['table']			= NULL;
			$this->query_parts['table_alias']	= NULL;
			$this->query_parts['joins']			= array();
			$this->query_parts['where']			= NULL;
			$this->query_parts['filters']		= array();
			$this->query_parts['group']			= NULL;
			$this->query_parts['having']		= NULL;
			$this->query_parts['order']			= array();
			$this->query_parts['limit']			= NULL;
			$this->query_parts['offset']		= NULL;
			
		}
		
		/**
		 * Tells if insert operations can be done on this db_source.
		 * @access public
		 * @return boolean
		 */
		function isInsertPossible()
		{
			if( ( sizeof( $this->defined_queries['insert'] ) > 0 ) )
			{
				return true;
			}
			else
			{
				if( ( $this->defined_queries['select'] === NULL ) and
				    ( $this->query_parts['select'] === NULL ) and
				    ( $this->query_parts['from'] === NULL ) and
				    ( sizeof( $this->query_parts['joins'] ) == 0 )
				  )
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			
			return false;
		}
		
		/**
		 * Tells if update operations can be done on this db_source.
		 * @access public
		 * @return boolean
		 */
		function isUpdatePossible()
		{
			if( ( sizeof( $this->defined_queries['update'] ) > 0 ) )
			{
				return true;
			}
			else
			{
				if( ( $this->defined_queries['select'] === NULL ) and
				    ( $this->query_parts['select'] === NULL ) and
				    ( $this->query_parts['from'] === NULL ) and
				    ( sizeof( $this->query_parts['joins'] ) == 0 )
				  )
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			
			return false;
		}
		
		/**
		 * Tells if delete operations can be done on this db_source.
		 * @access public
		 * @return boolean
		 */
		function isDeletePossible()
		{
			if( ( sizeof( $this->defined_queries['delete'] ) > 0 ) )
			{
				return true;
			}
			else
			{
				if( ( $this->defined_queries['select'] === NULL ) and
				    ( $this->query_parts['select'] === NULL ) and
				    ( $this->query_parts['from'] === NULL ) and
				    ( sizeof( $this->query_parts['joins'] ) == 0 )
				  )
				{
						return true;
				}
				else
				{
					return false;
				}
			}
			
			return false;
		}
		
		/**
		 * Tells if the db_source has a limit or an offset in his generation query.
		 * @access public
		 * @return boolean
		 */
		function isLimited()
		{
			if( ( $this->query_parts['limit'] !== NULL ) or 
			    ( $this->query_parts['offset'] !== NULL )
			  )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		/**
		 * Tells if the db_source allows filtering operations.
		 * @access public
		 * @return boolean
		 */
		function isFilterable()
		{
			if( ( $this->defined_queries['select'] === NULL ) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		/**
		 * Tells if the db_source allows sorting operations.
		 * @access public
		 * @return boolean
		 */
		function isSortable()
		{
			if( ( $this->defined_queries['select'] === NULL ) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		/**
		 * Adds a query that will be executed on db_source load.
		 * The query is a normal SQL query but you MUST use
		 * [P4A_PK] to indicate to the parser to substitute
		 * [P4A_PK] with an IN clause. Eg:
		 * "SELECT * FROM tab_test WHERE ( cond1=val1 AND [P4A_PK])"
		 * will be parsed as "SELECT * FROM tab_test WHERE ( cond1=val1 AND pk=IN(pk_val1,pk_val2))".
		 * Using this method will deny the execution of ordering or filtering operations.
		 * @access public
		 * @param string				SQL query.
		 */
		function defineSelectQuery( $query, $table_alias = NULL )
		{
			$this->defined_queries['select'] = $query;
			if (! is_null($table_alias)) {
				$this->query_parts['table_alias'] = $table_alias;
			}
		}

		/**
		*
		* If set, return table alias with trailing period.
		* Otherwise, return empty string
		*
		* @return	string
		* @access	public
		*/
		function getTableAlias() {
			if (is_null($this->query_parts['table_alias'])) {
				return '';
			}
			return $this->query_parts['table_alias'] . '.';
		}
		
		/**
		 * Removes a query that will be executed on db_source load.
		 * @access public
		 */
		function dropSelectQuery()
		{
			$this->defined_queries['select'] = NULL;
		}
		
		/**
		 * Adds a query that will be executed on insert.
		 * The query is a normal SQL query but you can use
		 * [field] to indicate to the parser to substitute
		 * [field] with the value of field.
		 * @access public
		 * @param string				SQL query.
		 */
		function addInsertQuery( $query )
		{
			$this->defined_queries['insert'][] = $query;
		}
		
		/**
		 * Removes a query that will be executed on insert.
		 * @access public
		 * @param string				SQL query.
		 */
		function dropInsertQuery( $query )
		{
			$old = $this->defined_queries['insert'];
			$new = array();
			
			foreach( $old as $key=>$value )
			{
				if( $query != $value )
				{
					$new[] = $value;
				}
			}
			
			$this->defined_queries['insert'] = $new;
		}
		
		/**
		 * Adds a query that will be executed on update.
		 * The query is a normal SQL query but you can use
		 * [field] to indicate to the parser to substitute
		 * [field] with the value of field.
		 * @access public
		 * @param string				SQL query.
		 */
		function addUpdateQuery( $query )
		{
			$this->defined_queries['update'][] = $query;
		}
		
		/**
		 * Removes a query that will be executed on update.
		 * @access public
		 * @param string				SQL query.
		 */
		function dropUpdateQuery( $query )
		{
			$old = $this->defined_queries['update'];
			$new = array();
			
			foreach( $old as $key=>$value )
			{
				if( $query != $value )
				{
					$new[] = $value;
				}
			}
			
			$this->defined_queries['update'] = $new;
		}
		
		/**
		 * Adds a query that will be executed on delete.
		 * The query is a normal SQL query but you can use
		 * [field] to indicate to the parser to substitute
		 * [field] with the value of field.
		 * @access public
		 * @param string				SQL query.
		 */
		function addDeleteQuery( $query )
		{
			$this->defined_queries['delete'][] = $query;
		}
		
		/**
		 * Removes a query that will be executed on delete.
		 * @access public
		 * @param string				SQL query.
		 */
		function dropDeleteQuery( $query )
		{
			$old = $this->defined_queries['delete'];
			$new = array();
			
			foreach( $old as $key=>$value )
			{
				if( $query != $value )
				{
					$new[] = $value;
				}
			}
			
			$this->defined_queries['delete'] = $new;
		}
		
		/**
		 * Sets the select clause for the db_source load query.
		 * @access public
		 * @param string				Select clause.
		 */
		function setSelect( $select )
		{
			//$this->unsetFields();
			$this->query_parts['select'] = $select;
		}
		
		/**
		 * Removes the select clause for the db_source load query.
		 * @access public
		 */
		function unsetSelect()
		{
			$this->query_parts['select'] = NULL;
		}
		
		/**
		 * Sets the structure of the db source.
		 * This mean that you set all the fields that data source will manage.
		 * @param array		All the fields name in one array.
		 * @access public
		 * @see DATA_FIELD
		 */
		function setFields($fields)
		{
			//$this->unsetSelect();
			parent::setFields($fields);
		}
		
		/**
		 * Returns an array with all the fields types.
		 * @access public
		 * @param array		Fields array (if not given uses $this->getFields())
		 * @return array
		 */
		function getFieldsTypes( $fields = NULL )
		{
			$return = array();
			
			if( !is_array( $fields ) )
			{
				$fields = $this->getFields();
			}
			
			foreach($fields as $field) {
				$return[] = 'text';
			}
			
			return $return;
		}
 		
		/**
		 * Sets a "raw" from clause for the db_source load query.
		 * @access public
		 * @param string				From clause.
		 */
		function setFrom( $from )
		{
			$this->query_parts['from'] = $from;
		}

		/**
		 * Sets the from clause as a single table for the db_source load query.
		 * @access public
		 * @param string				The table name.
		 * @see set_from()
		 */
		function setTable( $table )
		{
			$this->query_parts['table'] = $table;
		}
		
		/**
		 * Removes the from clause for the db_source load query.
		 * @access public
		 */
		function unsetFrom()
		{
			$this->query_parts['from'] = NULL;
		}

		/**
		 * Alias for unset_from().
		 * @access public
		 * @see unset_from()
		 */
		function unsetTable()
		{
			$this->unsetFrom();
		}
		
		/**
		 * Adds a join clause to the db_source load query.
		 * @access public
		 * @param string		The join sql type.
		 * @param string		The join table.
		 * @param string		The join "on" clause.
		 */
		function addJoin( $type, $table, $clause )
		{
			$tmp = array();
			$tmp['table'] = $table;
			$tmp['clause'] = $clause;
			$tmp['type'] = strtoupper( $type );
			$this->query_parts['joins'][] = $tmp;
		}
		
		/**
		 * Wrapper for add_join('INNER', $table, $clause).
		 * @access public
		 * @param string		The join table.
		 * @param string		The join "on" clause.
		 */
		function addInnerJoin( $table, $clause )
		{
			$this->addJoin('INNER', $table, $clause);
		}
		
		/**
		 * Wrapper for add_join('LEFT', $table, $clause).
		 * @access public
		 * @param string		The join table.
		 * @param string		The join "on" clause.
		 */
		function addLeftJoin( $table, $clause )
		{
			$this->addJoin('LEFT', $table, $clause);
		}
		
		/**
		 * Wrapper for add_join('RIGHT', $table, $clause).
		 * @access public
		 * @param string		The join table.
		 * @param string		The join "on" clause.
		 */
		function addRightJoin( $table, $clause )
		{
			$this->addJoin('RIGHT', $table, $clause);
		}
		
		/**
		 * Drops joins from the db_source load query.
		 * If nothing is passed, drops every join.
		 * If type is passed, drops every join with passed type.
		 * If type and table are passed, drops every join with passed type on passed table.
		 * If type and table and clause are passed, drops every join with passed type on passed table with passed clause.
		 * @access public
		 * @param string		The join sql type.
		 * @param string		The join table.
		 * @param string		The join "on" clause.
		 */
		function dropJoin( $type = NULL, $table = NULL, $clause = NULL )
		{
			$type = strtoupper( $type );
			$old = $this->query_parts['joins'];
			$new = array();
			
			if( ( $type !== NULL ) and ( $table !== NULL ) and ( $clause !== NULL ) )
			{
				for( $i=0; $i<sizeof($old); $i++ )
				{
					if( ! ( ( $old[$i]['type'] == $type ) and ( $old[$i]['table'] == $table ) and ( $old[$i]['clause'] == $clause ) ) )
					{
						$new[] = $old[$i];
					}
				}
			}
			elseif( ( $type !== NULL ) and ( $table !== NULL ) )
			{
				for( $i=0; $i<sizeof($old); $i++ )
				{
					if( ! ( ( $old[$i]['type'] == $type ) and ( $old[$i]['table'] == $table ) ) )
					{
						$new[] = $old[$i];
					}
				}
			}
			elseif( $type !== NULL )
			{
				for( $i=0; $i<sizeof($old); $i++ )
				{
					if( $old[$i]['type'] != $type )
					{
						$new[] = $old[$i];
					}
				}
			}
			
			$this->query_parts['joins'] = $new;
		}

		/**
		 * Defines the "static" where clause for the db_source.
		 * @access public
		 */
		function setWhere( $where )
		{
			$this->query_parts['where'] = $where;
		}


		/**
		 * Returns the "static" where clause for the db_source.
		 * @access public
		 */
		function getWhere()
		{
			return $this->query_parts['where'];
		}
				
		/**
		 * Removes the "static" where clause for the db_source.
		 * @access public
		 */
		function unsetWhere()
		{
			$this->query_parts['where'] = NULL;
		}

		/**
		 * Adds a "dinamic" where clause for the db_source.
		 * @access public
		 * @param string		The field on wich we'll filter.
		 * @param string		The filtering operator.
		 * @param string		The searched value.
		 */
		function addFilter( $field, $operator, $value )
		{
			$tmp = array('field'=>$field, 'operator'=>$operator, 'value'=>$value);
			$this->query_parts['filters'][] = $tmp;
		}
		
		/**
		 * Drops a filter for the db_source.
		 * For the implementation method see drop_joins()
		 * @param string		The field on wich we'll filter.
		 * @param string		The filtering operator.
		 * @param string		The searched value.
		 * @see drop_joins()
		 */
		function dropFilter($field = NULL, $operator = NULL, $value = NULL )
		{
			$old = $this->query_parts['filters'];
			$new = array();
			
			if( ( $field !== NULL ) and ( $operator !== NULL ) and ( $value !== NULL ) )
			{
				for( $i=0; $i<sizeof($old); $i++ )
				{
					if( ! ( ( $old[$i]['field'] == $field ) and ( $old[$i]['operator'] == $operator ) and ( $old[$i]['value'] == $value ) ) )
					{
						$new[] = $old[$i];
					}
				}
			}
			elseif( ( $field !== NULL ) and ( $operator !== NULL ) )
			{
				for( $i=0; $i<sizeof($old); $i++ )
				{
					if( ! ( ( $old[$i]['field'] == $field ) and ( $old[$i]['operator'] == $operator ) ) )
					{
						$new[] = $old[$i];
					}
				}
			}
			elseif( $field !== NULL )
			{
				for( $i=0; $i<sizeof($old); $i++ )
				{
					if( $old[$i]['field'] != $field )
					{
						$new[] = $old[$i];
					}
				}
			}
			
			$this->query_parts['filters'] = $new;
		}
		
		/**
		 * Sets the "GROUP BY" clause.
		 * @access public
		 * @param string	The "GROUP BY" sql statement.
		 */
		function setGroup( $group )
		{
			$this->query_parts['group'] = $group;
		}
		
		/**
		 * Removes the "GROUP BY" clause.
		 * @access public
		 */
		function unsetGroup()
		{
			$this->query_parts['group'] = NULL;
		}
		
		/**
		 * Sets the "HAVING" clause.
		 * @access public
		 * @param string	The "HAVING" sql statement.
		 */
		function setHaving( $having )
		{
			$this->query_parts['having'] = $having;
		}
		
		/**
		 * Removes the "HAVING" clause.
		 * @access public
		 */
		function unsetHaving()
		{
			$this->query_parts['having'] = NULL;
		}

		/**
		 * Adds on "ORDER BY" clause.
		 * Order clauses are multiple, this function push at the end. 
		 * @access public
		 * @param string		The field on wich the db_source we'll order.
		 * @param string		The ordering mode (P4A_ORDER_ASCENDING|P4A_ORDER_DESCENDING)
		 */
		function addOrder( $field, $mode = P4A_ORDER_ASCENDING )
		{
			$tmp = array();
			$tmp['field'] = $field;
			$tmp['mode'] = $mode;
			
			array_push( $this->query_parts['order'], $tmp );
		}
		
		/**
		 * Adds on "ORDER BY" clause.
		 * Order clauses are multiple, this function push on the top. 
		 * @access public
		 * @param string		The field on wich the db_source we'll order.
		 * @param string		The ordering mode (P4A_ORDER_ASCENDING|P4A_ORDER_DESCENDING)
		 */
		function addMasterOrder( $field, $mode = P4A_ORDER_ASCENDING )
		{
			$tmp = array();
			$tmp['field'] = $field;
			$tmp['mode'] = $mode;
			
			array_unshift( $this->query_parts['order'], $tmp );
		}
		
		/**
		 * Removes the first ordering clause. 
		 * @access public
		 * @return array		The dropped order data.
		 */
		function dropMasterOrder()
		{
			return array_shift( $this->query_parts['order'] );
		}
		
		/**
		 * Removes all the ordering clauses on $field. 
		 * @access public
		 * @param string		The field.
		 * @return array		The dropped order data.
		 */
		function dropOrder( $field = NULL )
		{
			$old    = $this->query_parts['order'];
			$new    = array();
			$return = NULL;
			
			if( $field !== NULL )
			{
				for( $i=0; $i<sizeof($old); $i++ )
				{
					if( $old[$i]['field'] != $field )
					{
						$new[] = $old[$i];
					}
					else
					{
						$return = $old[$i];
					}
				}
			}
			
			$this->query_parts['order'] = $new;
			return $return;
		}
		
		/**
		 * Reverses all the ordering clauses on $field. 
		 * @access public
		 * @param string		The field.
		 */
		function reverseOrder( $field )
		{
			for( $i=0; $i<sizeof($this->query_parts['order']); $i++ )
			{
				if( $this->query_parts['order'][$i]['field'] == $field )
				{
					if( $this->query_parts['order'][$i]['mode'] == P4A_ORDER_ASCENDING )
					{
						$this->query_parts['order'][$i]['mode'] = P4A_ORDER_DESCENDING;
					}
					else
					{
						$this->query_parts['order'][$i]['mode'] = P4A_ORDER_ASCENDING;
					}
				}
			}
		}
		
		/**
		 * Reverses the first ordering clauses. 
		 * @access public
		 */
		function reverseMasterOrder()
		{
			if( $this->hasOrder() )
			{
				$master_order = array_shift( $this->query_parts['order'] );
				$field = $master_order['field'];
				$mode = $master_order['mode'];
				
				if( $mode == P4A_ORDER_ASCENDING )
				{
					$mode = P4A_ORDER_DESCENDING;
				}
				else
				{
					$mode = P4A_ORDER_ASCENDING;
				}
				
				$this->addMasterOrder( $field, $mode );
			}
		}
		
		/**
		 * Returns true if the db_source has at least one ordering clause.
		 * @access public
		 * @return boolean
		 */
		function hasOrder()
		{
			if( sizeof( $this->query_parts['order'] ) > 0 )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		/**
		 * Returns the order structure.
		 * @access public
		 * @return array
		 */
		function getOrder()
		{
			return $this->query_parts['order'];
		}
		
		/**
		 * Sets the limit clause.
		 * @access public
		 * @param integer		The limit.
		 */
		function setLimit( $limit )
		{
			$this->query_parts['limit'] = $limit;
		}
		
		/**
		 * Removes the limit clause.
		 * @access public
		 */
		function unsetLimit()
		{
			$this->query_parts['limit'] = NULL;
		}
		
		/**
		 * Sets the offset clause.
		 * @access public
		 * @param integer		The offset.
		 */
		function setOffset( $offset )
		{
			$this->query_parts['offset'] = $offset;
		}
		
		/**
		 * Removes the offset clause.
		 * @access public
		 */
		function unsetOffset()
		{
			$this->query_parts['offset'] = NULL;
		}
		
		/**
		 * Builds the select part of the db_source queries.
		 * @access private
		 */
		function composeSelectPart()
		{
			$query = 'SELECT ';
			$fields = $this->getFields();
			
			if( sizeof( $fields ) > 0 and ($this->query_parts['select'] === NULL) )
			{
				$query .= join( ',', $fields );
			}
			elseif($this->query_parts['select'])
			{
				$query .= $this->query_parts['select'];
			}else{
				$query .= ' * ';
			}
			
			return $query; 
		}
		
		/**
		 * Builds the from part of the db_source queries.
		 * @access private
		 */
		function composeFromPart()
		{
			$query = ' FROM ' ;
			
			if( $this->query_parts['table'] === NULL )
			{
				$query .= $this->query_parts['from'];
			}
			else
			{
				$query .= $this->query_parts['table'];
			}
			
			foreach( $this->query_parts['joins'] as $join )
			{
				$query .= ' ' . strtoupper( $join['type'] ) . ' JOIN ' . $join['table'] . ' ON ' . $join['clause'] ;
			}

			return $query; 
		}
		
		/**
		 * Builds the where part of the db_source queries.
		 * @access private
		 * @param mixed		(string|array) pk or array or pks that will be added in the IN part.
		 */
		function composeWherePart($pk = NULL)
		{
			$query = '';
			
			if ($this->query_parts['having']) {
				return;
			}
			
			if( ( $this->query_parts['where'] !== NULL ) or ( sizeof( $this->query_parts['filters'] ) > 0 ) or $pk)
			{
				$query .= ' WHERE ';
				$tmp = array();
				
				if( $this->query_parts['where'] !== NULL )
				{
					$tmp[] = '(' . $this->query_parts['where'] . ')' ;
				}
				
				foreach( $this->query_parts['filters'] as $filter )
				{
					if( $filter['value'] == P4A_NULL )
					{
						$tmp[] = '(' . $filter['field'] . ' ' .$filter['operator'] . " NULL" . ')';
					}
					else
					{
						if (is_array($filter['value'])) {
							foreach ($filter['value'] as $k => $v) {
								$filter['value'][$k] = "'" . addslashes($v) . "'";
							}
							$tmp[] = '(' . $filter['field'] . ' ' .$filter['operator'] . ' (' . implode(', ', $filter['value']) . '))';
						} else {
						$tmp[] = '(' . $filter['field'] . ' ' .$filter['operator'] . " '" . addslashes($filter['value']) . "'" . ')';
					}
				}
				}
				
				if ($pk){
					if (is_array($pk))
					{
						$sPk = '';
						foreach($pk as $pk_value){
							$sPk .= "'" . addslashes($pk_value) . "',";	
						}		
						$sPk = $this->getTableAlias() . $this->pk . " in (" . substr($sPk, 0, -1) . ")"; 		 			
					}else{
						$sPk = $this->getTableAlias() . $this->pk . "='" . addslashes($pk) . "'";
					}
					$tmp[] = $sPk; 
				}
				
				$query .= '(' . join( ' AND ', $tmp ) . ')';
			}
			return $query;
		}
		
		/**
		 * Builds the "group by" part of the db_source queries.
		 * @access private
		 */
		function composeGroupPart()
		{
			$query = '';
			if( $this->query_parts['group'] !== NULL )
			{
				$query .= ' GROUP BY ' . $this->query_parts['group'];
			}
			return $query;
		}
		
		/**
		 * Builds the having part of the db_source queries.
		 * @access private
		 */
		function composeHavingPart()
		{
			$query = '';
			if( $this->query_parts['having'] !== NULL )
			{
				$query .= ' HAVING ' . $this->query_parts['having'];
			}
			return $query;
		}
		
		/**
		 * Builds the "order by" part of the db_source queries.
		 * @access private
		 */
		function composeOrderPart()
		{
			$query = '';
			if( sizeof( $this->query_parts['order'] ) > 0 )
			{
				$query .= ' ORDER BY ';
				$tmp = array();
				
				foreach( $this->query_parts['order'] as $order )
				{
					$tmp[] = $order['field'] . ' ' . $order['mode'];
				}
				
				$query .= join( ',', $tmp );
			}
			return $query;
		}

		/**
		 * Builds the query used for db_source load.
		 * @access private
		 * @param mixed		(string|array) pk or array or pks that will be added in the IN part.
		 * @return string
		 */
		function composeSelectQuery($pk = NULL)
		{
			$query  = '';
			$query .= $this->composeSelectPart();
			$query .= $this->composeFromPart();
			$query .= $this->composeWherePart($pk);
			$query .= $this->composeGroupPart();
			$query .= $this->composeHavingPart();
			$query .= $this->composeOrderPart();
			return $query;
		}
		
		/**
		 * Builds the query used for inserting a record .
		 * @access private
		 * @param array		The row that will be inserted.
		 * @return string
		 */
		function composeInsertQuery($row)
		{
			$fields = '' ;
			$values = '' ;
			foreach( $row as $key=>$value )
			{
				$fields .= $key . ", ";
				
				if( strlen( $value ) > 0 ) {
					$values .= "'" . addslashes( $value ) . "', ";
				} else {
					$values .= "NULL, ";
				}
			}
			$fields = substr($fields, 0, -2);
			$values = substr($values, 0, -2);
			
			$query   = 'INSERT INTO ';
			
			if( $this->query_parts['from'] === NULL ) {
				$query 	.= $this->query_parts['table'];
			} else {
				$query 	.= $this->query_parts['from'];
			}
			
			$query	.= ' (';
			$query	.= $fields;
			$query	.= ') VALUES (';
			$query	.= $values;
			$query	.= ')';
			return $query;
		}
		
		/**
		 * Builds the query used for updating a record.
		 * @access private
		 * @param array		The new row.
		 * @return string
		 */
		function composeUpdateQuery($row)
		{
			$query   = 'UPDATE ';
			
			if( $this->query_parts['from'] === NULL ) {
				$query 	.= $this->query_parts['table'];
			} else {
				$query 	.= $this->query_parts['from'];
			}
			
			$query	.= ' SET ';
			
			foreach( $row as $key=>$value )
			{
				$query .= $key . "=" ;
				
				if( strlen( $value ) > 0 ) {
					$query .= "'" . addslashes( $value ) . "', ";
				} else {
					$query .= "NULL, ";
				}
			}
			
			$query  = substr($query, 0, -2);
			$query .= $this->composeWherePart( $row[ $this->pk ] );
			return $query;
		}
		
		/**
		 * Builds the query used for deleting a record.
		 * @access private
		 * @param mixed		(string|array) pk or array or pks that will be added in the IN part.
		 * @return string
		 */
		function composeDeleteQuery($pk)
		{
			
			$query   = 'DELETE ';
			$query 	.= $this->composeFromPart();
			$query	.= $this->composeWherePart($pk);
			return $query;
		}
		
		/**
		 * Goes in "new row" mode.
		 * This mean that we'll act on a temporary row that can be saved.
		 * @access public
		 */
		/*
		function newRow()
		{
			$fields = $this->getFields();
			$this->_data[-1] = array();
			foreach($fields as $field){
				if ($field === $this->pk and $this->pk_sequence !== NULL){
					$this->_data[-1][$field] = $this->nextPk();
				}else{
					$this->_data[-1][$field] = $this->getFieldDefaultValue($field);
				}
			}		
		}*/
		
		/**
		 * Inserts a row into the data source.
		 * If there's an insert query it will prevale.
		 * @param array			The new row.
		 * @access public
		 */
		function insertRow($row)
		{
			$db =& P4A_DB::singleton();
			
			if( !$this->isInsertPossible() )
			{
				ERROR('CANNOT INSERT: DEFINE INSERT QUERY');
			}
			
			//Default insert new row
			if ($row === NULL){
				$row = $this->new_row;
			}
			
			//Primary key
			if (!array_key_exists($this->pk, $row) or
				strlen(trim($row[$this->pk])) == 0)
			{
				$row[$this->pk] = $this->nextPk();
			}
	
			$pk = $row[$this->pk];
			
			//If PK already exist error
			if(array_key_exists($pk, $this->_map_pk)){
				ERROR('DUPLICATE PK');				
			}
			else
			{
				if (sizeof($this->defined_queries['insert']) == 0)
				{
					$query = $this->composeInsertQuery($row);
					$result = $db->query($query);
					if (DB::isError($result)) 
					{
						ERROR('INSERT FAILED', $result->getMessage() . '->' . $query);
					}
				}
				else
				{
					foreach( $this->defined_queries['insert'] as $defined_query )
					{
						$query = $this->_getParsedQuery($defined_query, $row);
						$result = $db->query($query);
						if (DB::isError($result)) 
						{
							ERROR('INSERT FAILED', $result->getMessage() . '->' . $query);
						}
					}
				}

				//Get Row Number
				if(is_array($this->_data) && count($this->_data)){
					$num_row = max(array_keys($this->_data)) + 1 ;
				} else {
					$num_row = 0;
				}
				
				$this->_map_pk[$pk] = $num_row;
				$this->_data[$num_row] = $row[$this->pk];
				return $num_row + 1;
			}
		}
		
		/**
		 * Updates a row's data.
		 * If there's an update query it will prevale.
		 * @param integer		The row number.
		 * @param array			The new row.
		 * @access public
		 */
		function updateRow($row_number, $row)
		{
			$db =& P4A_DB::singleton();
			if( !$this->isUpdatePossible() )
			{
				ERROR('CANNOT UPDATE: DEFINE UPDATE QUERY');
			}
			
			$pk = $this->_data[$row_number -1];
			$new_pk = $row[$this->pk];
			if (! array_key_exists($pk, $this->_map_pk)){
				ERROR('PK NOT FOUND');
			}elseif(($new_pk != $pk) AND array_key_exists($new_pk, $this->_map_pk)){
				ERROR('DUPLICATE PK');
			}
			
			if( sizeof( $this->defined_queries['update'] ) == 0 )
			{
				$query = $this->composeUpdateQuery($row);
				$result = $db->query($query);
				if (DB::isError($result)) 
				{
					ERROR('UPDATE FAILED', $result->getMessage() . '->' . $query);
				}
			}
			else
			{
				foreach( $this->defined_queries['update'] as $query )
				{
					$query = str_replace('[P4A_PK]', $this->getTableAlias() . $this->pk . "='" . addslashes( $row[ $this->pk ] ) . "'", $query);
					$query = $this->_getParsedQuery($query, $row);
					$result = $db->query($query);
					if (DB::isError($result)) 
					{
						ERROR('UPDATE FAILED', $result->getMessage() . '->' . $query);
					}
				}
			}
			
			if($new_pk != $pk)
			{
				$this->_map_pk[$new_pk] = $this->_map_pk[$pk];
				$this->_data[$row_number - 1] = $new_pk;  
				unset($this->_map_pk[$pk]);
			}
		}
		
		/**
		 * Deletes a row.
		 * If there's a delete query it will prevale.
		 * @param integer		The row number.
		 * @param array			The row. Useful when you have an "onDelete" action.
		 * @access public
		 */
		function deleteRow($row_number, $row=NULL)
		{
			$db =& P4A_DB::singleton();
			if( !$this->isDeletePossible() )
			{
				ERROR('CANNOT DELETE: DEFINE DELETE QUERY');
			}
			
			if ($row_number != -1)
			{
	    		//Row is used by user defined delete query
	    		$pk_key = $this->pk;
	    		$pk = $this->_data[$row_number -1];
	    		
	    		if (sizeof( $this->defined_queries['delete'] ) == 0)
	    		{
	    			$query = $this->composeDeleteQuery($pk);
		    		$result = $db->query($query);
		    		if(DB::isError($result))
		    		{
		    			ERROR('DELETE FAILED', $result->getMessage() . '->' . $query);
		    		}
	    		}
	    		else
	    		{
					foreach( $this->defined_queries['delete'] as $query )
					{
						$query = str_replace('[P4A_PK]', $this->getTableAlias() . $this->pk . "='" . addslashes( $row[ $this->pk ] ) . "'", $query);
						$query = $this->_getParsedQuery($query, $row);
						$result = $db->query($query);
						if (DB::isError($result)) 
						{
							ERROR('DELETE FAILED', $result->getMessage() . '->' . $query);
						}
					}
	    		}
	    		
    			unset($this->_map_pk[$pk]);
    			unset($this->_data[$row_number -1]);
    			
    			if (count($this->_data))
    			{
    				$max_pk = max(array_keys($this->_data));
    
	    			for($i = $row_number; $i<=$max_pk; $i++)
	    			{
	    				//data compatting
	    				//---------------------
	    				$old_row_number = $i;
	    				$new_row_number = $i-1;
	    				$pk_value = $this->_data[$old_row_number];
	    				
	    				//I copy old row in new row
	    				$this->_data[$new_row_number] = $this->_data[$old_row_number];
	    				//I destroy old row
	    				unset($this->_data[$old_row_number]);
	    				//I rimap pk to new row
	    				$this->_map_pk[$pk_value] = $new_row_number;
	    			}
    			}
			}
			else //new row
			{
				unset($this->_data[$row_number]);				
			}
		}
		
		/**
		 * In a master-detail environment sets the master row of the master data source.
		 * @param array		The row.
		 * @access public
		 */
		function setMasterRow($row)
		{
			$aParts = explode('=', $this->master_relation);
			$master_field = $aParts[0];
			$child_field = $aParts[1];
			
			//It deletes old master row filter
			if ($this->_master_row) {
				$old_filter_value = $this->getFieldDefaultValue($child_field);
				$this->dropFilter($child_field, '=', $old_filter_value);  
			}else{
				$this->dropFilter($child_field, 'is', P4A_NULL);  
			}
			
			$this->_master_row = $row;
			if ($this->_master_row) 
			{
				$filter_value = $this->_master_row[$master_field];
				$this->addFilter($child_field, '=', $filter_value);
			}else{
				$filter_value = P4A_NULL;
				$this->addFilter($child_field, 'is', $filter_value);		
			}
			$this->load();
			$this->setFieldDefaultValue($child_field, $filter_value);
		}
		
		/**
		 * Returns all tables and all fields in the result set.
		 * @param result_set		The result set.
		 * @access private
		 */
		function _getResultInfo($result)
		{
			$table_info = $result->tableInfo();
			if (DB::isError($table_info))
			{
				ERROR('NOT VALID RESULT');
			}
			else
			{
				$array_return['fields'] = array();
				$array_return['tables'] = array();
				$array_return['structure'] = array();
				
				foreach($table_info as $key=>$aValue)
				{
					if ($key == 0){
						$array_return['first_field'] = $aValue['name'];
					}
					$array_return['fields'][] = $aValue['name'];
					switch( $aValue['type'] )
					{
						case 'bit':
						case 'bool':
						case 'boolean':
						case 'tinyint':
							$aValue['type'] = 'boolean';
							break;
						case 'numeric':
						case 'real':
						case 'float':
							$aValue['type'] = 'float';
							break;
						case 'decimal':
							$aValue['type'] = 'decimal';
							break;
						case 'int':
						case 'int2':
						case 'int4':
						case 'int8':
						case 'long':
						case 'integer':
							$aValue['type'] = 'integer';
							break;
						case 'char':
						case 'string':
						case 'varchar':
						case 'varchar2':
						case 'text':
							$aValue['type'] = 'text';
							break;
						case 'date':
							$aValue['type'] = 'date';
							break;
						case 'time':
							$aValue['type'] = 'time';
							break;
						default:
							$aValue['type'] = 'text';
							break;
					}
					
					$array_return['structure'][$aValue['name']]['type'] = $aValue['type'];
					$array_return['structure'][$aValue['name']]['len'] = $aValue['len'];
					$array_return['structure'][$aValue['name']]['table'] = $aValue['table'];
					if (! in_array($aValue['table'], $array_return['tables'])){
						$array_return['tables'][] = $aValue['table'];
					}
				}
				return $array_return;
			}
		}

		/**
		 * Parses the query and substitute all {expression} occurences with the eval()ed expression.
		 * This also quote your value so you must not quote in your query.
		 * @param string		The SQL query.
		 * @return string
		 * @access private
		 */
		function _getEvalParsedQuery($query)
		{
			$aMatches = array();
			preg_match_all( "/\{(.+?)\}/", $query, $aMatches ) ;
			$num_mathches = count($aMatches[0]);
			for($i=0; $i<$num_mathches; $i++)
			{
				$eval_pattern = $aMatches[0][$i];
				$eval_expression = $aMatches[1][$i];
				eval('$replacement = ' . $eval_expression . ';');
				$query = str_replace($eval_pattern, "'" . addslashes($replacement) . "'", $query) ;
			}
			return $query;
		}
			
		/**
		 * Parses the query and substitute all [field] occurence with the correspondent value in row.
		 * This also quote your value so you must not quote in your query.
		 * @param string		The SQL query.
		 * @param array			The interested row.
		 * @return string
		 * @access private
		 */
		function _getParsedQuery($query, $new_row)
		{
			$query = $this->_getEvalParsedQuery($query);
			$aMatches = array();
			preg_match_all( "/\[(.+?)\]/", $query, $aMatches ) ;
			$num_mathches = count($aMatches[0]);
			for($i=0; $i<$num_mathches; $i++)
			{
				$field_pattern = $aMatches[0][$i];
				$field_name = $aMatches[1][$i];
				$replacement = is_null($new_row[$field_name]) ? 'NULL' : "'" . addslashes($new_row[$field_name]) . "'";
				$query = str_replace($field_pattern, $replacement, $query);
			}
			
			return $query;
		}
		
		/**
		 * Return the desidered row.
		 * @param integer				Row number.
		 * @access public
		 */
		function getRow($num_row)
		{
			$db =& P4A_DB::singleton();
			if ($num_row == -1){
				//return $this->getNewRow();
				return NULL;
			}elseif( $num_row == 0 or !array_key_exists(($num_row -1), $this->_data) ){
				return NULL;
			}else{
				$pk = $this->_data[$num_row - 1];
				
				if( $this->defined_queries['select'] === NULL )
				{
					$query = $this->composeSelectQuery($pk);
				}
				else
				{
					$query = str_replace( '[P4A_PK]', $this->getTableAlias() . $this->pk . "='" . addslashes($pk) . "'" , $this->defined_queries['select'] );
				}
				$query = $this->_getEvalParsedQuery($query);
				
				$result = $db->query($query);
				
				if (DB::isError($result)){
					ERROR('GET ROW FAILED', $result->getMessage() . '->' . $query);	
				} else {
					return $result->fetchRow();
				}
			}
		}
		
		/**
		 * Returns all the rows between $from and $to.
		 * It should be implemented with only 1 query...
		 * but for now (db compatibylity problems with mysql subqueries)
		 * we do 1 query for every row.
		 * @param integer		Row number to start.
		 * @param integer		Row number to end.
		 * @return string
		 * @access public
		 */
		function getRowsFromTo( $from, $to )
		{
			$db =& P4A_DB::singleton();
			
			$array_pk = array();
			$array_return = array();
			$i = $from;

			while ($i <= $to and array_key_exists(($i - 1), $this->_data))
			{
				$array_pk[] = $this->_data[$i-1];
				$i++;
			}
			
			if( $this->defined_queries['select'] === NULL )
			{
				$query = $this->composeSelectQuery($array_pk);
			}
			else
			{
				if (sizeof($array_pk) > 0) 
				{
    				$in = $this->getTableAlias() . $this->pk . " IN(";
    				foreach( $array_pk as $pk )
    				{
    					$in .= "'" . addslashes($pk) . "',";
    				}
    				$in = substr( $in, 0, -1 ) . ")" ;
				} 
				else 
				{
					$in = "1=0";
				}
				
				$query = str_replace( '[P4A_PK]', $in , $this->defined_queries['select'] );
			}
			$query = $this->_getEvalParsedQuery($query);
			
			$rs = $db->query($query);
			if (DB::isError($rs)){
				error('GET ROWS FROM TO FAILED');
			}
			
			while ($row = $rs->fetchRow())
			{
				$array_return[$this->_map_pk[$row[$this->pk]]+1] = $row;
			}
			ksort($array_return);

			return $array_return ;
		}
		
		/**
		 * Loads data into the db_source.
		 * This method effectively retrieve only the primary key's value
		 * and create internal maps.
		 * @access public
		 */
		function load()
		{
			$db =& P4A_DB::singleton();
			
			$this->_map_pk = array();
			$this->_data = array();
			
			if( $this->defined_queries['select'] === NULL )
			{
				//Information about table
				$query = $this->composeSelectQuery();
				$query = $this->_getEvalParsedQuery($query);
				$result_info = $db->limitQuery($query, 0, 1);
				
				if (DB::isError($result_info)) {
					ERROR('QUERY ERROR');
				}
				$table_info = $this->_getResultInfo($result_info);
				
				if (count($this->getFields()) == 0)
				{
					$this->setFields($table_info['fields']);
					
					foreach( $table_info['structure'] as $field=>$aField )
					{
						$this->setFieldType($field, $aField['type']);
					}
				}
		
				//If pk is not defined I use first field
				if ($this->pk === NULL) {
					$this->setPk($table_info['first_field']); 										
				}
				
				$pk = $this->pk;
				
				if( $this->query_parts['select'] === NULL )
				{
					$query = "SELECT $pk";
					$types = array($this->getFieldType($pk));
				}
				else
				{
					$query = "SELECT " . $this->query_parts['select'];
					$types = $this->getFieldsTypes();
				}
				
				$query .= $this->composeFromPart();
				$query .= $this->composeWherePart();
				$query .= $this->composeGroupPart();
				$query .= $this->composeHavingPart();
				$query .= $this->composeOrderPart();
				$query = $this->_getEvalParsedQuery($query);
				
				$limit = $this->query_parts['limit'];
				$offset = $this->query_parts['offset'];
				
				if (($limit !== NULL) and ($offset === NULL)){
					$offset = 0;
				}
	
				if ($limit === NULL and $offset === NULL){
					$result = $db->query($query);
				}else{
					$result = $db->limitQuery($query, $offset, $limit);
				}
			}
			else
			{
				$query = str_replace( '[P4A_PK]', '1', $this->defined_queries['select'] );
				$query = $this->_getEvalParsedQuery($query);
				$result = $db->query($query);
				if (DB::isError($result)) {
					ERROR('NOT VALID TABLE');
				}
				$table_info = $this->_getResultInfo($result);
				
				if ((count($this->getFields()) == 0) and ($this->query_parts['select'] === NULL)){
					$this->setFields($table_info['fields']);
				}
		
				//If pk is not defined I use first field
				//if ($pk !== NULL) {
				//	$this->setPk($pk);
				//}
				//else
				if ($this->pk === NULL){
					$this->setPk($table_info['first_field']); 										
				}
				
				$pk = $this->pk;
			}
			
			if (DB::isError($result))
			{
				ERROR('LOAD TABLE FAILED', $result->getMessage());
			}
			else
			{
				for ($i=0; $i<$result->numRows(); $i++)
				{
					$row = $result->fetchRow();
					$pk_value = $row[$pk];
					$this->_map_pk[$pk_value] = $i;
					$this->_data[$i] = $pk_value; 															
				}
			}
			
			foreach(array_keys($this->data_browsers) as $browser_name)
			{
    			if (! $this->data_browsers[$browser_name]->getFields()){
    				$this->data_browsers[$browser_name]->setFields($this->getFields());		
    			}
    			
    			$this->data_browsers[$browser_name]->moveFirst();
			}
		}
		
		/**
		 * Wrapper for set_pk(), set_table(), load().
		 * @access public
		 * @param string		The table.
		 * @param string		The pk field name.
		 */
		function loadFromTable($table, $pk = NULL)
		{
			if ($pk){
				$this->setPk($pk);
			}
			$this->setTable($table);
			$this->load();
		}
	}
	
?>
