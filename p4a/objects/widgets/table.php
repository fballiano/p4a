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
	 * Tabular rapresentation of a data source.
	 * This is a complex widget that's used to allow users to navigate
	 * data sources and than (for example) edit a record or view details etc...
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Table extends P4A_Widget
	{
		/**
		 * Data source associated with the table.
		 * @var string
		 * @access private
		 */
		var $data = NULL;

		/**
		 * The gui widgets to allow table navigation.
		 * @var table_navigation_bar
		 * @access private
		 */
		var $navigation_bar = NULL;

		/**
		 * The table toolbar.
		 * @var toolbar
		 * @access private
		 */
		var $toolbar = NULL;

		/**
		 * All the table's rows.
		 * @var rows
		 * @access private
		 */
		var $rows = NULL;

		/**
		 * Decides if the table will show the "field's header" row.
		 * @var boolean
		 * @access private
		 */
		var $_show_headers = TRUE;

		/**
		 * Stores the table's structure (table_cols).
		 * @var array
		 * @access private
		 */
		var $cols = array();

		/**
		 * Defines if the table is rollable or not.
		 * @var boolean
		 * @access private
		 */
		var $rollable = TRUE;

		/**
		 * Decides if the table is collapsed or expanded.
		 * @var boolean
		 * @access private
		 */
		var $expand = TRUE;

		var $_title = "";

		var $_visible_cols = array();

		var $_auto_navigation_bar = TRUE;

		/**
		 * Class constructor.
		 * @param string				Mnemonic identifier for the object.
		 * @access private
		 */
		function &P4A_Table($name)
		{
			parent::P4A_Widget($name);
			$this->useTemplate('table');
			$this->setStyleProperty('border-collapse', 'collapse');
		}

		/**
		 * Sets the title for the table
		 * @param string		The title.
		 * @access public
		 * @see $title
		 */
		function setTitle($title)
		{
			/*if ($this->title_bar === NULL){
				$this->build("p4a_link",'title_bar');
			}
			$this->title_bar->setValue($title);
			$this->title_bar->addAction('onClick');
			$this->intercept($this->title_bar, 'onClick', 'rollup');
			*/
			$this->_title = $title;
		}

		function getTitle()
		{
			return $this->_title;
		}

		/**
		 * Sets the data source that the table will navigate.
		 * @param data_source		The data source.
		 * @access public
		 */
		function setSource(&$data_source)
		{
			unset($this->data);
			$this->data =& $data_source;

			$this->setDataStructure($this->data->fields->getNames());

			$this->build("p4a_table_rows", "rows");
			$this->addNavigationBar();
// 			if ($this->data->getNumPages() > 1){
// 				$this->addNavigationBar();
// 			}
		}

		/**
		 * Sets the table's structure (fields).
		 * @param array		All the fields.
		 * @access public
		 */
		function setDataStructure($array_fields)
		{
			$this->build('p4a_collection', 'cols');

			foreach($array_fields as $field) {
				$this->addCol($field);
			}
		}

		/**
		 * Adds a column to the data structure.
		 * @param string		Column name.
		 * @access public
		 */
		function addCol($column_name)
		{
			$this->cols->build("p4a_table_col",$column_name);
		}

		/**
		 * Returns the HTML rendered object.
		 * @access public
		 */
		function getAsString()
		{
			$this->clearDisplay();

			if (!$this->isVisible()) {
				return '';
			}

			$this->display('expand', $this->expand);
			$this->display('table_properties', $this->composeStringProperties());
			$width = $this->getStyleProperty("width");
			if (substr($width,-2) == "px") {
				$width = substr($width,0,-2);
				$width = (integer)$width -20;
				$width = "{$width}px";
			} else {
				$width = "95%";
			}
			$this->display('table_width', $width);

			if ($this->toolbar !== NULL and
				$this->toolbar->isVisible()) {
				$this->display('toolbar', $this->toolbar->getAsString());
			}

			if ($this->navigation_bar !== NULL) {
				if ($this->_auto_navigation_bar) {
					if ($this->data->getNumPages() > 1) {
						$this->display('navigation_bar', $this->navigation_bar);
					}
				} else {
					if ( $this->navigation_bar->isVisible()) {
						$this->display('navigation_bar', $this->navigation_bar);
					}
				}
			}

			$this->display("title", $this->getTitle());
			/*
			if ($this->title_bar !== NULL and
				$this->title_bar->isVisible())
			{
				if ($this->isRollable()) {
					$this->display('title_bar', $this->title_bar);
				} else {
					$this->display('title_bar', $this->title_bar->getValue());
				}
			}*/

			if($this->_show_headers)
			{
				$headers = array();
				$i = 0;

				$is_orderable	= false;
				$order_field	= NULL;
				$order_mode		= NULL;

				if ($this->data->getObjectType() == 'p4a_db_source') {
					$is_orderable = true;

					if ($this->data->hasOrder()) {
    					$order			= $this->data->getOrder();
    					$order_field	= $order[0][0];
    					$order_mode		= $order[0][1];
					}
				}

				$visible_cols = $this->getVisibleCols();
				foreach($visible_cols as $col_name) {
// 					if (! $this->cols->$col_name->isVisible()) {
// 						continue;
// 					}
					$col =& $this->cols->$col_name;
					$headers[$i]['properties']	= $col->composeStringProperties();
					$headers[$i]['value']		= $col->getLabel();
					$headers[$i]['order']		= NULL;

					if ($col->isOrderable()) {
						$headers[$i]['action'] = $col->composeStringActions();
					} else {
						$headers[$i]['action'] = "";
					}

					$data_field =& $this->data->fields->{$col->getName()};
					$field_name = $data_field->getName();
					$complete_field_name = $data_field->getTable() . "." . $data_field->getName();
					if ($is_orderable and ($order_field == $field_name or $order_field == $complete_field_name)) {
						 $headers[$i]['order'] = $order_mode;
					}

					$i++;
				}
				$this->display('headers', $headers);
			}

			if ($this->data->getNumRows() > 0){
				$this->display('table_rows_properties', $this->rows->composeStringProperties());
				$this->display('table_rows', $this->rows->getRows());
			}else{
				$this->display('table_rows_properties', NULL);
				$this->display('table_rows', NULL);
			}

			$visible_cols = $this->getVisibleCols();

			if( sizeof( $visible_cols ) > 0 ) {
				$this->display('table_cols', 'TRUE');
			} else {
				$this->display('table_cols', NULL);
			}

			return $this->fetchTemplate();
		}

		//todo
		function newToolbar($toolbar)
		{
			unset($this->toolbar);
			$this->build("P4A_Toolbar", "toolbar");
		}

		/**
		 * Adds a generic toolbar to the table.
		 * @access public
		 */

 		function addToolbar(&$toolbar)
		{
			unset($this->toolbar);
			$this->toolbar =& $toolbar;
		}

		/**
		 * Makes the toolbar visible.
		 * @access public
		 */
		function showToolbar()
		{
			if (is_object($this->toolbar)){
				$this->toolbar->setVisible();
			}else{
				P4A_Error('NO TOOLBAR');
			}
		}

		/**
		 * Makes the toolbar invisible.
		 * @access public
		 */
		function hideToolbar()
		{
			if (is_object($this->toolbar)){
				$this->toolbar->setInvisible();
			}else{
				P4A_Error('NO TOOLBAR');
			}
		}

		/**
		 * Adds the navigation bar to the table.
		 * @access public
		 */
		function addNavigationBar(){
			$this->build("p4a_table_navigation_bar", "navigation_bar");
		}

		/**
		 * Makes the navigation bar visible.
		 * @access public
		 */
		function showNavigationBar()
		{
			if ($this->navigation_bar === NULL ){
				$this->addNavigationBar();
			}
			$this->navigation_bar->setVisible();
			$this->_auto_navigation_bar = FALSE;
		}

		/**
		 * Makes the navigation bar hidden.
		 * @access public
		 */
		function hideNavigationBar()
		{
			if ($this->navigation_bar !== NULL ){
				$this->navigation_bar->setInvisible();
			}
			$this->_auto_navigation_bar = FALSE;
		}

		/**
		 * Sets the title bar visible
		 * @access public
		 */
		function showTitleBar(){
			if ($this->title_bar !== NULL){
				$this->setTitle($this->getName());
			}
			$this->title_bar->setVisible();
		}

		/**
		 * Sets the title bar hidden
		 * @access public
		 */
		function showHeaders()
		{
			$this->_show_headers = TRUE;
		}

		/**
		 * Sets the header row hidden
		 * @access public
		 * @see $_show_headers
		 */
		function hideHeaders()
		{
			$this->_show_headers = FALSE;
		}

		/**
		 * Returns true if the table is rollable
		 * @access public
		 */
		function isRollable()
		{
			return $this->rollable;
		}

		/**
		 * Enable table roolup when clicking on table title.
		 * @access public
		 */
		function enableRollup()
		{
			$this->rollable = true;
		}

		/**
		 * Disable table roolup when clicking on table title.
		 * @access public
		 */
		function disableRollup()
		{
			$this->rollable = false;
		}

		/**
		 * Sets the table collapsed if it was expanded or sets the table expanded if it was collapsed.
		 * @access public
		 */
		function rollup()
		{
			$this->expand = ! $this->expand;
		}

		/**
		 * Sets the table expanded.
		 * @access public
		 */
		function expand()
		{
			$this->expand = TRUE;
		}

		/**
		 * Sets the table collapsed.
		 * @access public
		 */
		function collapse()
		{
			$this->expand = FALSE;
		}

		/**
		 * Return an array with all columns id.
		 * @access public
		 * @return array
		 */
		function getCols()
		{
			return $this->cols->getNames();
		}

		/**
		 * Return an array with all id of visible columns.
		 * @access public
		 * @return array
		 */
		function getVisibleCols()
		{
			if ($this->_visible_cols) {
				return $this->_visible_cols;
			} else {
				return $this->getCols();
			}
		}

		/**
		 * Return an array with all id of invisible columns.
		 * @access public
		 * @return array
		 */
		function getInvisibleCols()
		{
			$return = array();

			while ($col =& $this->cols->nextItem()) {
				if (!$col->isVisible()) {
					$return[] = $col;
				}
			}

			return $return;
		}

		/**
		 * Sets all passed columns visible.
		 * If no array is given, than sets all columns visible.
		 * @access public
		 * @params array	Columns id in indexed array.
		 */
		function setVisibleCols( $cols = array() )
		{
			$this->setInvisibleCols();
			if (sizeof($cols) == 0) {
				$cols = $this->getCols();
			}
			$this->_visible_cols = $cols;

			foreach ($cols as $col) {
				if (isset($this->cols->$col)) {
					$this->cols->$col->setVisible();
				} else {
					P4A_Error("Unknow column $col");
				}
			}
		}

		/**
		 * Sets all passed columns invisible.
		 * If no array is given, than sets all columns invisible.
		 * @access public
		 * @params array	Columns id in indexed array.
		 */
		function setInvisibleCols( $cols = array() )
		{
			if (sizeof( $cols ) == 0) {
				$cols = $this->getCols();
			}

			foreach ($cols as $col) {
				$this->cols->$col->setInvisible();
				if ($pos = array_search($col, $this->_visible_cols)) {
					unset($this->_visible_cols[$pos]);
				}
			}
		}
	}

	/**
	 * Keeps the data for a single table column.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Table_Col extends P4A_Widget
	{
		/**
		 * Keeps the header string.
		 * @var string
		 * @access private
		 */
		var $header = NULL;

		/**
		 * Data source for the field.
		 * @var data_source
		 * @access private
		 */
		var $data = NULL;

		/**
		 * The data source member that contains the values for this field.
		 * @var string
		 * @access private
		 */
		var $data_value_field = NULL ;

		/**
		 * The data source member that contains the descriptions for this field.
		 * @var string
		 * @access private
		 */
		var $data_description_field	= NULL ;

		/**
		 * Tells if the fields content is formatted or not.
		 * @var boolean
		 * @access private
		 */
		var $formatted = true;

		/**
		 * The formatter class name for the data field.
		 * @var string
		 * @access private
		 */
		var $formatter_name = NULL;

		/**
		 * The format name for the data field.
		 * @var string
		 * @access private
		 */
		var $format_name = NULL;

		/**
		 * Tells if the table can order by this col
		 * @var boolean
		 * @access private
		 */
		var $orderable = true;

		/**
		 * Class constructor.
		 * @param string		Mnemonic identifier for the object.
		 * @access private
		 */
		function &P4A_Table_Col($name)
		{
			parent::P4A_Widget($name);
			$this->setDefaultLabel();
			$this->addAction('onClick');
		}

		/**
		 * Sets the header for the column.
		 * @param string		The header
		 * @access public
		 * @see $header
		 */
		function setHeader($header)
		{
			$this->setLabel($header);
		}

		/**
		 * Returns the header for the column.
		 * @access public
		 * @see $header
		 */
		function getHeader()
		{
			return $this->getLabel();
		}

		/**
		 * If we use fields like combo box we have to set a data source.
		 * By default we'll take the data source primary key as value field
		 * and the first fiels (not pk) as description.
		 * @param data_source		The data source.
		 * @access public
		 */
		function setSource(&$data_source)
		{
			unset( $this->data ) ;
			$this->data =& $data_source;
			$pk = $this->data->getPk();

			if ($pk !== NULL) {
				if( $this->getSourceValueField() === NULL ) {
					if (is_array($pk)) {
						P4A_Error("FEATURE NOT IMPLEMENTED: Multiple pk on table col.");
					} else {
						$this->setSourceValueField($pk) ;
					}
				}

				if ($this->getSourceDescriptionField() === NULL) {
					$source_value = $this->getSourceValueField();
					$names = $this->data->fields->getNames();
					foreach ($names as $name) {
						if ($name != $source_value) {
							$this->setSourceDescriptionField($name) ;
							break;
						}
					}
				}
			}
		}

		/**
		 * Sets what data source member is the keeper of the field's value.
		 * @param string		The name of the data source member.
		 * @access public
		 */
		function setSourceValueField( $name )
		{
			// No controls if $name exists...
			// too many controls may be too performance expensive.
			$this->data_value_field = $name ;
		}

		/**
		 * Sets what data source member is the keeper of the field's description.
		 * @param string		The name of the data source member.
		 * @access public
		 */
		function setSourceDescriptionField( $name )
		{
			// No controls if $name exists...
			// too many controls may be too performance expensive
			$this->data_description_field = $name ;
		}

		/**
		 * Returns the name of the data source member that keeps the field's value.
		 * @return string
		 * @access public
		 */
		function getSourceValueField()
		{
			return $this->data_value_field ;
		}

		/**
		 * Returns the name of the data source member that keeps the field's description.
		 * @return string
		 * @access public
		 */
		function getSourceDescriptionField()
		{
			return $this->data_description_field ;
		}

		/**
		 * Translate the value with the description
		 * @param string		The value to translate
		 * @return string
		 * @access public
		 */
		function getDescription($value)
		{
			if (!$this->data) {
				return $value;
			} else {
				$row = $this->data->getPkRow($value);
				if (is_array($row)){
					return $row[$this->data_description_field];
				}else{
					return $value;
				}
			}
		}

		/**
		 * Returns true if a formatting format for the field has been set.
		 * @access public
		 * @return boolean
		 */
		function isFormatted()
		{
			return $this->formatted;
		}

		/**
		 * Sets the column as formatted.
		 * @access public
		 */
		function setFormatted( $value = true )
		{
			$this->formatted = $value;
		}

		/**
		 * Sets the column as not formatted.
		 * @access public
		 */
		function unsetFormatted()
		{
			$this->formatted = false;
		}

		/**
		 * Sets the formatter and format for the column.
		 * This also turns formatting on.<br>
		 * Eg: set_format('numbers', 'decimal')
		 * @access public
		 * @param string	The formatter name.
		 * @param string	The format name.
		 */
		function setFormat( $formatter_name, $format_name )
		{
			$this->formatter_name = $formatter_name;
			$this->format_name = $format_name;
			$this->setFormatted();
		}

		/**
		 * Removes formatting options and turns formatting off.
		 * @access public
		 */
		function unsetFormat()
		{
			$this->formatter_name = NULL;
			$this->format_name = NULL;
			$this->unsetFormatted();
		}

		/**
		 * Sets the ability to order by this column
		 * @access public
		 */
		function setOrderable($orderable = true)
		{
			$this->orderable = $orderable;
		}

		/**
		 * Tell if the column is orderable or not
		 * @access public
		 */
		function isOrderable()
		{
			return $this->orderable;
		}

		function onClick()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());
			$parent =& $p4a->getObject($parent->getParentID());

			if ($parent->data->getObjectType() == 'p4a_db_source') {

				$data_field =& $parent->data->fields->{$this->getName()};
				$field_name = $data_field->getName();
				$complete_field_name = $data_field->getTable() . "." . $data_field->getName();

				$new_order = P4A_ORDER_ASCENDING;

				if ($parent->data->hasOrder()) {
					$order = $parent->data->getOrder();
					$order = $order[0];

					if ($order[0] == $complete_field_name or $order[0] == $field_name) {
    					if ($order[1] == P4A_ORDER_ASCENDING) {
    						$new_order = P4A_ORDER_DESCENDING;
    					}
					} else {
						$new_order = P4A_ORDER_ASCENDING;
					}
				}
				if ($data_field->getAliasOf()){
					$order = $data_field->getName();
				}else{
					$order = $data_field->getTable() . "." . $data_field->getName();
				}
				$parent->data->setOrder($order, $new_order);
				$parent->data->updateRowPosition();
			}
		}
	}

	/**
	 * Keeps all the data for all the rows.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Table_Rows extends P4A_Widget
	{
		/**
		 * Class constructor.
		 * By default we add an onClick action.
		 * @param string		Mnemonic identifier for the object
		 * @access private
		 */
		function &P4A_Table_Rows($name = 'rows')
		{
			parent::P4A_Widget($name);
			$this->addAction('onClick');
		}

		/**
		 * Sets the max height for the data rows.
		 * This is done adding a scrollbar to the table body.
		 * @param integer		The desidered height.
		 * @param string		Measure unit
		 * @access public
		 */
		function setMaxHeight( $height, $unit = 'px' )
		{
			$this->setStyleProperty('max-height', $height . $unit);
		}

		/**
		 * Retrive data for the current page.
		 * @return array
		 * @access private
		 */
		function getRows()
		{
			$p4a =& P4A::singleton();

			$aReturn = array();
			$parent =& $p4a->getObject($this->getParentID());

			$rows = $parent->data->page(null, false);

			$aCols = $parent->getVisibleCols();

			$limit = $parent->data->getPageLimit();
			$num_page = $parent->data->getNumPage();
			$offset = $parent->data->getOffset();

			$this->actionHandler('beforeDisplay', array(&$rows));

			$i = 0;
			foreach($rows as $row_number=>$row)
			{
				$j = 0;
				$action = $this->composeStringActions($row_number);
				if ($row_number + $offset + 1 == $parent->data->getRowNumber()) {
					$aReturn[$i]['row']['active'] = TRUE;
				} else {
					$aReturn[$i]['row']['active'] = FALSE;
				}

				foreach($aCols as $col_name) {
// 					if (! $parent->cols->$col_name->isVisible()) {
// 						continue;
// 					}
					$aReturn[$i]['cells'][$j]['action'] = $action;

					if ($parent->cols->$col_name->data) {
						$aReturn[$i]['cells'][$j]['value'] = $parent->cols->$col_name->getDescription($row[$col_name]);
					} elseif ($parent->cols->$col_name->isFormatted()) {
						if (($parent->cols->$col_name->formatter_name === NULL) and ($parent->cols->$col_name->format_name === NULL)) {
							$aReturn[$i]['cells'][$j]['value'] = $p4a->i18n->autoFormat($row[$col_name], $parent->data->fields->$col_name->getType());
						} else {
							$aReturn[$i]['cells'][$j]['value'] = $p4a->i18n->{$parent->cols->$col_name->formatter_name}->format( $row[$col_name], $p4a->i18n->{$parent->cols->$col_name->formatter_name}->getFormat($parent->cols->$col_name->format_name));
						}
					} else {
						$aReturn[$i]['cells'][$j]['value'] = $row[$col_name];
					}

					$aReturn[$i]['cells'][$j]['row_number'] = $i;
					$j++;
				}
				$i++;
			}

			return $aReturn;
		}

		/**
		 * onClick action handler for the row.
		 * We move pointer to the clicked row.
		 * @param array		All passed params.
		 * @access public
		 */
		function onClick($aParams)
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());

			if ($this->actionHandler('beforeClick', $aParams) == ABORT) return ABORT;

			if ($parent->data->row($aParams[0] + $parent->data->getOffset() + 1) == ABORT) return ABORT;

			if ($this->actionHandler('afterClick', $aParams) == ABORT) return ABORT;
		}
	}

	/**
	 * The gui widgets to navigate the table.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Table_Navigation_Bar extends P4A_Frame
	{
		/**
		 * Class constructor.
		 * @param string		Mnemonic identifier for the object.
		 * @access private
		 */
		function &P4A_Table_Navigation_Bar()
		{
			$p4a =& P4A::singleton();

			parent::P4A_Frame("table_navigation_bar");
			$this->build("p4a_collection","buttons");
			$this->setStyleProperty("float", "none");

			$this->addButton('button_go', 'apply', 'right');
			$this->buttons->button_go->addAction('onClick');
			$this->intercept($this->buttons->button_go, 'onClick', 'goOnClick');

			$field_num_page =& $this->buttons->build('p4a_field', 'field_num_page');
			$field_num_page->label->setStyleProperty("text-align", "right");
			$field_num_page->label->setWidth(80);
			$this->buttons->field_num_page->setWidth(30);
			$this->buttons->field_num_page->addAction('onReturnPress');
			$this->intercept($this->buttons->field_num_page, 'onReturnPress', 'goOnClick');
			$this->anchorRight($field_num_page);

			$current_page =& $this->buttons->build('p4a_label', 'current_page');
			$this->anchorLeft($current_page);

			if ($p4a->isHandheld()) {
				$this->addButton('button_first');
				$this->buttons->button_first->setLabel("<<");
				$this->addButton('button_prev');
				$this->buttons->button_prev->setLabel("<");
				$this->addButton('button_next');
				$this->buttons->button_next->setLabel(">");
				$this->addButton('button_last');
				$this->buttons->button_last->setLabel(">>");

				$this->buttons->button_go->setVisible(false);
				$this->buttons->field_num_page->setVisible(false);
			} else {
				$this->addButton('button_last', 'last', 'right');
				$this->addButton('button_next', 'next', 'right');
				$this->addButton('button_prev', 'prev', 'right');
				$this->addButton('button_first', 'first', 'right');
			}

			$this->buttons->button_last->addAction('onClick');
			$this->intercept($this->buttons->button_last, 'onClick', 'lastOnClick');

			$this->buttons->button_next->addAction('onClick');
			$this->intercept($this->buttons->button_next, 'onClick', 'nextOnClick');

			$this->buttons->button_prev->addAction('onClick');
			$this->intercept($this->buttons->button_prev, 'onClick', 'prevOnClick');

			$this->buttons->button_first->addAction('onClick');
			$this->intercept($this->buttons->button_first, 'onClick', 'firstOnClick');
		}

		function addButton($button_name, $icon = null, $float = "left")
		{
			$button =& $this->buttons->build("p4a_button", $button_name);

			if (strlen($icon)>0) {
				$button->setIcon($icon);
				$button->setSize(16);
			}

			$anchor = "anchor" . $float;
			$this->$anchor($button, "2px");
			return $button;
		}

		function getAsString()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());

			$this->buttons->field_num_page->setLabel( $p4a->i18n->messages->get('go_to_page'));
			$this->buttons->field_num_page->setNewValue($parent->data->getNumPage());
			$num_pages = $parent->data->getNumPages();

			if ($num_pages < 1) {
				$num_pages = 1;
			}

			$current_page  = $p4a->i18n->messages->get('current_page');
			$current_page .= ' ';
			$current_page .= $parent->data->getNumPage();
			$current_page .= ' ';
			$current_page .= $p4a->i18n->messages->get('of_pages');
			$current_page .= ' ';
			$current_page .= $num_pages;
			$current_page .= ' ';
			$this->buttons->current_page->setValue($current_page);
			return parent::getAsString();
		}

		/**
		 * Action handler for "next" button click.
		 * @access public
		 */
		function nextOnClick()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());
			$parent->data->nextPage();
		}

		/**
		 * Action handler for "previous" button click.
		 * @access public
		 */
		function prevOnClick()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());
			$parent->data->prevPage();
		}

		/**
		 * Action handler for "first" button click.
		 * @access public
		 */
		function firstOnClick()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());
			$parent->data->firstPage();
		}

		/**
		 * Action handler for "last" button click.
		 * @access public
		 */
		function lastOnClick()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());
			$parent->data->lastPage();
		}

		/**
		 * Action handler for "go" button click.
		 * @access public
		 */
		function goOnClick()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->getParentID());
			$parent->data->page($this->buttons->field_num_page->getNewValue());
		}

	}
?>
