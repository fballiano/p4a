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
	 * Multivalue fields are used to store multi selection data.
	 * This widget stores data on external (detail) table so no
	 * fields are used in the master table.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_MULTIVALUE_FIELD extends P4A_FIELD 
	{
		/**
		 * The value of the primary key (current record, current mask).
		 * @access private
		 * @var mixed
		 */
		var $pk_value = NULL;
		
		/**
		 * The field name of the primary key on the TARGET TABLE.
		 * @access private
		 * @var string
		 */
		var $pk_field = NULL;
		
		/**
		 * The field name of the value field on the TARGET TABLE.
		 * @access private
		 * @var string
		 */
		var $value_field = NULL;
		
		/**
		 * The table where we'll store the data.
		 * @access private
		 * @var string
		 */
		var $target_table = NULL;
		
		/**
		 * Here we store the values.
		 * @access private
		 * @var array
		 */
		var $value = array();
		
		/**
		 * Here we store the values passed by web (between load and save).
		 * @access private
		 * @var array
		 */
		var $new_value = array();
		
		/**
		 * Class constructor.
		 * Sets the field as multiselect.
		 * @access private
		 */
		function &p4a_multivalue_field($name)
		{
			parent::p4a_field($name);
			$this->setType('multiselect');
			$this->setProperty('name', $this->getID() .'[]');
		}
		
		/**
		 * Sets the table where we'll store the data.
		 * @param string		The table
		 * @param string		The name of the pk field.
		 * @param mixed			The value field name.
		 * @access public
		 */
		function setTargetTable($table, $pk_field, $value_field)
		{
			if ($this->data !== NULL) {
				$this->target_table = $table;
				$this->pk_field = $pk_field;
				$this->value_field = $value_field;
			}else{
				ERROR('NO DATASOURCE SPECIFIED, USE SET_SOURCE BEFORE SET_TARGET_TABLE');
			}
			$this->load();
		}
		
		/**
		 * Sets the primary key field name of the target table.
		 * @param mixed			The primary key field name.
		 * @access public
		 */
		function setPkValue($value)
		{
			$this->pk_value = $value;
		}
		
		/**
		 * Loads the data.
		 * @access public
		 */
		function load()
		{
			$db =& P4A_DB::singleton();
			$this->value = array();
			if ($this->target_table)
			{
				$query  = "SELECT " . $this->value_field . ' ';
				$query .= "FROM " . $this->target_table . ' ';
				$query .= "WHERE " . $this->pk_field . "='". $this->pk_value ."'";
				$rs = $db->getCol($query);
				if (! DB::isError($rs))
				{
					$this->setValue($rs);
				}
				else
				{
					ERROR('MULTI VALUE LOAD ERROR');
				}
			}
		}
		
		/**
		 * Saves the data.
		 * @access public
		 */
		function update()
		{
			$db =& P4A_DB::singleton();
			
			$sQueryDelete  = " DELETE FROM " . $this->target_table;
			$sQueryDelete .= " WHERE "  . $this->pk_field . "= ?";
			$rQueryDelete = $db->prepare($sQueryDelete);
			$result = $db->execute($rQueryDelete, array($this->pk_value));

			if (! DB::isError($result))
			{
				$rQueryInsert = $db->prepare("INSERT INTO " . $this->target_table . "(" . $this->pk_field . ", " . $this->value_field .") VALUES (?,?)");
				$data = array();
				foreach($this->getNewValue() as $child_value) {
					$data[] = array($this->pk_value, $child_value);
				}
				$db->executeMultiple($rQueryInsert, $data);
			}
			else
			{
				ERROR('DELETE QUERY FAILED');
			}
		}
		
		/**
		 * Gets the output
		 * @access private
		 */
		function getAsMultiselect()
		{
			$header 			= '<INPUT TYPE="hidden" NAME="' . $this->getID() . '" VALUE="false"><SELECT class="border_box font_normal" multiple="multiple" ';
			$close_header 		= '>';
			$footer				= '</SELECT>';
			$header			   .= $this->composeStringActions() . $this->composeStringProperties() . $close_header;
			
			$external_data		= $this->data->getAll() ;
			$description_field	= $this->getSourceDescriptionField() ;
			
			foreach( $external_data as $key=>$current )
			{
				if (in_array($current[ $this->data->pk ], $this->getNewValue()))
				{
					$selected = "SELECTED";
				}
				else
				{
					$selected = "";
				}
				
				$sContent  = "<option $selected value='" . htmlspecialchars($current[$this->data->pk]) ."'>"; 
				$sContent .= htmlspecialchars($current[ $description_field ]);
				$sContent .= "</option>";

				$header .= $sContent;
			}
			 
			return $this->composeLabel() . '</td><td>'.  $header . $footer; 
		}
		
		/**
		 * Gets the output
		 * @access private
		 */
		function getAsLabel()
		{
			$header 			= '<DIV ';
			$close_header 		= '>';
			$footer				= '</DIV>';
			$header			   .= $this->composeStringActions() . $this->composeStringProperties() . $close_header;
			
			$external_data		= $this->data->getAll() ;
			$description_field	= $this->getSourceDescriptionField() ;
			
			foreach( $external_data as $key=>$current )
			{
				if (in_array($current[ $this->data->pk ], $this->getNewValue())) {
					$header .= htmlspecialchars($current[ $description_field ]) . '<br>';
				}
			}
			 
			return $this->composeLabel() . '</td><td>'.  $header . $footer;
		}
		
		/**
		 * Sets the number of displayed rows.
		 * @param integer			The number of displayed rows.
		 * @access public
		 */
		function setSize( $size )
		{
			$this->setProperty('size', $size);
		}
		
		/**
		 * Removes the size property.
		 * @access public
		 * @see set_size()
		 */
		function unsetSize()
		{
			$this->unsetProperty('size');
		}
		
		function setValue($value = NULL)
		{
			$this->value = $value;
			$this->setNewValue($value);
		}
		
		function getValue()
		{
			return $this->value;
		}
		
		function setNewValue($value = array())
		{
			if (!is_array($value)) {
				$value = array();
			}
			
			$this->new_value = $value;
		}
		
		function getNewValue()
		{
			return $this->new_value;
		}
	}
?>
